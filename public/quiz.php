<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// ── GUARDS ────────────────────────────────────────────────
if (empty($_SESSION['quiz'])) {
    header('Location: index.php'); exit;
}

$quiz    = $_SESSION['quiz'];
$total   = $quiz['total'];
$current = $_SESSION['current_q'] ?? 0;

// ── ANTWORT VERARBEITEN ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $qIndex = (int)($_POST['q_index'] ?? -1);
    $answer = strtoupper(trim($_POST['answer']));

    if ($qIndex === $current && in_array($answer, ['A','B','C','D'], true)) {
        $_SESSION['answers'][$current] = $answer;
        $_SESSION['current_q']++;
    }

    if ($_SESSION['current_q'] >= $total) {
        // ── ERGEBNIS BERECHNEN ────────────────────────────
        $correct = 0;
        $details = [];

        foreach ($quiz['questions'] as $i => $q) {
            $given     = $_SESSION['answers'][$i] ?? null;
            $isCorrect = ($given === $q['correct']);
            if ($isCorrect) $correct++;
            $details[] = [
                'question'    => $q['question'],
                'options'     => $q['options'],
                'given'       => $given,
                'correct'     => $q['correct'],
                'explanation' => $q['explanation'],
                'is_correct'  => $isCorrect,
            ];
        }

        $score = round(($correct / $total) * 100);

        // ── IN DB SPEICHERN ───────────────────────────────
        $dbConfig = require __DIR__ . '/../config/database.php';
        $db = @new mysqli(
            $dbConfig['host'],
            $dbConfig['user'],
            $dbConfig['password'],
            $dbConfig['dbname']
        );

        if (!$db->connect_error) {
            $db->set_charset('utf8mb4');
            $stmt = $db->prepare(
                "INSERT INTO results (score, correct, total, difficulty, created_at)
                 VALUES (?, ?, ?, ?, NOW())"
            );
            if ($stmt) {
                $stmt->bind_param('iiis', $score, $correct, $total, $quiz['difficulty']);
                $stmt->execute();
            }
            $db->close();
        }

        // In Session für result.php speichern
        $_SESSION['result'] = [
            'score'      => $score,
            'correct'    => $correct,
            'total'      => $total,
            'difficulty' => $quiz['difficulty'],
            'details'    => $details,
        ];

        unset($_SESSION['quiz'], $_SESSION['answers'], $_SESSION['current_q']);
        header('Location: result.php'); exit;
    }

    header('Location: quiz.php'); exit;
}

// ── AKTUELLE FRAGE ────────────────────────────────────────
if ($current >= $total) {
    header('Location: result.php'); exit;
}

$q       = $quiz['questions'][$current];
$letters = ['A', 'B', 'C', 'D'];
$diff    = ['easy' => '🟢 Einfach', 'medium' => '🟡 Mittel', 'hard' => '🔴 Schwer'][$quiz['difficulty']];
$pct     = round(($current / $total) * 100);
$pageTitle = "Frage " . ($current + 1) . " / {$total}";

require_once __DIR__ . '/../templates/header.php';
?>

<div class="container">

  <!-- QUIZ HEADER -->
  <div class="quiz-header">
    <div class="quiz-meta">
      <span class="badge badge-yellow"><?= $diff ?></span>
      <span class="badge badge-green"><?= $total ?> Fragen</span>
      <a href="index.php" class="btn btn-ghost" style="padding: 0.3rem 0.9rem; font-size: 0.82rem; margin-left: auto;">
        ✕ Abbrechen
      </a>
    </div>
    <div class="progress-bar-wrap">
      <div class="progress-bar-fill" style="width: <?= $pct ?>%"></div>
    </div>
    <div class="question-counter">Frage <?= $current + 1 ?> von <?= $total ?></div>
  </div>

  <!-- QUESTION CARD -->
  <div class="card question-card">

    <p class="question-text"><?= $q['question'] /* already escaped in QuizGenerator */ ?></p>

    <form action="quiz.php" method="POST" id="answerForm">
      <input type="hidden" name="q_index" value="<?= $current ?>">

      <ul class="options-list">
        <?php foreach ($letters as $letter):
          if (empty($q['options'][$letter])) continue;
        ?>
        <li class="option-item" id="opt-<?= $letter ?>">
          <input type="radio" name="answer" id="ans-<?= $letter ?>"
                 value="<?= $letter ?>" onchange="selectOption('<?= $letter ?>')">
          <label class="option-label" for="ans-<?= $letter ?>">
            <span class="option-letter"><?= $letter ?></span>
            <?= $q['options'][$letter] ?>
          </label>
        </li>
        <?php endforeach; ?>
      </ul>

      <button type="submit" class="btn btn-primary" id="nextBtn" disabled>
        <?= ($current + 1 < $total) ? 'Nächste Frage →' : '🎯 Ergebnis anzeigen' ?>
      </button>
    </form>
  </div>

  <!-- FORTSCHRITTS-DOTS -->
  <div class="progress-dots">
    <?php for ($i = 0; $i < $total; $i++):
      $cls = $i === $current ? 'dot-current' : (isset($_SESSION['answers'][$i]) ? 'dot-done' : '');
    ?>
    <div class="progress-dot <?= $cls ?>"></div>
    <?php endfor; ?>
  </div>

</div>

<script>
function selectOption(letter) {
  document.getElementById('nextBtn').disabled = false;
  document.querySelectorAll('.option-item').forEach(el => el.style.opacity = '0.55');
  document.getElementById('opt-' + letter).style.opacity = '1';
}

// Keyboard shortcuts: A/B/C/D oder 1/2/3/4, Enter zum Bestätigen
document.addEventListener('keydown', function(e) {
  const map = { a:'A', b:'B', c:'C', d:'D', 1:'A', 2:'B', 3:'C', 4:'D' };
  const l   = map[e.key?.toLowerCase()];
  if (l) {
    const r = document.getElementById('ans-' + l);
    if (r) { r.checked = true; selectOption(l); }
  }
  if (e.key === 'Enter' && !document.getElementById('nextBtn').disabled) {
    document.getElementById('answerForm').submit();
  }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>