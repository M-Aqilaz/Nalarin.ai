<?php

namespace App\Services\Learning;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class MaterialTextExtractor
{
    public function extractFromUpload(UploadedFile $file, ?int $maxOcrPages = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        $text = match ($extension) {
            'txt', 'md', 'markdown', 'csv', 'json', 'xml' => $this->extractPlainText($path),
            'html', 'htm' => $this->extractHtmlText($path),
            'docx' => $this->extractDocxText($path),
            'pptx' => $this->extractPptxText($path),
            'xlsx' => $this->extractXlsxText($path),
            'pdf' => $this->extractPdfText($path),
            'png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp' => $this->extractImageOcr($path),
            default => '',
        };

        $text = $this->normalize($text);
        $usedOcr = false;
        $warning = null;

        $needsOcr = $this->shouldRunOcr($extension, $text);

        if ($needsOcr) {
            $ocrText = match ($extension) {
                'pdf' => $this->extractPdfOcr($path, $maxOcrPages),
                'png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp' => $this->extractImageOcr($path),
                default => '',
            };

            $ocrText = $this->normalize($ocrText);

            if ($ocrText !== '') {
                $text = $ocrText;
                $usedOcr = true;
            }
        }

        if ($needsOcr && ! $usedOcr && $this->isProbablyScan($extension)) {
            $text = '';
        }

        if ($text === '') {
            $warning = match ($extension) {
                'pdf' => 'Isi PDF belum dapat dibaca. Coba gunakan file yang lebih jelas atau tempelkan teks materi secara langsung.',
                'png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp' => 'Tulisan pada gambar belum dapat dibaca. Coba gunakan gambar yang lebih jelas atau tempelkan teks materi.',
                'doc', 'ppt', 'xls', 'odt', 'odp', 'ods', 'rtf' => 'Format file ini belum didukung. Simpan ulang sebagai PDF, DOCX, PPTX, atau XLSX, lalu coba lagi.',
                default => 'Isi file belum dapat dibaca. Kamu dapat menempelkan teks materi secara langsung.',
            };
        }

        return [
            'text' => $text,
            'warning' => $warning,
            'used_ocr' => $usedOcr,
            'engine' => $usedOcr ? 'tesseract' : $this->engineFor($extension, $text),
        ];
    }

    private function extractPlainText(string $path): string
    {
        return (string) file_get_contents($path);
    }

    private function extractHtmlText(string $path): string
    {
        return strip_tags((string) file_get_contents($path));
    }

    private function extractDocxText(string $path): string
    {
        return $this->extractZipXmlText($path, [
            'word/document.xml',
            'word/header*.xml',
            'word/footer*.xml',
            'word/footnotes.xml',
            'word/endnotes.xml',
        ], ['w:t']);
    }

    private function extractPptxText(string $path): string
    {
        return $this->extractZipXmlText($path, [
            'ppt/slides/slide*.xml',
            'ppt/notesSlides/notesSlide*.xml',
        ], ['a:t']);
    }

    private function extractXlsxText(string $path): string
    {
        return $this->extractZipXmlText($path, [
            'xl/sharedStrings.xml',
            'xl/worksheets/sheet*.xml',
        ], ['t', 'v']);
    }

    private function extractZipXmlText(string $path, array $patterns, array $textTags): string
    {
        if (! class_exists(\ZipArchive::class)) {
            return '';
        }

        $zip = new \ZipArchive();

        if ($zip->open($path) !== true) {
            return '';
        }

        $chunks = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = str_replace('\\', '/', (string) $zip->getNameIndex($index));

            if (! collect($patterns)->contains(fn (string $pattern): bool => Str::is($pattern, $name))) {
                continue;
            }

            $xml = $zip->getFromIndex($index) ?: '';

            if ($xml !== '') {
                $chunks[] = $this->extractTextFromOfficeXml($xml, $textTags);
            }
        }

        $zip->close();

