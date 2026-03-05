<?php
    use Smalot\PdfParser\Parser;

    class PdfParser{
        public function extractText($filePath){
            $parser = new Parser();

            $pdf = $parser -> parseFile($filePath);

            $text = $pdf -> getText();

            return $text;
        }
    }