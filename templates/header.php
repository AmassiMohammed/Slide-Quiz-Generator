<?php
$pageTitle = ($pageTitle ?? 'QuizGen') . ' – QuizGen';

// Basis-URL automatisch erkennen – funktioniert auf jeder Seite
$basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__DIR__));
$baseUrl  = rtrim($basePath, '/\\');
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="../public/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/style.css">
</head>
<body>

<nav>
  <a class="nav-logo" href="<?= $baseUrl ?>/public/index.php">Quiz<span>Gen</span></a>
  <ul class="nav-links">
    <li><a href="<?= $baseUrl ?>/public/index.php" <?= basename($_SERVER['PHP_SELF']) === 'index.php'  ? 'class="active"' : '' ?>>Neu</a></li>
  </ul>
</nav>