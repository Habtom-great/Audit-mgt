<?php
include('header.php');
include('db.php');

// Debugging the value of audit_id
if (isset($_GET['audit_id'])) {
    $audit_id = $_GET['audit_id'];
    echo "<div class='alert alert-info text-center'>Audit ID is: " . htmlspecialchars($audit_id) . "</div>"; // Debug message
} else {
    echo "<div class='alert alert-danger text-center'>Audit ID is not provided in the URL.</div>";
    exit;
}

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Validate and fetch data if audit_id is valid
if (is_numeric($audit_id)) {
    // Prepare and execute query for audit details
    $query = "SELECT * FROM audit_clients WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        // Print the error if the query preparation fails
        echo "<div class='alert alert-danger text-center'>Error preparing query: " . $conn->error . "</div>";
        exit;
    }

    $stmt->bind_param("i", $audit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $audit = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger text-center'>Audit not found for ID: " . htmlspecialchars($audit_id) . ".</div>";
        exit;
    }

    // Prepare and execute query for ledger accounts
    $ledgerQuery = "SELECT * FROM ledger_account WHERE audit_id = ?";
    $ledgerStmt = $conn->prepare($ledgerQuery);

    if ($ledgerStmt === false) {
        // Print the error if the query preparation fails
        echo "<div class='alert alert-danger text-center'>Error preparing ledger query: " . $conn->error . "</div>";
        exit;
    }

    $ledgerStmt->bind_param("i", $audit_id);
    $ledgerStmt->execute();
    $ledgerResult = $ledgerStmt->get_result();
} else {
    echo "<div class='alert alert-danger text-center'>Invalid Audit ID. Please ensure it's a valid number.</div>";
    exit;
}

// Close the connection
$conn->close();
?>

