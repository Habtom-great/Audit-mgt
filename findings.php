
<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "audit_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch clients
$sqlClients = "SELECT company_name, tin_no FROM audit_clients";
$resultClients = $conn->query($sqlClients);
$clients = [];

if ($resultClients && $resultClients->num_rows > 0) {
    while ($row = $resultClients->fetch_assoc()) {
        $clients[] = $row;
    }
} else if (!$resultClients) {
    die("Error fetching clients: " . $conn->error);
}

// Fetch ledger accounts
$sqlLedger = "SELECT account_item, audit_objectives FROM ledger_account";
$resultLedger = $conn->query($sqlLedger);
$ledgerAccounts = [];

if ($resultLedger && $resultLedger->num_rows > 0) {
    while ($row = $resultLedger->fetch_assoc()) {
        $ledgerAccounts[] = $row;
    }
} else if (!$resultLedger) {
    die("Error fetching ledger accounts: " . $conn->error);
}

// Fetch audit findings linked to ledger accounts
$sqlAuditFindings = "SELECT account_item, audit_findings FROM audit_findings";
$resultAuditFindings = $conn->query($sqlAuditFindings);
$auditFindings = [];

if ($resultAuditFindings && $resultAuditFindings->num_rows > 0) {
    while ($row = $resultAuditFindings->fetch_assoc()) {
        $auditFindings[$row['account_item']][] = $row['audit_findings'];
    }
} else if (!$resultAuditFindings) {
    die("Error fetching audit findings: " . $conn->error);
}

// Fetch balance sheet accounts
$sqlBalanceSheet = "SELECT account_name, balance FROM balance_sheet_accounts";
$resultBalanceSheet = $conn->query($sqlBalanceSheet);
$balanceSheetAccounts = [];

