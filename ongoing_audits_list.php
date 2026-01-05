<?php
include('header.php');
include('db.php');

// Check database connection


?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="container mt-5">
    <h2 class="text-center mb-5">Ongoing Audits</h2>

    <?php
    // Query to fetch ongoing audits
    $query = "
        SELECT 
            id, 
            tin_no, 
            company_name, 
            audit_date, 
            status 
        FROM audit_clients 
        WHERE status = 'In progress'";

    // Execute the query
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Table header
        echo "
        <table class='table table-bordered table-hover table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th scope='col'>Audit ID</th>
                    <th scope='col'>TIN Number</th>
                    <th scope='col'>Client Name</th>
                    <th scope='col'>Audit Date</th>
                    <th scope='col'>Status</th>
                    <th scope='col'>Action</th>
                </tr>
            </thead>
            <tbody>";

        // Table rows
        while ($row = $result->fetch_assoc()) {
            $auditID = htmlspecialchars($row['id']);
            $tinNo = htmlspecialchars($row['tin_no']);
            $clientName = htmlspecialchars($row['company_name']);
            $auditDate = htmlspecialchars($row['audit_date']);
            $status = htmlspecialchars($row['status']);

            echo "
            <tr>
                <td>{$auditID}</td>
                <td>{$tinNo}</td>
                <td>{$clientName}</td>
                <td>{$auditDate}</td>
                <td><span class='badge bg-warning text-dark'>{$status}</span></td>
                <td>
                    <a href='audit_working_paper.php?audit_id={$auditID}' class='btn btn-primary btn-sm'>
                        <i class='fas fa-file-alt'></i> View Working Paper
                    </a>
                </td>
            </tr>";
        }

        echo "</tbody></table>";
    } else {
        // Message when no ongoing audits are found
        echo "<div class='alert alert-warning text-center'>No ongoing audits found.</div>";
    }

    // Close the database connection
    $conn->close();
    ?>
</div>

<!-- Footer -->
<div class="text-center my-4">
    <a href="audit_clients.php" class="btn btn-outline-dark me-3">
        <i class="bi bi-arrow-left"></i> Back to Audit Clients Lists
    </a>
    <?php if (isset($auditID)) { ?>
        <a href="audit_working_paper.php?audit_id=<?php echo $auditID; ?>" class="btn btn-outline-dark">
            <i class="bi bi-arrow-right"></i> Go to Audit Working Paper
        </a>
    <?php } ?>
</div>

<?php include('footer.php'); ?>
