<?php
require_once 'db.php';

// Fetch all uploaded documents
$sql = "SELECT * FROM uploaded_documents ORDER BY upload_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0):
?>
    <ul class="list-group">
        <?php while ($doc = $result->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($doc['original_name']) ?></strong><br>
                    <small class="text-muted">
                        Uploaded on: <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($doc['upload_date']))) ?>
                    </small>
                </div>
                <div>
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
        <?php endwhile; ?>
    </ul>
<?php
else:
    echo "<div class='alert alert-info'>No documents uploaded yet.</div>";
endif;
?>
