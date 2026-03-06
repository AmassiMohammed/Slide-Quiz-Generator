<?php
session_start();

if (empty($_SESSION['result'])) {
    header('Location: index.php'); exit;
}

$r          = $_SESSION['result'];
$score      = $r['score'];
$correct    = $r['correct'];
$total      = $r['total'];
$wrong      = $total - $correct;
$difficulty = $r['difficulty'];
$details    = $r['details'];

$diff = ['easy' => 'Einfach', 'medium' => 'Mittel', 'hard' => 'Schwer'][$difficulty];

[$emoji, $msg] = match (true) {
    $score >= 90 => ['🏆', 'Ausgezeichnet!'],
    $score >= 70 => ['🎯', 'Gut gemacht!'],
    $score >= 50 => ['📚', 'Weiter lernen!'],
    default      => ['💪', 'Nicht aufgeben!'],
};

// SVG Ring
$circumference = 2 * M_PI * 54; // r=54 → ≈ 339.3
$dash          = $circumference * ($score / 100);

// Session aufräumen
unset($_SESSION['result']);

$pageTitle = 'Ergebnis';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="container">

  <!-- SCORE RING -->
  <div class="result-hero">
    <div class="score-ring">
      <svg width="140" height="140" viewBox="0 0 140 140">
        <circle class="ring-bg"   cx="70" cy="70" r="54"/>
        <circle class="ring-fill" cx="70" cy="70" r="54"
                id="ringFill"
                stroke-dasharray="0 <?= $circumference ?>"
                stroke-dashoffset="0"/>
      </svg>
      <div class="score-label">
        <span class="pct" id="scoreNum">0%</span>
        <span class="total"><?= $correct ?>/<?= $total ?></span>
      </div>
    </div>

    <div style="font-size:2.5rem; margin-bottom:0.5rem;"><?= $emoji ?></div>
    <h1 class="result-title"><?= $msg ?></h1>
    <p class="text-muted">Schwierigkeit: <?= $diff ?> &nbsp;·&nbsp; <?= $total ?> Fragen</p>
  </div>

  <!-- STATS -->
  <div class="result-stats">
    <div class="stat-box">
      <div class="val val-green"><?= $correct ?></div>
      <div class="lbl">✓ Richtig</div>
    </div>
    <div class="stat-box">
      <div class="val val-red"><?= $wrong ?></div>
      <div class="lbl">✗ Falsch</div>
    </div>
    <div class="stat-box">
      <div class="val val-accent"><?= $score ?>%</div>
      <div class="lbl">Punktzahl</div>
    </div>
  </div>

  <!-- AKTIONEN -->
  <div style="display:flex; gap:1rem; margin-bottom:2.5rem; flex-wrap:wrap;">
    <a href="index.php" class="btn btn-primary" style="flex:1;">⚡ Neues Quiz</a>
  </div>

  <!-- AUSWERTUNG -->
  <div class="answers-review">
    <h2 class="page-title" style="font-size:1.3rem; margin-bottom:1.25rem;">📋 Auswertung</h2>

    <?php foreach ($details as $i => $item): ?>
    <div class="review-item">
      <div class="q-num">Frage <?= $i + 1 ?></div>
      <div class="q-text"><?= $item['question'] ?></div>

      <?php if (!$item['is_correct']): ?>
        <?php if ($item['given']): ?>
        <div class="answer-row">
          <div class="dot dot-red"></div>
          <span style="color:var(--danger);">
            Deine Antwort: <?= $item['given'] ?> – <?= $item['options'][$item['given']] ?? '' ?>
          </span>
        </div>
        <?php else: ?>
        <div class="answer-row">
          <div class="dot dot-red"></div>
          <span style="color:var(--danger);">Nicht beantwortet</span>
        </div>
        <?php endif; ?>
      <?php endif; ?>

      <div class="answer-row">
        <div class="dot dot-green"></div>
        <span style="color:var(--success);">
          Richtig: <?= $item['correct'] ?> – <?= $item['options'][$item['correct']] ?? '' ?>
        </span>
      </div>

      <?php if ($item['explanation']): ?>
      <p style="margin-top:0.75rem; font-size:0.85rem; color:var(--muted);
                border-top:1px solid var(--border); padding-top:0.75rem;">
        💡 <?= $item['explanation'] ?>
      </p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
const circumference = <?= $circumference ?>;
const targetScore   = <?= $score ?>;
const ring = document.getElementById('ringFill');
const num  = document.getElementById('scoreNum');
let cur = 0;
const step = targetScore / 60;

const t = setInterval(() => {
  cur = Math.min(cur + step, targetScore);
  const d = circumference * (cur / 100);
  ring.setAttribute('stroke-dasharray', `${d} ${circumference - d}`);
  num.textContent = Math.round(cur) + '%';
  if (cur >= targetScore) clearInterval(t);
}, 16);
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>