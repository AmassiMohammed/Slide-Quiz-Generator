<?php
    require_once '../templates/header.php';
?>

<h2>Upload Lecture Slides</h2>

<p>
    Upload a PDF file or paste your lecture text to generate quiz questions
</p>

<form action="" method="POST" enctype="multipart/form-data">
    <div>
        <label>Upload PDF:</label><br>
        <input type="file" name="slide_file" accept=".pdf"> 
    </div>
    <br>
    <div>
        <label>Or paste lecture text: </label><br>
        <textarea name="lecture_text" rows="8" cols="80"></textarea>
    </div>
    <br>
    <button type="submit">Generate Quiz</button>
</form>

<?php
    require_once '../templates/footer.php';
?>
