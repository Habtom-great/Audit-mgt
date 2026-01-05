<?php
// Include database connection
include 'db.php';
include('header.php');

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch audit findings from the database
$query = "SELECT * FROM audit_findings ORDER BY date_reported DESC";
$result = mysqli_query($conn, $query);

// Check for database query errors
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Findings Report</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        header {
            background-color: #004d99;
            color: white;
            text-align: center;
            padding: 20px;
        }

        header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #004d99;
            color: white;
            font-size: 1.1rem;
        }

        table td {
            background-color: #f9f9f9;
            color: #333;
        }

        table tr:nth-child(even) td {
            background-color: #f1f1f1;
        }

        table tr:hover td {
            background-color: #eaeaea;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #004d99;
            color: white;
            margin-top: 30px;
        }

        footer p {
            margin: 0;
            font-size: 1rem;
        }

        p {
            text-align: center;
            font-size: 1.2rem;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <h1>Audit Findings Report</h1>
    </header>

    <main>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Finding ID</th>
                        <th>Description</th>
                        <th>Severity</th>
                        <th>Date Reported</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['finding_id']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['severity']) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['date_reported']))) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No audit findings available.</p>
        <?php endif; ?>

        <?php mysqli_free_result($result); ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Audit Management System</p>
    </footer>

    <?php mysqli_close($conn); ?>
</body>
</html>
kkkkkkkkk
<?php
include 'header.php';
include 'db.php';
// Database connection (replace with actual credentials)
$conn = new mysqli('localhost', 'root', '', 'audit_management');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// SQL Query
$sql = "SELECT * FROM audit_report"; // Replace with your table name

// Execute Query
$result = $conn->query($sql);

// Check if query execution was successful
if (!$result) {
    die("SQL Error: " . $conn->error); // Output MySQL error and stop execution
}

// Fetch data only if the query was successful
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
    echo "<pre>";
    print_r($rows);
    echo "</pre>";
} else {
    echo "No records found.";
}

// Close connection
$conn->close();



// SQL query to fetch data
$sql = "SELECT * FROM audit_clients"; // Replace `audit_entries` with your actual table name

// Execute query and check for errors
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Fetch rows
$savedRows = $result->fetch_all(MYSQLI_ASSOC);

// Debugging output (optional)
echo "<pre>";
print_r($savedRows);
echo "</pre>";

// Fetch saved rows from the database
$sql = "SELECT * FROM audit_entries";
$result = $conn->query($sql);
$savedRows = $result->fetch_all(MYSQLI_ASSOC);

$company_name = "Sample Company Ltd.";
$tin_no = "123456789";
$audit_year = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta, title, styles -->
</head>
<body>
<div class="header">
    Audit Findings Report for the Year: <?= htmlspecialchars($audit_year) ?>
</div>

<div class="container mt-4">
    <div class="mb-4">
        <div><strong>Company Name:</strong> <?= htmlspecialchars($company_name) ?></div>
        <div><strong>TIN No.:</strong> <?= htmlspecialchars($tin_no) ?></div>
    </div>

    <form action="process_audit_report.php" method="POST" enctype="multipart/form-data">
        <table id="audit-table" class="table audit-table table-hover">
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
            <?php foreach ($savedRows as $index => $row): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><input type="date" name="date[]" class="form-control" value="<?= $row['date'] ?>"></td>
                    <td><input type="text" name="account_name[]" class="form-control" value="<?= $row['account_name'] ?>"></td>
                    <td><textarea name="description[]" class="form-control"><?= $row['description'] ?></textarea></td>
                    <td><input type="text" name="reference_type[]" class="form-control" value="<?= $row['reference_type'] ?>"></td>
                    <td><input type="text" name="reference_no[]" class="form-control" value="<?= $row['reference_no'] ?>"></td>
                    <td><input type="text" name="status[]" class="form-control" value="<?= $row['status'] ?>"></td>
                    <td><input type="text" name="discrepancy_type[]" class="form-control" value="<?= $row['discrepancy_type'] ?>"></td>
                    <td><input type="number" name="amount[]" class="form-control" value="<?= $row['amount'] ?>"></td>
                    <td><input type="text" name="other_info[]" class="form-control" value="<?= $row['other_info'] ?>"></td>
                    <td>
                        <button type="button" class="btn btn-save-row">Save</button>
                        <button type="button" class="btn btn-delete-row">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button type="button" id="add-row-btn" class="btn btn-add-row">Add Row</button>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success">Submit Audit Report</button>
        </div>
    </form>
</div>

<div class="footer">
    © <?= date('Y') ?> Audit Report System | All Rights Reserved
</div>

<script>
    // Add dynamic row functionality (same as before)
    function addRow() {
    const tableBody = document.querySelector('#audit-table tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${orderNo++}</td>
        <td><input type="date" name="date[]" class="form-control" required></td>
        <td>
            <select name="account_name[]" class="form-select" required>
                <option value="">Select Account</option>
                <option value="cash">Cash</option>
                <option value="accounts_receivable">Accounts Receivable</option>
                <option value="inventory">Inventory</option>
            </select>
        </td>
        <td><textarea name="description[]" class="form-control description-field" required></textarea></td>
        <td>
            <select name="reference_type[]" class="form-select" required>
                <option value="">Select Reference</option>
                <option value="pv_no">PV No.</option>
                <option value="crv_no">CRV No.</option>
                <option value="jv_no">JV No.</option>
                <option value="other">Other</option>
            </select>
        </td>
        <td><input type="text" name="reference_no[]" class="form-control" required></td>
        <td>
            <select name="status[]" class="form-select" required>
                <option value="">Select Status</option>
                <option value="resolved">Resolved</option>
                <option value="unresolved">Unresolved</option>
                <option value="pending">Pending</option>
            </select>
        </td>
        <td>
            <select name="discrepancy[]" class="form-select" required>
                <option value="">Select Type</option>
                <option value="ledger_vs_bank">Ledger vs Bank</option>
                <option value="understated_by">Understated By</option>
                <option value="declared_vs_payable">Declared vs Payable</option>
            </select>
        </td>
        <td><input type="number" name="amount[]" class="form-control" placeholder="Amount" min="0" required></td>
        <td><input type="text" name="other_info[]" class="form-control"></td>
        <td>
            <button type="button" class="btn btn-save-row" onclick="saveRow(this)">Save</button>
            <button type="button" class="btn btn-delete-row" onclick="deleteRow(this)">Delete</button>
        </td>
    `;
    tableBody.appendChild(newRow);
}

    function addRow() {
        const tableBody = document.querySelector('#audit-table tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `...`; // Same as your addRow() logic
        tableBody.appendChild(newRow);
    }

    // Event listeners
    document.getElementById('add-row-btn').addEventListener('click', addRow);
</script>
</body>
</html>