        return implode("\n", $chunks);
    }

    private function extractTextFromOfficeXml(string $xml, array $textTags): string
    {
        $xml = str_replace([
            '</w:p>',
            '</w:tr>',
            '</a:p>',
            '</a:tr>',
            '</si>',
            '</row>',
            '</c>',
        ], "\n", $xml);

        $segments = [];

        foreach ($textTags as $tag) {
            $escapedTag = str_contains($tag, ':')
                ? preg_quote($tag, '/')
                : '(?:[A-Za-z0-9_]+:)?'.preg_quote($tag, '/');

            if (preg_match_all('/<'.$escapedTag.'(?:\s[^>]*)?>(.*?)<\/'.$escapedTag.'>/s', $xml, $matches)) {
                foreach ($matches[1] as $match) {
                    $value = trim(html_entity_decode(strip_tags($match), ENT_QUOTES | ENT_XML1, 'UTF-8'));

                    if ($value !== '') {
                        $segments[] = $value;
                    }
                }
            }
        }

        if ($segments !== []) {
            return implode(' ', $segments);
        }

        return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function extractPdfText(string $path): string
    {
        $binaryText = $this->runCommand([
            (string) config('services.ocr.pdftotext_path', 'pdftotext'),
            '-layout',
            '-enc',
            'UTF-8',
            $path,
            '-',
        ]);

        if ($this->normalize($binaryText) !== '') {
            return $binaryText;
        }

        return $this->extractPdfTextFallback($path);
    }

    private function extractPdfTextFallback(string $path): string
    {
        $content = (string) file_get_contents($path);

        if ($content === '') {
            return '';
        }

        $streams = [$content];

        if (preg_match_all('/stream\s*(.*?)\s*endstream/s', $content, $matches)) {
            foreach ($matches[1] as $stream) {
                $streams[] = $stream;
                $decoded = @gzuncompress($stream);

                if ($decoded !== false) {
                    $streams[] = $decoded;
                    continue;
                }

                $decoded = @gzdecode($stream);

                if ($decoded !== false) {
                    $streams[] = $decoded;
                }
            }
        }

        $chunks = [];

        foreach ($streams as $stream) {
            if (preg_match_all('/\((.*?)(?<!\\\\)\)\s*Tj/s', $stream, $matches)) {
                foreach ($matches[1] as $match) {
                    $chunks[] = $this->decodePdfString($match);
                }
            }

            if (preg_match_all('/\[(.*?)\]\s*TJ/s', $stream, $matches)) {
                foreach ($matches[1] as $group) {
                    if (preg_match_all('/\((.*?)(?<!\\\\)\)/s', $group, $nestedMatches)) {
                        foreach ($nestedMatches[1] as $match) {
                            $chunks[] = $this->decodePdfString($match);
                        }
                    }
                }
            }
        }

        return implode(' ', $chunks);
    }

    private function extractPdfOcr(string $path, ?int $maxPages): string
    {
        if (! (bool) config('services.ocr.enabled', true)) {
            return '';
        }

        $tempDir = storage_path('framework/cache/ocr/'.Str::uuid());
        File::ensureDirectoryExists($tempDir);

        try {
            $prefix = $tempDir.DIRECTORY_SEPARATOR.'page';
            $command = [
                (string) config('services.ocr.pdftoppm_path', 'pdftoppm'),
                '-png',
                '-r',
                '200',
                '-f',
                '1',
            ];

            if ($maxPages !== null && $maxPages > 0) {
                $command[] = '-l';
                $command[] = (string) $maxPages;
            }

            $command[] = $path;
            $command[] = $prefix;

            $this->runCommand($command);

            $pages = glob($tempDir.DIRECTORY_SEPARATOR.'page-*.png') ?: [];
            sort($pages);

            return implode("\n\n", array_map(fn (string $image): string => $this->extractImageOcr($image), $pages));
        } finally {
            File::deleteDirectory($tempDir);
        }
    }

    private function extractImageOcr(string $path): string
    {
        if (! (bool) config('services.ocr.enabled', true)) {
            return '';
        }

        return $this->runCommand([
            (string) config('services.ocr.tesseract_path', 'tesseract'),
            $path,
            'stdout',
            '-l',
            (string) config('services.ocr.languages', 'ind+eng'),
            '--psm',
            '3',
        ]);
    }

    private function shouldRunOcr(string $extension, string $text): bool
    {
        if (! in_array($extension, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp'], true)) {
            return false;
        }

        return mb_strlen($this->normalize($text)) < (int) config('services.ocr.min_text_length', 120);
    }

    private function isProbablyScan(string $extension): bool
    {
        return in_array($extension, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp'], true);
    }

    private function runCommand(array $command): string
    {
        try {
            $process = new Process($command);
            $process->setTimeout((int) config('services.ocr.timeout', 120));
            $process->run();

            return $process->isSuccessful() ? $process->getOutput() : '';
        } catch (\Throwable) {
            return '';
        }
    }

    private function engineFor(string $extension, string $text): ?string
    {
        if ($text === '') {
            return null;
        }

        return match ($extension) {
            'pdf' => 'pdftotext',
            default => 'native',
        };
    }

    private function decodePdfString(string $value): string
    {
        $decoded = preg_replace_callback('/\\\\([0-7]{1,3})/', fn (array $matches): string => chr(octdec($matches[1])), $value) ?? $value;

        return str_replace(['\\n', '\\r', '\\t', '\\b', '\\f', '\\(', '\\)', '\\\\'], ["\n", "\r", "\t", '', '', '(', ')', '\\'], $decoded);
    }

    private function normalize(string $text): string
    {
        $text = $this->ensureUtf8($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        $text = preg_replace('/[^\S\n]+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function ensureUtf8(string $text): string
    {
        if ($text === '') {
            return '';
        }

        if (function_exists('mb_check_encoding') && ! mb_check_encoding($text, 'UTF-8')) {
            $converted = @mb_convert_encoding($text, 'UTF-8', 'UTF-8, Windows-1252, ISO-8859-1');

            if (is_string($converted) && $converted !== '') {
                $text = $converted;
            }
        }

        if (function_exists('iconv')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $text);

            if (is_string($cleaned)) {
                $text = $cleaned;
            }
        }

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;
    }
}
