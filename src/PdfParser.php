<?php

namespace QuizGen;

class PdfParser
{
    private const MAX_CHARS = 12000;

    /**
     * Extrahiert Text aus einer hochgeladenen PDF-Datei.
     * Versucht zuerst pdftotext (Poppler), dann einen PHP-Fallback.
     *
     * @param  string $filePath  Absoluter Pfad zur temporären Datei
     * @return string            Extrahierter Text
     * @throws \RuntimeException Wenn kein Text extrahiert werden kann
     */
    public function extract(string $filePath): string
    {
        // 1) pdftotext (Poppler) – beste Qualität
        if ($this->hasPdfToText()) {
            $text = $this->extractWithPdfToText($filePath);
            if (!empty(trim($text))) {
                return $this->clean($text);
            }
        }

        // 2) smalot/pdfparser (Composer-Paket)
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            $text = $this->extractWithSmalot($filePath);
            if (!empty(trim($text))) {
                return $this->clean($text);
            }
        }

        // 3) Roher Byte-Fallback
        $text = $this->extractRaw($filePath);
        if (!empty(trim($text))) {
            return $this->clean($text);
        }

        throw new \RuntimeException('PDF konnte nicht gelesen werden. Bitte eine andere Datei versuchen.');
    }

    // ── PRIVATE METHODS ──────────────────────────────────────

    private function hasPdfToText(): bool
    {
        return !empty(shell_exec('which pdftotext 2>/dev/null'))
            || !empty(shell_exec('where pdftotext 2>NUL'));
    }

    private function extractWithPdfToText(string $path): string
    {
        $safe = escapeshellarg($path);
        return (string) shell_exec("pdftotext {$safe} - 2>/dev/null");
    }

    private function extractWithSmalot(string $path): string
    {
        try {
            $parser   = new \Smalot\PdfParser\Parser();
            $pdf      = $parser->parseFile($path);
            return $pdf->getText();
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function extractRaw(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) return '';

        preg_match_all('/BT(.*?)ET/s', $content, $matches);
        $text = '';
        foreach ($matches[1] as $block) {
            preg_match_all('/\((.*?)\)\s*Tj/s', $block, $tj);
            $text .= implode(' ', $tj[1]) . "\n";
        }
        return preg_replace('/[^\x20-\x7E\x80-\xFF\n]/', ' ', $text);
    }

    private function clean(string $text): string
    {
        // Leerzeichen normalisieren
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        // Auf Zeichenlimit kürzen
        return mb_substr(trim($text), 0, self::MAX_CHARS);
    }
}