if ($resultBalanceSheet && $resultBalanceSheet->num_rows > 0) {
    while ($row = $resultBalanceSheet->fetch_assoc()) {
        $balanceSheetAccounts[] = $row;
    }
} else if (!$resultBalanceSheet) {
    die("Error fetching balance sheet accounts: " . $conn->error);
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Findings Table</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        header { background-color: #343a40; color: white; text-align: center; padding: 15px; }
        .container { max-width: 100%; padding: 20px; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; }
        .table th { background-color: #f8f9fa; }
        footer { background-color: #343a40; color: white; text-align: center; padding: 10px; }
    </style>
</head>
<body>
    <header>
        <h2>Audit Findings for Financial Review</h2>
    </header>

    <div class="container">
        <!-- Client Selection -->
        <div class="form-group">
            <label for="clientSelect">Select Client for Audit:</label>
            <select id="clientSelect" class="form-control" onchange="populateClientDetails()">
                <option value="">Select Company</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= htmlspecialchars($client['company_name']); ?>" data-tin="<?= htmlspecialchars($client['tin_no']); ?>">
                        <?= htmlspecialchars($client['company_name']); ?> - <?= htmlspecialchars($client['tin_no']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Display Client Details -->
        <div id="clientDetails" style="margin-bottom: 20px;">
            <p><strong>Company Name:</strong> <span id="companyName"></span></p>
            <p><strong>Company TIN:</strong> <span id="TIN_no"></span></p>
        </div>

        <!-- Balance Sheet Accounts Table -->
        <h3>Balance Sheet Accounts</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($balanceSheetAccounts as $account): ?>
                    <tr>
                        <td><?= htmlspecialchars($account['account_name']); ?></td>
                        <td><?= htmlspecialchars($account['balance']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Audit Findings Table -->
        <h3>Audit Findings</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ledger Account</th>
                    <th>Audit Objective</th>
                    <th>Audit Findings</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Amount Differences</th>
                    <th>Additional Remarks</th>
                    <th>Action Required</th>
                    <th>Audit Status</th>
                    <th>Next Steps</th>
                    <th>Reviewer Name</th>
                    <th>Reviewer Sign</th>
                    <th>Client Representative</th>
                    <th>Client's Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <select class="form-control account-item-dropdown">
                            <option value="">Select Account Item</option>
                            <?php foreach ($ledgerAccounts as $account): ?>
                                <option value="<?= htmlspecialchars($account['account_item']); ?>">
                                    <?= htmlspecialchars($account['account_item']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" class="form-control audit-objective" readonly></td>
                    <td>
                        <select class="form-control audit-findings-dropdown">
                            <option value="">Select Finding</option>
                        </select>
                        <textarea class="form-control mt-2" placeholder="Or write custom finding..." rows="2"></textarea>
                    </td>
                    <td><input type="text" class="form-control" placeholder="Reference"></td>
                    <td><input type="date" class="form-control"></td>
                    <td><input type="text" class="form-control" placeholder="Amount difference"></td>
                    <td><textarea class="form-control" placeholder="Remarks" rows="2"></textarea></td>
                    <td>
                        <select class="form-control">
                            <option value="">Select Action</option>
                            <option value="resolved">Resolved</option>
                            <option value="pending">Pending</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control">
                            <option value="unresolved">Unresolved</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </td>
                    <td><textarea class="form-control" placeholder="Next Steps..." rows="2"></textarea></td>
                    <td><input type="text" class="form-control" placeholder="Reviewer Name"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="text" class="form-control" placeholder="Representative Name"></td>
                    <td><textarea class="form-control" placeholder="Client's Comment" rows="2"></textarea></td>
                    <td>
                        <button class="btn btn-success" onclick="saveRow(this)">Save</button>
                        <button class="btn btn-warning" onclick="editRow(this)">Edit</button>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button class="btn btn-primary btn-add-row" onclick="addRow()">Add New Row</button>
    </div>

    <footer>
        <p>&copy; 2025 Audit Solutions</p>
    </footer>

    <script>
        const ledgerAccounts = <?= json_encode($ledgerAccounts); ?>;
        const auditFindings = <?= json_encode($auditFindings); ?>;

        document.addEventListener('change', (event) => {
            if (event.target.classList.contains('account-item-dropdown')) {
                const selectedValue = event.target.value;
                const row = event.target.closest('tr');
                const findingsDropdown = row.querySelector('.audit-findings-dropdown');
                const objectiveField = row.querySelector('.audit-objective');
                const findings = auditFindings[selectedValue] || [];

                const account = ledgerAccounts.find(item => item.account_item === selectedValue);
                objectiveField.value = account ? account.audit_objectives || '' : '';

                findingsDropdown.innerHTML = '<option value="">Select Finding</option>';
                findings.forEach(finding => {
                    const option = document.createElement('option');
                    option.value = finding;
                    option.textContent = finding;
                    findingsDropdown.appendChild(option);
                });
            }
        });

        function addRow() {
            const table = document.querySelector('table tbody');
            const newRow = table.rows[0].cloneNode(true);
            newRow.querySelectorAll('input, textarea, select').forEach(input => {
                input.value = '';
                input.disabled = false;
            });
            table.appendChild(newRow);
        }

        function saveRow(button) {
            const row = button.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = true;
            });
        }

        function editRow(button) {
            const row = button.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
            });
        }

        function deleteRow(button) {
            button.closest('tr').remove();
        }
    </script>
</body>
</html>

kkkkkkkk
<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "audit_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch clients
$sqlClients = "SELECT company_name, tin_no FROM audit_clients";
$resultClients = $conn->query($sqlClients);
$clients = [];

if ($resultClients && $resultClients->num_rows > 0) {
    while ($row = $resultClients->fetch_assoc()) {
        $clients[] = $row;
    }
} else if (!$resultClients) {
    die("Error fetching clients: " . $conn->error);
}

// Fetch ledger accounts
$sqlLedger = "SELECT account_item, audit_objectives FROM ledger_account";
$resultLedger = $conn->query($sqlLedger);
$ledgerAccounts = [];

if ($resultLedger && $resultLedger->num_rows > 0) {
    while ($row = $resultLedger->fetch_assoc()) {
        $ledgerAccounts[] = $row;
    }
} else if (!$resultLedger) {
    die("Error fetching ledger accounts: " . $conn->error);
}

// Fetch audit findings linked to ledger accounts
$sqlAuditFindings = "SELECT account_item, audit_findings FROM audit_findings";
$resultAuditFindings = $conn->query($sqlAuditFindings);
$auditFindings = [];

if ($resultAuditFindings && $resultAuditFindings->num_rows > 0) {
    while ($row = $resultAuditFindings->fetch_assoc()) {
        $auditFindings[$row['account_item']][] = $row['audit_findings'];
    }
} else if (!$resultAuditFindings) {
    die("Error fetching audit findings: " . $conn->error);
}

// Fetch balance sheet accounts
$sqlBalanceSheet = "SELECT account_name, balance FROM balance_sheet_accounts";
$resultBalanceSheet = $conn->query($sqlBalanceSheet);
$balanceSheetAccounts = [];

if ($resultBalanceSheet && $resultBalanceSheet->num_rows > 0) {
    while ($row = $resultBalanceSheet->fetch_assoc()) {
        $balanceSheetAccounts[] = $row;
    }
} else if (!$resultBalanceSheet) {
    die("Error fetching balance sheet accounts: " . $conn->error);
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Findings Table</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        header { background-color: #343a40; color: white; text-align: center; padding: 15px; }
        .container { max-width: 100%; padding: 20px; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; }
        .table th { background-color: #f8f9fa; }
        footer { background-color: #343a40; color: white; text-align: center; padding: 10px; }
    </style>
</head>
<body>
    <header>
        <h2>Audit Findings for Financial Review</h2>
    </header>

    <div class="container">
        <!-- Client Selection -->
        <div class="form-group">
            <label for="clientSelect">Select Client for Audit:</label>
            <select id="clientSelect" class="form-control" onchange="populateClientDetails()">
                <option value="">Select Company</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= htmlspecialchars($client['company_name']); ?>" data-tin="<?= htmlspecialchars($client['tin_no']); ?>">
                        <?= htmlspecialchars($client['company_name']); ?> - <?= htmlspecialchars($client['tin_no']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Display Client Details -->
        <div id="clientDetails" style="margin-bottom: 20px;">
            <p><strong>Company Name:</strong> <span id="companyName"></span></p>
            <p><strong>Company TIN:</strong> <span id="TIN_no"></span></p>
        </div>

 
      
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ledger Account</th>
                    <th>Audit Objective</th>
                    <th>Audit Findings</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Amount Differences</th>
                    <th>Additional Remarks</th>
                    <th>Action Required</th>
                    <th>Audit Status</th>
                    <th>Next Steps</th>
                    <th>Reviewer Name</th>
                    <th>Reviewer Sign</th>
                    <th>Client Representative</th>
                    <th>Client's Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <select class="form-control account-item-dropdown">
                            <option value="">Select Account Item</option>
                            <?php foreach ($ledgerAccounts as $account): ?>
                                <option value="<?= htmlspecialchars($account['account_item']); ?>">
                                    <?= htmlspecialchars($account['account_item']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" class="form-control audit-objective" readonly></td>
                    <td>
                        <select class="form-control audit-findings-dropdown">
                            <option value="">Select Finding</option>
                        </select>
                        <textarea class="form-control mt-2" placeholder="Or write custom finding..." rows="2"></textarea>
                    </td>
                    <td><input type="text" class="form-control" placeholder="Reference"></td>
                    <td><input type="date" class="form-control"></td>
                    <td><input type="text" class="form-control" placeholder="Amount difference"></td>
                    <td><textarea class="form-control" placeholder="Remarks" rows="2"></textarea></td>
                    <td>
                        <select class="form-control">
                            <option value="">Select Action</option>
                            <option value="resolved">Resolved</option>
                            <option value="pending">Pending</option>
                        </select>
                    </td>

                    <td><textarea class="form-control" placeholder="Next Steps..." rows="2"></textarea></td>
                    <td><input type="text" class="form-control" placeholder="Reviewer Name"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="text" class="form-control" placeholder="Representative Name"></td>
                    <td><textarea class="form-control" placeholder="Client's Comment" rows="2"></textarea></td>
                    <td>
                        <button class="btn btn-success" onclick="saveRow(this)">Save</button>
                        <button class="btn btn-warning" onclick="editRow(this)">Edit</button>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <td>
    <select class="form-control representative-dropdown">
        <option value="">Select Representative</option>
        <script>
            clientRepresentatives.forEach(rep => {
                document.write(`
                    <option value="${rep.representative_name}">
                        ${rep.representative_name} (${rep.position})
                    </option>
                `);
            });
        </script>
    </select>
</td>
<div class="export-options">
    <button class="btn btn-info" onclick="exportToExcel()">Export to Excel</button>
    <button class="btn btn-success" onclick="exportToWord()">Export to Word</button>
    <button class="btn btn-primary" onclick="window.print()">Print</button>
</div>
<script>
function exportToExcel() {
    const table = document.querySelector('table');
    const data = new Blob([table.outerHTML], { type: 'application/vnd.ms-excel' });
    const url = window.URL.createObjectURL(data);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'audit_findings.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function exportToWord() {
    const table = document.querySelector('table');
    const data = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"></head>
        <body>${table.outerHTML}</body></html>`;
    const blob = new Blob([data], { type: 'application/msword' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'audit_findings.doc';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
        <button class="btn btn-primary btn-add-row" onclick="addRow()">Add New Row</button>
    </div>

    <footer>
        <p>&copy; 2025 Audit Solutions</p>
    </footer>
    <body>
    <script>
        function populateClientDetails() {
            const select = document.getElementById('clientSelect');
            const name = select.options[select.selectedIndex].value;
            const tin = select.options[select.selectedIndex].getAttribute('data-tin');
            document.getElementById('companyName').textContent = name;
            document.getElementById('TIN_no').textContent = tin;
        }
        </script>
    <script>
        
        const ledgerAccounts = <?= json_encode($ledgerAccounts); ?>;
        const auditFindings = <?= json_encode($auditFindings); ?>;

        document.addEventListener('change', (event) => {
            if (event.target.classList.contains('account-item-dropdown')) {
                const selectedValue = event.target.value;
                const row = event.target.closest('tr');
                const findingsDropdown = row.querySelector('.audit-findings-dropdown');
                const objectiveField = row.querySelector('.audit-objective');
                const findings = auditFindings[selectedValue] || [];

                const account = ledgerAccounts.find(item => item.account_item === selectedValue);
                objectiveField.value = account ? account.audit_objectives || '' : '';

                findingsDropdown.innerHTML = '<option value="">Select Finding</option>';
                findings.forEach(finding => {
                    const option = document.createElement('option');
                    option.value = finding;
                    option.textContent = finding;
                    findingsDropdown.appendChild(option);
                });
            }
        });

        function addRow() {
            const table = document.querySelector('table tbody');
            const newRow = table.rows[0].cloneNode(true);
            newRow.querySelectorAll('input, textarea, select').forEach(input => {
                input.value = '';
                input.disabled = false;
            });
            table.appendChild(newRow);
        }

        function saveRow(button) {
            const row = button.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = true;
            });
        }

        function editRow(button) {
            const row = button.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
            });
        }

        function deleteRow(button) {
            button.closest('tr').remove();
        }
    </script>
</body>
</html>

kkkkkkkkkk
<?php
// Sample data for Audit Items, common findings, actions, and employee positions
$auditItems = [
    "Cash" => [
        "objective" => "Ensure that cash balances are accurate and reflect the correct amounts as per the records."
    ],
    "Accounts Receivable" => [
        "objective" => "Review all outstanding balances and confirm their validity."
    ],
    "Inventory" => [
        "objective" => "Check inventory levels against physical stock and reports."
    ],
];

$commonFindings = [
    "Cash discrepancy identified",
    "Outstanding balance verification required",
    "Inventory stock mismatch",
    "Audit review completed",
    "Data inconsistency observed"
];

$commonActions = [
    "Conduct detailed verification",
    "Request supporting documents",
    "Investigate discrepancies",
    "Update records accordingly",
    "Reconcile balances"
];

$employeePositions = [
    "Accountant",
    "Senior Accountant",
    "Finance Manager",
    "CEO",
    "Owner",
    "Others"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Findings Table</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        header {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
        }
        .container {
            max-width: 100%;
            padding: 20px;
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .btn-add-row {
            margin-top: 15px;
            background-color: #007bff;
            color: white;
            padding: 10px;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h2>Audit Findings for Financial Review</h2>
    </header>

    <div class="container">
        <div class="form-group">
            <label for="clientSelect">Select Client for Audit:</label>
            <select id="clientSelect" class="form-control" onchange="populateClientDetails()">
                <option value="">Select Company</option>
                <option value="ABC Corp" data-tin="1234567890">ABC Corp - 1234567890</option>
                <option value="XYZ Ltd" data-tin="9876543210">XYZ Ltd - 9876543210</option>
            </select>
        </div>
        <div id="clientDetails">
            <p><strong>Company Name:</strong> <span id="companyName"></span></p>
            <p><strong>Company TIN:</strong> <span id="TIN_no"></span></p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Account Item</th>
                    <th>Audit Objective</th>
                    <th>Audit Findings</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Amount Differences</th>
                    <th>Additional Remarks</th>
                    <th>Action Required</th>
                    <th>Audit Status</th>
                    <th>Next Steps</th>
                    <th>Reviewer Name</th>
                    <th>Reviewer Sign</th>
                    <th>Client Representative</th>
                    <th>Client's Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select class="form-control" onchange="updateAuditObjective(this)">
                            <option value="">Select Item</option>
                            <?php foreach ($auditItems as $item => $details): ?>
                                <option value="<?= $item; ?>"><?= $item; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><textarea class="form-control audit-objective" placeholder="Objective..." rows="2"></textarea></td>
                    <td>
                        <select class="form-control">
                            <option value="">Select Finding</option>
                            <?php foreach ($commonFindings as $finding): ?>
                                <option value="<?= $finding; ?>"><?= $finding; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" class="form-control" placeholder="Custom finding">
                    </td>
                    <td><input type="text" class="form-control" placeholder="Reference"></td>
                    <td><input type="date" class="form-control"></td>
                    <td><input type="text" class="form-control" placeholder="Amount difference"></td>
                    <td><textarea class="form-control" placeholder="Remarks" rows="2"></textarea></td>
                    <td>
                        <select class="form-control">
                            <option value="">Select Action</option>
                            <?php foreach ($commonActions as $action): ?>
                                <option value="<?= $action; ?>"><?= $action; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="form-control">
                            <option value="unresolved">Unresolved</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control">
                            <option value="action">Take action</option>
                            <option value="no-action">No action needed</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" placeholder="Reviewer Name"></td>
                    <td><input type="checkbox"> Signed</td>
                    <td>
                        <input type="text" class="form-control" placeholder="Representative Name">
                        <select class="form-control">
                            <option value="">Select Position</option>
                            <?php foreach ($employeePositions as $position): ?>
                                <option value="<?= $position; ?>"><?= $position; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><textarea class="form-control" placeholder="Client's Comment" rows="2"></textarea></td>
                    <td>
                        <button class="btn btn-success" onclick="saveRow(this)">Save</button>
                        <button class="btn btn-warning" onclick="editRow(this)">Edit</button>
                        <button class="btn btn-danger" onclick="deleteRow(this)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button class="btn btn-primary btn-add-row" onclick="addRow()">Add New Row</button>
    </div>

    <footer>
        <p>&copy; 2025 Audit Solutions</p>
    </footer>

    <script>
        function populateClientDetails() {
            const select = document.getElementById('clientSelect');
            const name = select.options[select.selectedIndex].value;
            const tin = select.options[select.selectedIndex].getAttribute('data-tin');
            document.getElementById('companyName').textContent = name;
            document.getElementById('TIN_no').textContent = tin;
        }

        function updateAuditObjective(select) {
            const objectiveInput = select.closest('tr').querySelector('.audit-objective');
            const objectives = <?= json_encode($auditItems); ?>;
            objectiveInput.value = objectives[select.value]?.objective || '';
        }

        function addRow() {
            const table = document.querySelector('table tbody');
            const newRow = table.rows[0].cloneNode(true);
            newRow.querySelectorAll('input, textarea, select').forEach(input => {
                input.value = '';
                input.disabled = false;
            });
            table.appendChild(newRow);
        }

        function saveRow(button) {
            const row = button.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = true;
            });
            console.log('Row saved.');
        }

        function editRow(button) {
            const row = button.closest('tr');
            row.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
            });
        }

        function deleteRow(button) {
            button.closest('tr').remove();
        }
    </script>
</body>
</html>
