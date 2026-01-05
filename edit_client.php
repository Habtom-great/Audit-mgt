<?php
include('header.php');
require_once 'db.php';

// Validate and fetch client ID from GET
$client_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$client_id) {
    echo "<div class='alert alert-danger'>Invalid client ID.</div>";
    exit();
}

// Check database connection
if (!$conn) {
    die("<div class='alert alert-danger'>Database connection failed: " . mysqli_connect_error() . "</div>");
}

// Fetch client details
$client_sql = "SELECT id, tin_no, company_name, registration_no, contact_person, status, supporting_docs FROM audit_clients WHERE id = ?";
$client_stmt = $conn->prepare($client_sql);
if ($client_stmt) {
    $client_stmt->bind_param('i', $client_id);
    $client_stmt->execute();
    $client_result = $client_stmt->get_result();
    $client_data = $client_result->fetch_assoc();
    if (!$client_data) {
        echo "<div class='alert alert-danger'>Client not found.</div>";
        exit();
    }
    $client_stmt->close();
} else {
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($conn->error) . "</div>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tin_no = trim($_POST['tin_no']);
    $company_name = trim($_POST['company_name']);
    $registration_no = trim($_POST['registration_no']);
    $contact_person = trim($_POST['contact_person']);
    $status = trim($_POST['status']);

    // Validate required fields
    if (empty($tin_no) || empty($company_name) || empty($registration_no) || empty($contact_person) || empty($status)) {
        echo "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        // File upload handling
        $file_valid = true;
        $document = $client_data['supporting_docs']; // Default to existing document
        if (!empty($_FILES['document']['name']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['pdf', 'jpg', 'png', 'doc', 'docx', 'xlsx', 'txt', 'zip'];
            $file_extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
            if (in_array($file_extension, $allowed_extensions)) {
                $document = 'uploads/' . uniqid() . '.' . $file_extension;
                if (!move_uploaded_file($_FILES['document']['tmp_name'], $document)) {
                    echo "<div class='alert alert-danger'>Failed to upload the file.</div>";
                    $file_valid = false;
                }
            } else {
                echo "<div class='alert alert-danger'>Invalid file type. Allowed types: " . implode(", ", $allowed_extensions) . ".</div>";
                $file_valid = false;
            }
        }

        if ($file_valid) {
            $update_sql = "UPDATE audit_clients SET tin_no = ?, company_name = ?, registration_no = ?, contact_person = ?, status = ?, supporting_docs = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param('ssssssi', $tin_no, $company_name, $registration_no, $contact, $status, $document, $client_id);
                if ($update_stmt->execute()) {
                    echo "<div class='alert alert-success'>Client updated successfully.</div>";
                    $client_data = compact('tin_no', 'company_name', 'registration_no', 'contact', 'status', 'document');
                } else {
                    echo "<div class='alert alert-danger'>Update failed: " . htmlspecialchars($conn->error) . "</div>";
                }
                $update_stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error preparing update query: " . htmlspecialchars($conn->error) . "</div>";
            }
        }
    }
}

// Handle document deletion
if (isset($_POST['delete_document']) && !empty($client_data['document'])) {
    if (file_exists($client_data['document'])) {
        unlink($client_data['document']);
        $update_sql = "UPDATE audit_clients SET document = NULL WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('i', $client_id);
        $update_stmt->execute();
        $update_stmt->close();
        $client_data['document'] = null;
        echo "<div class='alert alert-success'>Document deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Document not found for deletion.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3>Edit Client Information</h3>
            </div>
            <div class="card-body">
                <form action="edit_client.php?id=<?= htmlspecialchars($client_id) ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="tin_no" class="form-label">TIN No.</label>
                        <input type="text" class="form-control" id="tin_no" name="tin_no" value="<?= htmlspecialchars($client_data['tin_no']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?= htmlspecialchars($client_data['company_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="registration_no" class="form-label">Registration Number</label>
                        <input type="text" class="form-control" id="registration_no" name="registration_no" value="<?= htmlspecialchars($client_data['registration_no']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">contact_person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?= htmlspecialchars($client_data['contact_person']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status" name="status" value="<?= htmlspecialchars($client_data['status']) ?>" required>
                    </div>
                    <form action="upload.php" method="post" enctype="multipart/form-data">
    <label for="file">Upload File:</label>
    <input type="file" name="file" id="file" accept=".pdf, .jpg, .png, .doc, .docx, .xlsx, .txt, .zip" required>
    <button type="submit">Upload</button>
</form>
<script>
    document.getElementById('file').addEventListener('change', function () {
        const allowedExtensions = ['pdf', 'jpg', 'png', 'doc', 'docx', 'xlsx', 'txt', 'zip'];
        const file = this.files[0];
        if (file) {
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                alert('Invalid file type. Allowed types: pdf, jpg, png, doc, docx, xlsx, txt, zip.');
                this.value = ''; // Reset the input field
            }
        }
    });
</script>
                   <button type="submit" class="btn btn-primary">Update Client</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowedExtensions = ['pdf', 'jpg', 'png', 'doc', 'docx', 'xlsx', 'txt', 'zip'];
    $file = $_FILES['file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($file['name']);
            
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                echo "File successfully uploaded!";
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Allowed types: " . implode(', ', $allowedExtensions) . ".";
        }
    } else {
        echo "Error: " . $file['error'];
    }
} else {
    echo "Invalid request method.";
}
?>
