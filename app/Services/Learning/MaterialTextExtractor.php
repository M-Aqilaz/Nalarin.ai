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
                'pdf' => 'PDF ini belum bisa dibaca sebagai teks. Untuk PDF scan, install Poppler pdftoppm dan Tesseract, lalu upload ulang.',
                'png', 'jpg', 'jpeg', 'webp', 'tif', 'tiff', 'bmp' => 'Gambar ini belum bisa dibaca. Install Tesseract dan pastikan OCR_LANGUAGES sudah sesuai.',
                'doc', 'ppt', 'xls', 'odt', 'odp', 'ods', 'rtf' => 'Format Office lama belum didukung di mode ringan. Convert ke PDF, DOCX, PPTX, atau XLSX lalu upload ulang.',
                default => 'Isi file belum bisa dibaca otomatis. Tempelkan teks materi agar fitur belajar bisa diproses.',
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
        return $this->extractZipXmlText($path, 'word/document.xml');
    }

    private function extractPptxText(string $path): string
    {
        return $this->extractZipXmlText($path, 'ppt/slides/slide*.xml');
    }

    private function extractXlsxText(string $path): string
    {
        return $this->extractZipXmlText($path, 'xl/sharedStrings.xml');
    }

    private function extractZipXmlText(string $path, string $pattern): string
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
            $name = $zip->getNameIndex($index);

            if (! Str::is($pattern, $name)) {
                continue;
            }

            $xml = $zip->getFromIndex($index) ?: '';

            if ($xml !== '') {
                $chunks[] = html_entity_decode(strip_tags(str_replace(['</w:p>', '</a:p>', '</si>', '</row>'], "\n", $xml)), ENT_QUOTES | ENT_XML1, 'UTF-8');
            }
        }

        $zip->close();

        return implode("\n", $chunks);
    }

    private function extractPdfText(string $path): string
    {
        $binaryText = $this->runCommand([
            (string) config('services.ocr.pdftotext_path', 'pdftotext'),
            '-layout',
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
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        $text = preg_replace('/[^\S\n]+/', ' ', $text) ?? $text;

        return trim($text);
    }
}
