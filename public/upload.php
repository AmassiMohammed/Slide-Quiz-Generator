<?php
session_start();

// .env einlesen – funktioniert garantiert
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            putenv(trim($line));
        }
    }
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/PdfParser.php';
require_once __DIR__ . '/../src/AiService.php';
require_once __DIR__ . '/../src/QuizGenerator.php';

use QuizGen\PdfParser;
use QuizGen\AiService;
use QuizGen\QuizGenerator;

// ── KONFIGURATION ─────────────────────────────────────────
define('ANTHROPIC_API_KEY', getenv('API_KEY') ?: '');
define('MAX_FILE_SIZE',  10 * 1024 * 1024);        // 10 MB
define('UPLOAD_DIR',     __DIR__ . '/../uploads/');

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

// ── INPUT VALIDIERUNG ─────────────────────────────────────
$inputType     = $_POST['input_type']     ?? 'text';
$questionCount = (int)($_POST['question_count'] ?? 10);
$difficulty    = $_POST['difficulty']     ?? 'medium';

if (!in_array($questionCount, [10, 20], true)) $questionCount = 10;
if (!in_array($difficulty, ['easy', 'medium', 'hard'], true)) $difficulty = 'medium';

// ── TEXT ERMITTELN ────────────────────────────────────────
$text = '';

try {
    if ($inputType === 'pdf') {

        // Datei prüfen
        if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Fehler beim Hochladen. Bitte erneut versuchen.');
        }

        $file = $_FILES['pdf_file'];

        if ($file['size'] > MAX_FILE_SIZE) {
            throw new \RuntimeException('Datei zu groß (max. 10 MB).');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            throw new \RuntimeException('Nur PDF-Dateien sind erlaubt.');
        }

        // Datei sicher speichern
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
        $safeName  = uniqid('upload_', true) . '.pdf';
        $savedPath = UPLOAD_DIR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $savedPath)) {
            throw new \RuntimeException('Datei konnte nicht gespeichert werden.');
        }

        // Text extrahieren
        $parser = new PdfParser();
        $text   = $parser->extract($savedPath);

        // Temporäre Datei löschen
        @unlink($savedPath);

    } else {
        $text = trim($_POST['text_input'] ?? '');
        if (mb_strlen($text) < 100) {
            throw new \RuntimeException('Bitte mindestens 100 Zeichen Text eingeben.');
        }
    }

    // ── QUIZ GENERIEREN ───────────────────────────────────
    $ai        = new AiService(ANTHROPIC_API_KEY);
    $generator = new QuizGenerator($ai);
    $questions = $generator->generate($text, $questionCount, $difficulty);

    // ── IN SESSION SPEICHERN ──────────────────────────────
    $_SESSION['quiz'] = [
        'questions'  => $questions,
        'total'      => count($questions),
        'difficulty' => $difficulty,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    $_SESSION['current_q'] = 0;
    $_SESSION['answers']   = [];

    header('Location: quiz.php'); exit;

} catch (\RuntimeException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php'); exit;
}