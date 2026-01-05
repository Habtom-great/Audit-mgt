<?php 
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that client_id, statement_type, and uploaded_file are set
    $client_id = $_POST['client_id'] ?? null;
    $statement_type = $_POST['statement_type'] ?? null;
    $uploaded_file = $_FILES['financial_statement'] ?? null;

    // Check if any required field is missing
    if (!$client_id || !$statement_type || !$uploaded_file) {
        echo "<div class='alert alert-danger'>All fields are required.</div>";
        exit();
    }

    // Allowed file types for upload
    $allowed_file_types = ['pdf', 'doc', 'docx', 'xlsx', 'txt', 'jpg', 'png', 'zip'];

    // Get the file extension and convert it to lowercase for comparison
    $file_extension = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);
    $file_extension = strtolower($file_extension); // Ensure case-insensitive comparison

    // Check if the file type is allowed
    if (in_array($file_extension, $allowed_file_types)) {
        // Proceed with the file upload process
        $target_dir = "uploads/";
        $file_name = basename($uploaded_file['name']);
        $target_file = $target_dir . uniqid() . "_" . $file_name;

        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
            // Insert file details into the database
            $sql = "INSERT INTO uploaded_documents (client_id, original_name, file_name, statement_type, upload_date) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('isss', $client_id, $file_name, $target_file, $statement_type);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>File uploaded successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading file.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid file type. Only PDF, DOC, DOCX, XLSX, TXT, JPG, PNG, ZIP files are allowed.</div>";
    }
}
?>

<!-- Stylish Financial Documents Section -->
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="bi bi-file-earmark-text"></i> Financial Statements</h4>
        </div>
        <div class="card-body">
            <!-- File Upload Form -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="client_id" class="form-label">Client ID</label>
                    <input type="number" class="form-control" id="client_id" name="client_id" required>
                </div>
                <div class="form-group mb-3">
                    <label for="statement_type" class="form-label">Statement Type</label>
                    <select class="form-select" id="statement_type" name="statement_type" required>
                        <option value="Income Statement">Income Statement</option>
                        <option value="Balance Sheet">Balance Sheet</option>
                        <option value="Cash Flow">Cash Flow</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="financial_statement" class="form-label">Upload Financial Statement</label>
                    <input type="file" class="form-control" id="financial_statement" name="financial_statement" required>
                </div>
                <button type="submit" class="btn btn-success mt-3"><i class="bi bi-upload"></i> Upload File</button>
            </form>

            <hr>

            <!-- Displaying Uploaded Documents -->
            <?php
            $sql = "SELECT statement_type, original_name, file_name, upload_date FROM uploaded_documents WHERE client_id = ? ORDER BY statement_type, upload_date DESC";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('i', $client_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $current_type = '';
                    while ($doc = $result->fetch_assoc()) {
                        if ($doc['statement_type'] !== $current_type) {
                            if ($current_type !== '') echo "</ul>";
                            $current_type = $doc['statement_type'];
                            echo "<h5 class='mt-4'>" . htmlspecialchars($current_type) . "</h5><ul class='list-group'>";
                        }
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center mb-2'>";
                        echo "<strong>" . htmlspecialchars($doc['original_name']) . "</strong>";
                        echo "<small class='text-muted'>Uploaded on: " . htmlspecialchars(date('F j, Y, g:i a', strtotime($doc['upload_date']))) . "</small>";
                        echo "<a href='uploads/" . htmlspecialchars($doc['file_name']) . "' target='_blank' class='btn btn-outline-primary btn-sm'><i class='bi bi-eye'></i> View</a>";
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<div class='alert alert-warning mt-3'>No financial statements uploaded yet.</div>";
                }

                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Custom Styling for a Cleaner Look -->
<style>
    .card-header {
        background-color: #0056b3;
    }
    .btn-success {
        background-color: #28a745;
    }
    .btn-outline-primary:hover {
        background-color: #007bff;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ced4da;
    }
    .list-group-item {
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .alert {
        border-radius: 8px;
    }
    .container {
        max-width: 900px;
    }
</style>
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
