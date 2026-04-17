<?php

namespace App\Services\Learning;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MaterialTextExtractor
{
    public function extractFromUpload(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $text = match ($extension) {
            'txt', 'md', 'markdown', 'csv', 'json', 'xml' => $this->extractPlainText($file->getRealPath()),
            'html', 'htm' => $this->extractHtmlText($file->getRealPath()),
            'docx' => $this->extractDocxText($file->getRealPath()),
            'pdf' => $this->extractPdfText($file->getRealPath()),
            default => '',
        };

        $text = $this->normalize($text);

        if ($text !== '') {
            return [
                'text' => $text,
                'warning' => null,
            ];
        }

        $warning = match ($extension) {
            'pdf' => 'PDF ini belum bisa dibaca otomatis dengan baik. Jika isinya tidak muncul, tempelkan teks materi di kolom teks.',
            'doc', 'ppt', 'pptx', 'xls', 'xlsx' => 'Format file ini belum didukung untuk ekstraksi otomatis. Tempelkan teks materi agar flashcard dan kuis bisa dibuat.',
            default => 'Isi file belum bisa dibaca otomatis. Tempelkan teks materi agar fitur belajar bisa diproses.',
        };

        return [
            'text' => '',
            'warning' => $warning,
        ];
    }

    private function extractPlainText(string $path): string
    {
        return (string) file_get_contents($path);
    }

    private function extractHtmlText(string $path): string
    {
        $html = (string) file_get_contents($path);

        return strip_tags($html);
    }

    private function extractDocxText(string $path): string
    {
        if (! class_exists(\ZipArchive::class)) {
            return '';
        }

        $zip = new \ZipArchive();

        if ($zip->open($path) !== true) {
            return '';
        }

        $documentXml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        if ($documentXml === '') {
            return '';
        }

        $text = strip_tags(str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $documentXml));

        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function extractPdfText(string $path): string
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

    private function decodePdfString(string $value): string
    {
        $decoded = preg_replace_callback('/\\\\([0-7]{1,3})/', function (array $matches): string {
            return chr(octdec($matches[1]));
        }, $value) ?? $value;

        $decoded = str_replace(
            ['\\n', '\\r', '\\t', '\\b', '\\f', '\\(', '\\)', '\\\\'],
            ["\n", "\r", "\t", '', '', '(', ')', '\\'],
            $decoded,
        );

        return $decoded;
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
