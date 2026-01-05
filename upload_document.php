<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'] ?? null;

    if (!isset($_FILES['working_paper']) || !$client_id) {
        echo "Error: Invalid file or client ID.";
        exit();
    }

    $file = $_FILES['working_paper'];
    $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Error: Invalid file type. Allowed types: PDF, Word, Excel.";
        exit();
    }

    $upload_dir = 'uploads/';
    $new_filename = $client_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        echo "File uploaded successfully!";
        // Optionally, save file details in the database
    } else {
        echo "Error uploading the file.";
    }
}
?>

kkkkkkk
<?php
include('header.php');
require_once 'db.php'; // Include your database connection

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

phpinfo();


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the client ID from the hidden field in the form
    $client_id = $_POST['client_id'];

    // Check if a file is uploaded
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        // Get the file details
        $file_name = $_FILES['document']['name'];
        $file_tmp_name = $_FILES['document']['tmp_name'];
        $file_size = $_FILES['document']['size'];
        $file_error = $_FILES['document']['error'];

        // Define the allowed file types (you can modify this list if necessary)
        $allowed_extensions = ['pdf', 'docx', 'xlsx', 'jpg', 'png', 'txt'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Check if the file extension is allowed
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<div class='alert alert-danger'>Invalid file type. Only PDF, DOCX, XLSX, JPG, PNG, and TXT files are allowed.</div>";
        } else {
            // Create a unique file name to avoid overwriting
            $unique_file_name = uniqid('doc_', true) . '.' . $file_extension;

            // Define the directory where the file will be uploaded
            $upload_dir = 'uploads/';
            $upload_path = $upload_dir . $unique_file_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                // Update the database with the document information
                $sql = "INSERT INTO client_documents (client_id, file_name, file_path, upload_date) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param('iss', $client_id, $file_name, $upload_path);
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Document uploaded successfully!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error uploading document to the database: " . htmlspecialchars($stmt->error) . "</div>";
                    }

                    $stmt->close();
                } else {
                    echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>There was an error uploading the document. Please try again.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Please select a document to upload.</div>";
    }
}

include('footer.php');
?>
<?php
include('header.php');
require_once 'db.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch client ID from GET request
$client_id = $_GET['id'] ?? null;

if (!$client_id) {
    echo "<div class='alert alert-danger'>No client ID provided.</div>";
    exit();
}

// Fetch client details
$client_sql = "SELECT id, name, license_no, contact, status, document FROM audit_clients WHERE id = ?";
$client_stmt = $conn->prepare($client_sql);

if ($client_stmt) {
    $client_stmt->bind_param('i', $client_id);
    $client_stmt->execute();
    $client_result = $client_stmt->get_result();

    if ($client_result->num_rows > 0) {
        $client_data = $client_result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Client not found.</div>";
        exit();
    }
    $client_stmt->close();
} else {
    echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
    exit();
}

// Handle form submission to update client information and document
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $license_no = $_POST['license_no'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $status = $_POST['status'] ?? '';
    $document = $client_data['document'];  // Keep the current document name by default

    // Handle file upload (only if a new file is selected)
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            // Upload new file
            $document = 'uploads/' . uniqid() . '.' . $file_extension;
            if (move_uploaded_file($_FILES['document']['tmp_name'], $document)) {
                // Successfully uploaded file
            } else {
                echo "<div class='alert alert-danger'>Failed to upload document.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid file type. Allowed types: " . implode(", ", $allowed_extensions) . ".</div>";
        }
    }

    if (!$name || !$license_no || !$contact || !$status) {
        echo "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        $update_sql = "UPDATE audit_clients SET name = ?, license_no = ?, contact = ?, status = ?, document = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);

        if ($update_stmt) {
            $update_stmt->bind_param('sssssi', $name, $license_no, $contact, $status, $document, $client_id);
            if ($update_stmt->execute()) {
                echo "<div class='alert alert-success'>Client information updated successfully.</div>";
                // Refresh the client data
                $client_data = [
                    'name' => $name,
                    'license_no' => $license_no,
                    'contact' => $contact,
                    'status' => $status,
                    'document' => $document
                ];
            } else {
                echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($conn->error) . "</div>";
            }
            $update_stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// Handle document deletion
if (isset($_POST['delete_document'])) {
    $document_path = $client_data['document'];
    if ($document_path && file_exists($document_path)) {
        unlink($document_path);  // Delete the file from the server
        // Update the database to remove the document
        $update_sql = "UPDATE audit_clients SET document = NULL WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('i', $client_id);
        $update_stmt->execute();
        $update_stmt->close();

        echo "<div class='alert alert-success'>Document deleted successfully.</div>";
        // Clear the document variable
        $client_data['document'] = null;
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
                <form action="edit_client.php?id=<?= urlencode($client_id) ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Client Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($client_data['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="license_no" class="form-label">License Number</label>
                        <input type="text" class="form-control" id="license_no" name="license_no" value="<?= htmlspecialchars($client_data['license_no']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($client_data['contact']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Active" <?= $client_data['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $client_data['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Document upload section -->
                    <div class="mb-3">
                        <label for="document" class="form-label">Client Document</label>
                        <input type="file" class="form-control" id="document" name="document">
                    </div>

                    <!-- Display current document if available -->
                    <?php if ($client_data['document']): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Document</label><br>
                            <a href="<?= htmlspecialchars($client_data['document']) ?>" target="_blank">View Document</a>
                            <br><br>
                            <button type="submit" name="delete_document" class="btn btn-danger">Delete Document</button>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg m-2">Save Changes</button>
                        <a href="client_details.php?id=<?= urlencode($client_id) ?>" class="btn btn-secondary btn-lg m-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
