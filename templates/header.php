<?php
    $title = ($pageTitle ?? 'QuizGen').' - QuizGen';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com"crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
</head>
<body>

    <nav>
        <a class="nav-logo" href="/index.php">Quiz<span>Gen</span></a>
        <ul class="nav-links">
            <li><a href="/index.php" <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'class="active"' : '' ?>>Neu</a></li>
            <li><a href="/quiz.php" <?= basename($_SERVER['PHP_SELF']) === 'quiz.php' ? 'class="active"' : '' ?>>Quiz</a></li>
        </ul>
    </nav>
