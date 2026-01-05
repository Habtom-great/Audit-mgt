<?php
include('header.php');
include('db.php'); // Ensure this file establishes a proper connection
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <h2 class="text-center mb-5">Pending Audits</h2>

    <?php
    // Fetch pending audits from the database
    $result = $conn->query("SELECT id, tin_no, company_name, status, report_link, audit_date, created_at, updated_at 
    FROM audit_clients 
    WHERE status = 'Pending'");
   
   

    if ($result && $result->num_rows > 0) {
        // Display results in a table
       
        echo " <table class='table table-bordered table-hover table-striped'>
            <thead class='table-dark'>
                    <tr>
                        <th scope='col'>Audit ID</th>
                        <th scope='col'>TIN NO</th>
                        <th scope='col'>Company Name</th>
                        <th scope='col'>Audit Date</th>
                        <th scope='col'>Reason for Delay</th>
                        <th scope='col'>Status</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "
                    <td>{$row['id']}</td>
                    <td>{$row['tin_no']}</td>
                    <td>{$row['company_name']}</td>
                    <td>{$row['audit_date']}</td>
                    <td>{$row['report_link']}</td>
                    <td><span class='badge bg-danger'>{$row['status']}</span></td>
                </tr>";
        }

        echo "</tbody></table>";
    } else {
        // Display a message if no pending audits are found
        echo "<div class='alert alert-warning text-center'>No pending audits found.</div>";
    }

    // Close the database connection
    $conn->close();
    ?>
</div>

<?php include('footer.php'); ?>
