<?php
include('header.php');
include('db.php');
?>

<div class="container mt-5">
    <h2 class="text-center mb-5">View Clients</h2>

    <?php
    $result = $conn->query("SELECT id, tin_no, company_name, status, report_link, audit_date, created_at, updated_at 
    FROM audit_clients 
    WHERE status = 'audit_working_paper'");
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

    // Fetch client details from database
    $client_sql = "SELECT id, tin_no, company_name, registration_no, contact_person, status FROM audit_clients WHERE id = ?";
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

    // Fetch uploaded documents for the client
    $documents = [];
    $doc_sql = "SELECT id, original_name, file_name, upload_date FROM uploaded_documents WHERE client_id = ? ORDER BY upload_date DESC";
    $doc_stmt = $conn->prepare($doc_sql);

    if ($doc_stmt) {
        $doc_stmt->bind_param('i', $client_id);
        $doc_stmt->execute();
        $doc_result = $doc_stmt->get_result();

        while ($doc = $doc_result->fetch_assoc()) {
            $documents[] = $doc;
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
    <title>Client Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
    <div class="container my-5">
        <!-- Client Details Section -->
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-primary text-white text-center">
                <h3>Client Details</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <?php foreach ($client_data as $key => $value): ?>
                        <tr>
                            <th><?= ucfirst(str_replace('_', ' ', $key)) ?></th>
                            <td><?= htmlspecialchars($value ?: 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div class="text-center">
                    <a href="edit_client.php?id=<?= urlencode($client_data['id']) ?>" class="btn btn-warning btn-lg m-2">Edit</a>
                    <a href="audit_clients.php" class="btn btn-secondary btn-lg m-2">Back to Clients</a>
                </div>
            </div>
        </div>

        <!-- Link to Audit Working Paper -->
        <div class="text-center mt-4">
            <a href="audit_working_paper.php?audit_id=<?= urlencode($client_data['id']) ?>" class="btn btn-info btn-lg">
                <i class="bi bi-file-earmark-ear"></i> Go to Audit Working Paper
            </a>
        </div>

        <!-- Uploaded Documents Section -->
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-file-earmark"></i> Uploaded Documents</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($documents)): ?>
                    <ul class="list-group">
                        <?php foreach ($documents as $doc): ?>
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
                                    <a href="edit_document.php?doc_id=<?= urlencode($doc['id']) ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete_document.php?doc_id=<?= urlencode($doc['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this document?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> No documents uploaded yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
