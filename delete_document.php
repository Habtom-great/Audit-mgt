<?php
require_once 'db.php';

// Fetch the documents from the database
$documents = [];
$sql = "SELECT id, original_name, file_name, upload_date FROM uploaded_documents";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($doc = $result->fetch_assoc()) {
        $documents[] = $doc;  // Add each document to the documents array
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
}

// Check if a document ID is provided for deletion
if (isset($_GET['doc_id'])) {
    $doc_id = $_GET['doc_id'];

    // Prepare the SQL query to fetch the document's file name from the database
    $sql = "SELECT file_name FROM uploaded_documents WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $doc_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the file name
            $doc = $result->fetch_assoc();
            $file_name = $doc['file_name'];

            // Delete the document from the file system
            $file_path = 'uploads/' . $file_name;
            if (file_exists($file_path)) {
                unlink($file_path);  // Delete the file from the server
            }

            // Delete the document record from the database
            $delete_sql = "DELETE FROM uploaded_documents WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            if ($delete_stmt) {
                $delete_stmt->bind_param('i', $doc_id);
                $delete_stmt->execute();
                echo "<div class='alert alert-success mt-4'>Document deleted successfully.</div>";
            } else {
                echo "<div class='alert alert-danger mt-4'>Error deleting document from database.</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-4'>Document not found.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger mt-4'>Error preparing query: " . htmlspecialchars($conn->error) . "</div>";
    }
} else {
    echo "<div class='alert alert-danger mt-4'>No document ID provided.</div>";
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="text-primary">Documents List</h3>
        <a href="document_list.php" class="btn btn-secondary btn-lg">
            <i class="bi bi-arrow-left-circle"></i> Back to Document List
        </a>
    </div>

    <div class="mt-4">
        <ul class="list-group">
            <?php foreach ($documents as $doc): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center shadow-sm mb-3">
                    <div>
                        <strong><?= htmlspecialchars($doc['original_name']) ?></strong><br>
                        <small class="text-muted">
                            Uploaded on: <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($doc['upload_date']))) ?>
                        </small>
                    </div>
                    <div class="btn-group">
                        <a href="uploads/<?= htmlspecialchars($doc['file_name']) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="uploads/<?= htmlspecialchars($doc['file_name']) ?>" class="btn btn-outline-success btn-sm" download>
                            <i class="bi bi-download"></i> Download
                        </a>
                        <a href="delete_document.php?doc_id=<?= $doc['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this document?')">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Include Bootstrap JS and Icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
