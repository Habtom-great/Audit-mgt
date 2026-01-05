<?php
// Assuming a database connection is established
include 'db.php';

// Initialize $savedRows to avoid undefined variable warnings
$savedRows = [];

// Example: Fetch saved rows from the database (adjust the query as per your database structure)
$query = "SELECT * FROM audit_report"; // Adjust 'audit_report' to your actual table name
$result = mysqli_query($conn, $query);

// Check for query success
if ($result) {
    $savedRows = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "Error fetching data: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Report</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
</head>
<body>
    <header>
        <h1>Audit Report</h1>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Date</th>
                    <th>Account Name</th>
                    <th>Description</th>
                    <th>Reference Type</th>
                    <th>Reference No.</th>
                    <th>Status</th>
                    <th>Discrepancy Type</th>
                    <th>Amount</th>
                    <th>Other Info</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($savedRows)): ?>
                    <?php foreach ($savedRows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_no'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['date'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['account_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['description'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['reference_type'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['reference_no'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['status'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['discrepancy_type'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['amount'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['other_info'] ?? 'N/A') ?></td>
                            <td><button>Edit</button> <button>Delete</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button onclick="addRow()">Add Row</button>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Audit Management System</p>
    </footer>

    <script>
        function addRow() {
            alert("Add Row functionality not implemented yet.");
        }
    </script>
</body>
</html>

kkkkkkkkkkkkkkk
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include_once 'db.php'; // Ensure this file contains your database connection logic

// Start session for user data
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $prepared_by = trim($_POST['prepared_by']);
    $prepared_date = trim($_POST['prepared_date']);
    $prepared_signature = trim($_POST['prepared_signature']);
    $supervisor_name = trim($_POST['supervisor_name']);
    $supervisor_date = trim($_POST['supervisor_date']);
    $supervisor_signature = trim($_POST['supervisor_signature']);
    $uploaded_files = isset($_FILES['uploaded_files']) ? $_FILES['uploaded_files'] : null;

    // Handle file upload if any
    $uploaded_file_paths = [];
    if ($uploaded_files) {
        $upload_dir = 'uploads/'; // Set upload directory
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
        }

        foreach ($uploaded_files['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($uploaded_files['name'][$key]);
            $target_path = $upload_dir . time() . "_" . $file_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $uploaded_file_paths[] = $target_path;
            } else {
                die("Error uploading file: " . $file_name);
            }
        }
    }

    // Prepare SQL query for audit summary
    $sql_summary = "INSERT INTO audit_summary (
        prepared_by, 
        prepared_date, 
        prepared_signature, 
        supervisor_name, 
        supervisor_date, 
        supervisor_signature, 
        uploaded_files
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt_summary = $conn->prepare($sql_summary);

    // Check if preparation is successful
    if (!$stmt_summary) {
        die("Error in SQL preparation: " . $conn->error);
    }

    // Bind parameters
    $stmt_summary->bind_param(
        'sssssss',
        $prepared_by,
        $prepared_date,
        $prepared_signature,
        $supervisor_name,
        $supervisor_date,
        $supervisor_signature,
        json_encode($uploaded_file_paths)
    );

    // Execute statement
    if ($stmt_summary->execute()) {
        // Success message
        echo "<script>alert('Audit report saved successfully!');</script>";
    } else {
        die("Error executing query: " . $stmt_summary->error);
    }

    $stmt_summary->close();
    $conn->close();
}

// Export data to Excel or Word
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];

    // Fetch audit data
    $sql_export = "SELECT * FROM audit_summary";
    $result = $conn->query($sql_export);

    if ($result->num_rows > 0) {
        // Initialize export content
        $export_content = "";

        // Prepare table headers
        $export_content .= "Prepared By\tPrepared Date\tSupervisor Name\tSupervisor Date\tReference Files\n";

        // Fetch rows
        while ($row = $result->fetch_assoc()) {
            $export_content .= "{$row['prepared_by']}\t{$row['prepared_date']}\t{$row['supervisor_name']}\t{$row['supervisor_date']}\t{$row['uploaded_files']}\n";
        }

        // Determine export type
        if ($export_type === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="audit_report.xls"');
        } elseif ($export_type === 'word') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="audit_report.doc"');
        }

        echo $export_content;
        exit();
    } else {
        echo "<script>alert('No data available for export');</script>";
    }
}

// Include header and footer
include_once 'header.php';
include_once 'footer.php';
?>
