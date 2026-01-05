<?php
include('header.php');
require_once 'db.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch document ID from GET request
$doc_id = $_GET['doc_id'] ?? null;

if (!$doc_id) {
    echo "<div class='alert alert-danger'>No document ID provided.</div>";
    exit();
}

// Fetch document details
$doc_sql = "SELECT id, original_name, file_name, upload_date FROM uploaded_documents WHERE id = ?";
$doc_stmt = $conn->prepare($doc_sql);

if ($doc_stmt) {
    $doc_stmt->bind_param('i', $doc_id);
    $doc_stmt->execute();
    $doc_result = $doc_stmt->get_result();

    if ($doc_result->num_rows > 0) {
        $doc_data = $doc_result->fetch_assoc();
    } else {
        echo "<div class='alert alert-warning'>Document not found.</div>";
        exit();
    }
    $doc_stmt->close();
} else {
    echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body style="background-color: #f8f9fa;">
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h4>Edit Document</h4>
            </div>
            <div class="card-body">
                <form action="update_document.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="doc_id" value="<?= htmlspecialchars($doc_data['id']) ?>">

                    <!-- Original Name -->
                    <div class="mb-3">
                        <label for="original_name" class="form-label">Original Document Name</label>
                        <input type="text" class="form-control" id="original_name" name="original_name" value="<?= htmlspecialchars($doc_data['original_name']) ?>" required>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label for="upload" class="form-label">Change Document</label>
                        <input type="file" class="form-control" id="upload" name="upload">
                        <small class="form-text text-muted">Leave blank if you don't want to update the document.</small>
                    </div>

                    <!-- Current Document -->
                    <div class="mb-3">
                        <label class="form-label">Current Document</label>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="uploads/<?= htmlspecialchars($doc_data['file_name']) ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> View Current Document
                            </a>
                            <span class="text-muted">Uploaded on: <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($doc_data['upload_date']))) ?></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Update Document</button>
                        <a href="client_details.php?id=<?= urlencode($doc_data['id']) ?>" class="btn btn-secondary btn-lg ms-3">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php include('footer.php'); ?>
