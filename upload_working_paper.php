<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $upload_dir = 'uploads/';
    $file = $_FILES['working_paper'];
    $original_name = $file['name'];
    $file_name = uniqid() . '_' . basename($original_name);

    if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
        // Insert file details into the database
        $sql = "INSERT INTO uploaded_documents (client_id, original_name, file_name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('iss', $client_id, $original_name, $file_name);
            if ($stmt->execute()) {
                echo "File uploaded and saved successfully.";
            } else {
                echo "Error saving file details: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Failed to upload file.";
    }
}
?>

