<?php
    require_once '../templates/header.php';
    $text = "";
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        #Wenn Text hinzugefügt wurde
        if(!empty($_POST["lecture_text"])){
            $text = $_POST["lecture_text"];
        }
        if(isset($_FILES["slide_file"]) && $_FILES["slide_file"]["error"] === 0){
            $uploadDir = "../uploads/";
            $fileName = basename($_FILES["slide_file"]["name"]);
            $targetPath = $uploadDir.$filename;

            move_uploaded_file($_FILES["slide_file"]["tmp_name"],$targetPath);
            echo "<p>File uploaded succesfully.</p>";
        }

    }
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
