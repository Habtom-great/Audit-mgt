<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDir = "assets/uploads/documents/";
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $allowedTypes = array('pdf', 'doc', 'docx', 'jpg', 'png');
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type. Only PDF, DOC, JPG, and PNG are allowed.";
    }
}
?>
