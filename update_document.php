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
$client_sql = "SELECT document FROM audit_clients WHERE id = ?";
$client_stmt = $conn->prepare($client_sql);

if ($client_stmt) {
    $client_stmt->bind_param('i', $client_id);
    $client_stmt->execute();
    $client_result = $client_stmt->get_result();

    if ($client_result->num_rows > 0) {
        $client_data = $client_result->fetch_assoc();
        $current_document = $client_data['document'];
    } else {
        echo "<div class='alert alert-warning'>Client not found.</div>";
        exit();
    }
    $client_stmt->close();
} else {
    echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
    exit();
}

// Handle document update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            // Generate unique name and upload the new file
            $new_document_path = 'uploads/' . uniqid() . '.' . $file_extension;
            if (move_uploaded_file($_FILES['document']['tmp_name'], $new_document_path)) {
                // Delete the old document, if any
                if ($current_document && file_exists($current_document)) {
                    unlink($current_document);
                }

                // Update the database with the new document path
                $update_sql = "UPDATE audit_clients SET document = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);

                if ($update_stmt) {
                    $update_stmt->bind_param('si', $new_document_path, $client_id);
                    if ($update_stmt->execute()) {
                        echo "<div class='alert alert-success'>Document updated successfully.</div>";
                        $current_document = $new_document_path; // Update for display
                    } else {
                        echo "<div class='alert alert-danger'>Failed to update the database: " . htmlspecialchars($conn->error) . "</div>";
                    }
                    $update_stmt->close();
                } else {
                    echo "<div class='alert alert-danger'>Error preparing update query: " . htmlspecialchars($conn->error) . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Failed to upload the new document.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid file type. Allowed types: " . implode(', ', $allowed_extensions) . ".</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No document file selected or file upload error occurred.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3>Update Client Document</h3>
            </div>
            <div class="card-body">
                <form action="update_document.php?id=<?= urlencode($client_id) ?>" method="POST" enctype="multipart/form-data">
                    <!-- Display current document if available -->
                    <?php if ($current_document): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Document</label><br>
                            <a href="<?= htmlspecialchars($current_document) ?>" target="_blank" class="btn btn-link">View Current Document</a>
                        </div>
                    <?php endif; ?>

                    <!-- File upload input -->
                    <div class="mb-3">
                        <label for="document" class="form-label">New Document</label>
                        <input type="file" class="form-control" id="document" name="document" required>
                        <small class="text-muted">Allowed types: pdf, doc, docx, jpg, jpeg, png.</small>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Update Document</button>
                        <a href="edit_client.php?id=<?= urlencode($client_id) ?>" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
