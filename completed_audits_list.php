<?php
include('header.php');
include('db.php'); // Include the database connection file

// Check if the database connection is initialized
if (!isset($conn)) {
    die("Database connection is not initialized.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Audits</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5">Completed Audits</h2>

        <?php
        // Fetch completed audits
        $sql = "SELECT id, tin_no, company_name, status, report_link, created_at, updated_at 
                FROM audit_clients 
                WHERE status = 'Completed'";
        $result = $conn->query($sql);

        // Check if results exist
        if ($result && $result->num_rows > 0): ?>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Audit ID</th>
                        <th>TIN NO</th>
                        <th>Company Name</th>
                        <th>Report</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><?= htmlspecialchars($row['tin_no']); ?></td>
                            <td><?= htmlspecialchars($row['company_name']); ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($row['report_link']); ?>" target="_blank" class="btn btn-success btn-sm">
                                    View Report
                                </a>
                            </td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($row['status']); ?></span></td>
                            <td><?= htmlspecialchars($row['created_at']); ?></td>
                            <td><?= htmlspecialchars($row['updated_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">No completed audits available.</div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-3">Go Back</a>
    </div>
</body>
</html>

<?php
$conn->close(); // Close the database connection
include('footer.php');
?>
