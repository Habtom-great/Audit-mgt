<?php
include('header.php');
include('db.php');

// Initialize file validity
$file_valid = true;
$file_path = null;

// Check if the file is uploaded
if (isset($_FILES['supporting_doc']) && $_FILES['supporting_doc']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['supporting_doc']['size'] > 5 * 1024 * 1024) { // 5MB limit
        echo "<div class='alert alert-danger text-center mt-3'>File is too large. Maximum size is 5MB.</div>";
        $file_valid = false;
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($_FILES['supporting_doc']['tmp_name']);
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'text/plain',
            'image/jpeg',
            'image/png',
            'application/zip'
        ];
        if (!in_array($mime_type, $allowed_mimes)) {
            echo "<div class='alert alert-danger text-center mt-3'>Invalid file type. Allowed types: PDF, DOC, DOCX, XLS, TXT, JPEG, PNG, ZIP.</div>";
            $file_valid = false;
        } else {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $upload_dir . basename($_FILES['supporting_doc']['name']);
            if (!move_uploaded_file($_FILES['supporting_doc']['tmp_name'], $file_path)) {
                echo "<div class='alert alert-danger text-center mt-3'>Error moving uploaded file.</div>";
                $file_valid = false;
            }
        }
    }
}

// Handle form data
$data = $_POST;
$company_name = trim($data['company_name'] ?? '');
$company_owner = trim($data['company_owner'] ?? '');
$tin_no = trim($data['tin_no'] ?? '');
$registration_number = trim($data['registration_number'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');

// Validation
$errors = [];

if (empty($company_name)) $errors[] = "Company Name is required.";
if (empty($company_owner)) $errors[] = "Company Owner is required.";
if (empty($tin_no)) $errors[] = "TIN Number is required.";
if (empty($registration_number)) $errors[] = "Registration Number is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid Email Address.";
if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "Phone number must be 10 digits.";

if (!empty($errors)) {
    echo "<div class='alert alert-danger mt-3'><ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul></div>";
} elseif ($file_valid) {
    // Insert data into the database
    $stmt = $conn->prepare("
        INSERT INTO audit_clients (company_name, company_owner, tin_no, registration_no, email, phone, supporting_docs)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("<div class='alert alert-danger text-center mt-3'>Prepare failed: " . $conn->error . "</div>");
    }

    $stmt->bind_param("sssssss", $company_name, $company_owner, $tin_no, $registration_number, $email, $phone, $file_path);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center mt-3'>Client registered successfully.</div>";
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Audit Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .form-container {
            max-width: 700px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">Register Audit Client</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" name="company_name" id="company_name" required>
            </div>

            <div class="mb-3">
                <label for="company_owner" class="form-label">Company Owner/Representative</label>
                <input type="text" class="form-control" name="company_owner" id="company_owner" required>
            </div>

            <div class="mb-3">
                <label for="tin_no" class="form-label">TIN Number</label>
                <input type="text" class="form-control" name="tin_no" id="tin_no" required>
            </div>

            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" class="form-control" name="registration_number" id="registration_number" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" required>
            </div>

            <div class="mb-3">
                <label for="supporting_doc" class="form-label">Upload Supporting Document</label>
                <input type="file" class="form-control" name="supporting_doc" id="supporting_doc" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register Client</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>