<?php
include('header.php');
require_once 'db.php';
// Database connection details
$db_host = 'localhost';
$db_user = 'root'; // Default XAMPP username
$db_password = ''; // Default XAMPP password is empty
$db_name = 'audit_management'; // Replace with your database name

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_POST['client_id'];
    $name = $_POST['name'];
    $license_number = $_POST['license_number'];
    $contact = $_POST['contact'];
    $status = $_POST['status'];

    // Handle file upload
    $upload_dir = 'uploads/';
    $upload_status = '';

    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['document']['tmp_name'];
        $file_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES['document']['name']));
        $file_dest = $upload_dir . $file_name;

        // Get the file extension
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        // Check if the file extension is allowed
        if (!in_array($file_extension, $allowed_extensions)) {
            die("Invalid file extension. Allowed types: " . implode(', ', $allowed_extensions));
        }

        // Validate MIME type
        $file_mime_type = mime_content_type($file_tmp_path);
        $allowed_mime_types = [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
            'image/jpeg', 
            'image/png'
        ];

        // Check if the MIME type is allowed
        if (!in_array($file_mime_type, $allowed_mime_types)) {
            die("Invalid MIME type: $file_mime_type. Allowed types: " . implode(', ', $allowed_mime_types));
        }

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($file_tmp_path, $file_dest)) {
            $upload_status = "File uploaded successfully.";
        } else {
            die("Error uploading the file.");
        }
    } else {
        // If no file is uploaded, set an empty document filename
        $file_dest = null;
    }

    // Prepare and execute SQL to update client data
    $sql = "UPDATE audit_clients SET name=?, license_number=?, contact=?, status=?, document=? WHERE client_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $license_number, $contact, $status, $file_dest, $client_id);

    if ($stmt->execute()) {
        echo "<p>Client information updated successfully.</p>";
    } else {
        echo "<p>Error updating client information.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Client</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
</head>
<body>
    <h1>Update Client Details</h1>

    <form method="POST" action="update_client.php" enctype="multipart/form-data">
        <!-- Client Name -->
        <label for="name">Client Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
        <br>

        <!-- License Number -->
        <label for="license_number">License Number:</label>
        <input type="text" name="license_number" id="license_number" value="<?php echo htmlspecialchars($license_number); ?>" required>
        <br>

        <!-- Contact -->
        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
        <br>

        <!-- Status -->
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="Active" <?php echo ($status === 'Active') ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo ($status === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
        </select>
        <br>

        <!-- Upload Client Document -->
        <label for="document">Upload Client Document:</label>
        <input type="file" name="document" id="document" accept=".pdf, .doc, .docx, .jpg, .jpeg, .png">
        <br>

        <!-- Hidden Field for Client ID -->
        <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($client_id); ?>">

        <!-- Submit Button -->
        <button type="submit">Update Client</button>
    </form>
</body>
</html>

    
 <style>
        /* Inline CSS for quick preview */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #444;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        select,
        input[type="file"] {
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>