<!-- Bootstrap and FontAwesome CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mt-5">
    <!-- Header -->
    <div class="text-center">
        <h1 class="mb-4">Audit Working Paper</h1>
        <p><strong>Audit ID:</strong> <?php echo htmlspecialchars($audit['id']); ?></p>
        <p><strong>Company Name:</strong> <?php echo htmlspecialchars($audit['company_name']); ?></p>
    </div>

    <!-- Filter for Ledger Accounts -->
    <div class="mb-4 d-flex justify-content-between">
        <form method="GET" action="audit_working_paper.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Account Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <!-- Ledger Accounts Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="bi bi-book"></i> Ledger Accounts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Account Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ledgerResult->num_rows > 0): ?>
                            <?php while ($row = $ledgerResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['account_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo number_format($row['debit'], 2); ?></td>
                                    <td><?php echo number_format($row['credit'], 2); ?></td>
                                    <td><?php echo number_format($row['balance'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['reference'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No ledger accounts found for this audit.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5><i class="bi bi-pencil"></i> Auditor Notes</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="save_auditor_notes.php">
                <textarea class="form-control" rows="5" name="notes" placeholder="Enter auditor notes here..."><?php echo isset($audit['auditor_notes']) ? htmlspecialchars($audit['auditor_notes']) : ''; ?></textarea>
                <button type="submit" class="btn btn-success mt-3">Save Notes</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center">
        <a href="audit_clients.php" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left"></i> Back to Audit Clients List
        </a>
        <a href="Partner’s Review Checklist.php?audit_id=<?php echo $audit_id; ?>" class="btn btn-outline-dark">
            <i class="bi bi-arrow-right"></i> Go to Partner's Review Checklist
        </a>
    </div>
</div>

<?php include('footer.php'); ?>
kkkkkkkkkkk
<?php
include('header.php');
include('db.php');
?>
<?php
include('db.php');

// Fetch ledger accounts
$query = "SELECT audit_id, account_item FROM ledger_account";
$result = $conn->query($query);

if (!$result) {
    die("Error in SQL query: " . $conn->error);
}

$ledger_account = [];
if ($result->num_rows > 0) {
    $ledger_account = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "<div class='alert alert-warning'>No ledger accounts found.</div>";
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-5">Audit Working Paper</h2>

    <!-- Form to Select Findings for Each Ledger Account -->
    <form action="submit_audit.php" method="POST" class="bg-light p-4 rounded shadow-lg">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Ledger ID</th>
                        <th>Ledger Account Name</th>
                        <th>Findings</th>
                        <th>Query Amount</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Example array of ledger accounts (replace with your database query if needed)
                    $ledger_account = [
                        ['id' => 1, 'name' => 'Cash Account'],
                        ['id' => 2, 'name' => 'Receivables'],
                        ['id' => 3, 'name' => 'Payables'],
                        // Add more ledger accounts here
                    ];
                   
                    foreach ($ledger_account as $account) {
                        echo '
                        <tr>
                            <td class="text-center">' . $account['id'] . '</td>
                            <td>' . htmlspecialchars($account['name']) . '</td>
                            <td>
                                <select class="form-control" name="ledger_account_' . $account['id'] . '_finding">
                                    <option value="No Issues">No Issues</option>
                                    <option value="Minor Discrepancy">Minor Discrepancy</option>
                                    <option value="Significant Discrepancy">Significant Discrepancy</option>
                                    <option value="Missing Data">Missing Data</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="ledger_account_' . $account['id'] . '_amount" placeholder="Enter query amount">
                            </td>
                            <td>
                                <textarea class="form-control" name="ledger_account_' . $account['id'] . '_remark" rows="2" placeholder="Enter remarks"></textarea>
                            </td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg mt-3">Submit Audit Findings</button>
        </div>
    </form>

    <!-- Links to Other Pages -->
    <div class="text-center mt-5">
        <a href="Audit findings_report.php" class="btn btn-info btn-lg m-2">
            <i class="bi bi-file-earmark-text"></i> Submit Audit Findings Report
        </a>
        <a href="findings.php" class="btn btn-info btn-lg m-2">
            <i class="bi bi-file-earmark-text"></i> Go to Findings Page
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include('footer.php'); ?>
<?php
include('header.php');
include('db.php');

// Fetch ledger accounts
$query = "SELECT id, name FROM ledger_account"; // Ensure the table name is correct
$result = $conn->query($query);

if (!$result) {
    die("SQL Query Failed: " . $conn->error); // Debugging message
}

$ledger_account = [];
if ($result && $result->num_rows > 0) {
    $ledger_account = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "<div class='alert alert-warning'>No ledger accounts found.</div>";
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-5">Audit Working Paper</h2>

    <!-- Display ledger accounts -->
    <form action="submit_audit.php" method="POST" class="bg-light p-4 rounded shadow-lg">
        <?php foreach ($ledger_account as $account): ?>
            <div class="form-group mb-4">
                <label for="ledger_account_<?= $account['id'] ?>" class="font-weight-bold">
                    Ledger Account: <?= htmlspecialchars($account['name']) ?>
                </label>
                <select class="form-control" id="ledger_account_<?= $account['id'] ?>" name="ledger_account_<?= $account['id'] ?>_finding">
                    <option value="No Issues">No Issues</option>
                    <option value="Minor Discrepancy">Minor Discrepancy</option>
                    <option value="Significant Discrepancy">Significant Discrepancy</option>
                    <option value="Missing Data">Missing Data</option>
                </select>
            </div>
        <?php endforeach; ?>

        <!-- Custom Findings Section -->
        <div class="form-group mb-4">
            <label for="custom_findings" class="font-weight-bold">Custom Findings (Optional)</label>
            <textarea class="form-control" id="custom_findings" name="custom_findings" rows="4" placeholder="Enter additional comments or findings here..."></textarea>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg m-2">Submit Audit Findings</button>
        </div>
    </form>

    <!-- Link to Findings Page -->
    <div class="text-center mt-5">
        <a href="findings.php" class="btn btn-info btn-lg">
            <i class="bi bi-file-earmark-text"></i> Go to Findings Page
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include('footer.php'); ?>


kkkkkkkkkkk
<?php
include('header.php');
include('db.php');

// Fetch ledger accounts from the database
$query = "SELECT id, name FROM ledger_account";  // Query the correct table (ledger_accounts)
$result = $conn->query($query);

// Check if there are any ledger accounts
if ($result->num_rows > 0) {
    $ledger_account = [];
    while ($row = $result->fetch_assoc()) {
        $ledger_account[] = $row;
    }
} else {
    echo "<div class='alert alert-danger'>No ledger accounts found.</div>";
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-5">Audit Working Paper</h2>

    <!-- Form to Select Findings for Each Ledger Account -->
    <form action="submit_audit.php" method="POST" class="bg-light p-4 rounded shadow-lg">
        <!-- Loop through each ledger account and allow the user to select findings -->
        <?php
        // Loop through each ledger account to generate the form field
        foreach ($ledger_account as $account) {
            echo '
            <div class="form-group mb-4">
                <label for="ledger_account_' . $account['id'] . '" class="font-weight-bold">Ledger Account: ' . $account['name'] . '</label>
                <select class="form-control" id="ledger_account_' . $account['id'] . '" name="ledger_account_' . $account['id'] . '_finding">
                    <option value="No Issues">No Issues</option>
                    <option value="Minor Discrepancy">Minor Discrepancy</option>
                    <option value="Significant Discrepancy">Significant Discrepancy</option>
                    <option value="Missing Data">Missing Data</option>
                </select>
            </div>';
        }
        ?>

        <!-- Custom Findings Section (Optional) -->
        <div class="form-group mb-4">
            <label for="custom_findings" class="font-weight-bold">Custom Findings (Optional)</label>
            <textarea class="form-control" id="custom_findings" name="custom_findings" rows="4" placeholder="Enter additional comments or findings here..."></textarea>
        </div>

        
<!-- Submit Button -->
<div class="text-center mt-5">
        <a href="Audit findings_report.php" class="btn btn-info btn-lg">
            <i class="bi bi-file-earmark-text"></i> Submit Audit Findings  Report
        </a>
    </div></form>
    <!-- Link to Direct User to Findings Page -->
    <div class="text-center mt-5">
        <a href="findings.php" class="btn btn-info btn-lg">
            <i class="bi bi-file-earmark-text"></i> Go to Findings Page
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include('footer.php'); ?>
kkkkkkkkkkk
<?php
include('header.php');
include('db.php');

// Fetch ledger accounts from the database
$query = "SELECT id, name FROM ledger_account"; // Use the correct table name here
$result = $conn->query($query);

// Check for query errors
if ($result === false) {
    echo "<div class='alert alert-danger'>Error fetching ledger accounts: " . $conn->error . "</div>";
    $ledger_accounts = [];
} elseif ($result->num_rows > 0) {
    $ledger_accounts = [];
    while ($row = $result->fetch_assoc()) {
        $ledger_accounts[] = $row;
    }
} else {
    echo "<div class='alert alert-warning'>No ledger accounts found.</div>";
    $ledger_accounts = [];
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-5">Audit Working Paper</h2>

    <!-- Stylish Table -->
    <form action="submit_audit.php" method="POST" class="bg-light p-4 rounded shadow-lg">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Ledger ID</th>
                        <th>Ledger Account Name</th>
                        <th>Findings</th>
                        <th>Queries Amount</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($ledger_accounts)) {
                        foreach ($ledger_accounts as $account) {
                            echo '
                            <tr>
                                <td class="text-center">' . $account['id'] . '</td>
                                <td>' . htmlspecialchars($account['name']) . '</td>
                                <td>
                                    <select class="form-control" name="ledger_account_' . $account['id'] . '_finding">
                                        <option value="No Issues">No Issues</option>
                                        <option value="Minor Discrepancy">Minor Discrepancy</option>
                                        <option value="Significant Discrepancy">Significant Discrepancy</option>
                                        <option value="Missing Data">Missing Data</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="ledger_account_' . $account['id'] . '_amount" placeholder="Enter query amount">
                                </td>
                                <td>
                                    <textarea class="form-control" name="ledger_account_' . $account['id'] . '_remark" rows="2" placeholder="Enter remarks"></textarea>
                                </td>
                            </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center">No ledger accounts available.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Submit Button -->
         
        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg m-3">Submit Audit Findings</button>
        </div>
    </form>

    <!-- Link to Direct User to Findings Page -->
    <div class="text-center mt-5">
        <a href="findings.php" class="btn btn-info btn-lg">
            <i class="bi bi-file-earmark-text"></i> Go to Findings Page
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include('footer.php'); ?>
