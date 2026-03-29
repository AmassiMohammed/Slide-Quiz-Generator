<?php
<<<<<<< HEAD
    require_once '../templates/header.php';
?>

<h2>Turn your lecture Slides into quizzes</h2>

<p>
    Upload your lecture slides and automatically generate quiz questions to help you study faster.
</p>

<a href="/upload.php">
    <button>Upload Slides</button>
</a>

<?php
    require_once '../templates/footer.php';
?>
=======
session_start();

$pageTitle = 'Startseite';
$error     = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

require_once __DIR__ . '/../templates/header.php';
?>


<div class="container">

  <!-- HERO -->
  <div class="hero">
    <div class="hero-badge">✦ KI-gestütztes Lernen</div>
    <h1>Aus jedem Text ein <em>Quiz</em> machen</h1>
    <p>PDF hochladen oder Text einfügen – QuizGen erstellt sofort intelligente Fragen zum Lernen.</p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- MAIN CARD -->
  <div class="card">
    <form action="upload.php" method="POST" enctype="multipart/form-data" id="mainForm">

      <!-- TABS -->
      <div class="tabs">
        <button type="button" class="tab-btn active" onclick="switchTab('pdf')">📄 PDF hochladen</button>
        <button type="button" class="tab-btn"        onclick="switchTab('text')">✏️ Text eingeben</button>
      </div>

      <!-- PDF TAB -->
      <div class="tab-panel active" id="tab-pdf">
        <div class="upload-zone" id="uploadZone">
          <input type="file" name="pdf_file" id="pdfInput"
                 accept=".pdf" onchange="onFileSelect(this)">
          <span class="upload-icon">📁</span>
          <p id="uploadText"><strong>PDF hier ablegen</strong> oder klicken zum Auswählen</p>
          <p class="text-muted" style="font-size:0.82rem; margin-top:0.3rem;">Nur .pdf – max. 10 MB</p>
        </div>
      </div>

      <!-- TEXT TAB -->
      <div class="tab-panel" id="tab-text">
        <div class="form-group">
          <label for="text_input">Text hier einfügen</label>
          <textarea name="text_input" id="text_input"
                    placeholder="Vorlesungsnotizen, Artikel, Skriptum ..."></textarea>
        </div>
      </div>

      <input type="hidden" name="input_type" id="inputType" value="pdf">

      <div class="divider">Quiz-Einstellungen</div>

      <!-- FRAGEN-ANZAHL -->
      <div class="form-group">
        <label>Anzahl der Fragen</label>
        <div class="radio-group">
          <div class="radio-pill">
            <input type="radio" name="question_count" id="q10" value="10" checked>
            <label for="q10"><strong>10</strong> Fragen</label>
          </div>
          <div class="radio-pill">
            <input type="radio" name="question_count" id="q20" value="20">
            <label for="q20"><strong>20</strong> Fragen</label>
          </div>
        </div>
      </div>

      <!-- SCHWIERIGKEIT -->
      <div class="form-group mt-2">
        <label for="difficulty">Schwierigkeitsgrad</label>
        <select name="difficulty" id="difficulty">
          <option value="easy">🟢 Einfach – Grundlagen</option>
          <option value="medium" selected>🟡 Mittel – Standard</option>
          <option value="hard">🔴 Schwer – Vertieft</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary" id="submitBtn">
        ⚡ Quiz generieren
      </button>

    </form>
  </div>

  <!-- HOW IT WORKS -->
  <div class="how-grid">
    <div class="card how-card">
      <div class="how-icon">📤</div>
      <strong>1. Hochladen</strong>
      <p>PDF oder Text einfügen</p>
    </div>
    <div class="card how-card">
      <div class="how-icon">🤖</div>
      <strong>2. KI analysiert</strong>
      <p>Fragen werden generiert</p>
    </div>
    <div class="card how-card">
      <div class="how-icon">🎯</div>
      <strong>3. Quiz starten</strong>
      <p>Lernen &amp; Ergebnis sehen</p>
    </div>
  </div>

</div>

<!-- LOADER -->
<div class="loader-overlay" id="loader">
  <div class="loader-spinner"></div>
  <div class="loader-text">Quiz wird generiert...</div>
  <div class="loader-sub">KI analysiert deinen Inhalt – dauert ~15 Sek.</div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach((b, i) =>
    b.classList.toggle('active', (i === 0) === (tab === 'pdf'))
  );
  document.getElementById('tab-pdf').classList.toggle('active', tab === 'pdf');
  document.getElementById('tab-text').classList.toggle('active', tab === 'text');
  document.getElementById('inputType').value = tab;
}

function onFileSelect(input) {
  if (input.files?.[0]) {
    document.getElementById('uploadText').innerHTML =
      `<strong>✓ ${input.files[0].name}</strong>`;
    document.getElementById('uploadZone').style.borderColor = 'var(--accent)';
  }
}

// Drag & Drop
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', ()  => zone.classList.remove('drag-over'));
zone.addEventListener('drop', e => {
  e.preventDefault();
  zone.classList.remove('drag-over');
  if (e.dataTransfer.files[0]) {
    document.getElementById('pdfInput').files = e.dataTransfer.files;
    onFileSelect(document.getElementById('pdfInput'));
  }
});

// Submit
document.getElementById('mainForm').addEventListener('submit', function(e) {
  const type = document.getElementById('inputType').value;
  if (type === 'pdf' && !document.getElementById('pdfInput').files?.[0]) {
    alert('Bitte eine PDF-Datei auswählen.'); e.preventDefault(); return;
  }
  if (type === 'text' && document.getElementById('text_input').value.trim().length < 100) {
    alert('Bitte mindestens 100 Zeichen Text eingeben.'); e.preventDefault(); return;
  }
  document.getElementById('loader').classList.add('show');
  document.getElementById('submitBtn').disabled = true;
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
>>>>>>> 7f8f1c33dbed2223e54562a0225bba897bf7014e

