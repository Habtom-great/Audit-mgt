kkkkkkkkkkkk

<?php
// Assuming you're fetching the client list from the database
include('db.php'); // Modify with your DB connection details

?>

<!-- HTML Form -->
<div class="container">
    <h2 class="content-header">Audit Findings for Financial Review</h2>

    <!-- Company Selection Dropdown -->
    <div class="form-group">
        <label for="clientSelect">Select Client for Audit:</label>
        <select id="clientSelect" class="dropdown-select" onchange="populateCompanyDetails()">
            <option value="">Select Company</option>
            <?php foreach ($clientList as $client) { ?>
                <option value="<?= $client['id']; ?>" data-name="<?= $client['company_name']; ?>" data-tin="<?= $client['tin_no']; ?>">
                    <?= $client['company_name'] . " - " . $client['tin_no']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <!-- Display selected company details -->
    <div id="clientDetails" style="display:none;">
        <p><strong>Company Name: </strong><span id="companyName"></span></p>
        <p><strong>Company TIN: </strong><span id="TIN_no"></span></p>
    </div>

    <!-- Audit Item Selection Dropdown -->
    <div class="form-group">
        <label for="auditItemSelect">Select Audit Item:</label>
        <select id="auditItemSelect" class="dropdown-select" onchange="populateAuditObjective()">
            <option value="">Select Item</option>
            <?php foreach ($auditItems as $item => $details) { ?>
                <option value="<?= $item; ?>"><?= $item; ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Audit Details -->
    <div id="auditDetails" style="display:none;">
        <p><strong>Audit Objective: </strong><span id="auditObjective"></span></p>
        <p><strong>Audit Findings: </strong><span id="auditFindings"></span></p>
        <p><strong>Action Required: </strong><span id="actionRequired"></span></p>
    </div>

    <!-- Audit Table -->
    <table class="audit-table" id="auditTable">
        <thead>
            <tr>
                <th>Item</th>
                <th>Audit Objective</th>
                <th>Audit Findings</th>
                <th>Action Required</th>
                <th>Audit Status</th>
                <th>Next Steps</th>
                <th>Amount Differences to Be Reviewed and Corrected</th>
                <th>Reviewer Name</th>
                <th>Reviewer Sign</th>
                <th>Additional Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr class="audit-item-row">
                <td>
                    <!-- Select dropdown for "Item" -->
                    <select class="form-control" onchange="updateItemDetails(this)">
                        <option value="Cash">Cash</option>
                        <option value="Accounts Receivable">Accounts Receivable</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Audit objective will appear here" id="auditObjectiveField">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Audit findings will appear here" id="auditFindingsField">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Action required will appear here" id="actionRequiredField">
                </td>
                <td>
                    <select class="dropdown-select">
                        <option value="unresolved">Unresolved</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Next steps">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Amount difference">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Reviewer name">
                </td>
                <td>
                    <input type="checkbox" class="form-check-input"> Signed
                </td>
                <td>
                    <textarea class="form-control" placeholder="Add remarks here..." rows="2"></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <button type="button" class="btn btn-add-row" onclick="addRow()">Add New Row</button>
</div>

<script>
    // Populate company details when client is selected
    function populateCompanyDetails() {
        const selectElement = document.getElementById('clientSelect');
        const companyDetails = selectElement.selectedOptions[0].dataset;
        
        document.getElementById('companyName').innerText = companyDetails.name || '';
        document.getElementById('TIN_no').innerText = companyDetails.tin || '';

        document.getElementById('clientDetails').style.display = companyDetails.name ? 'block' : 'none';
    }

    // Populate audit details when an audit item is selected
    function populateAuditObjective() {
        const auditItems = <?php echo json_encode($auditItems); ?>;
        const selectedItem = document.getElementById('auditItemSelect').value;

        if (selectedItem && auditItems[selectedItem]) {
            document.getElementById('auditObjective').innerText = auditItems[selectedItem].objective;
            document.getElementById('auditFindings').innerText = auditItems[selectedItem].findings;
            document.getElementById('actionRequired').innerText = auditItems[selectedItem].action_required;

            document.getElementById('auditDetails').style.display = 'block';
        } else {
            document.getElementById('auditDetails').style.display = 'none';
        }
    }

    // Add row to the audit table
    function addRow() {
        const table = document.getElementById("auditTable").getElementsByTagName('tbody')[0];
        const newRow = table.insertRow(table.rows.length);

        // Define cells for the new row
        const cell1 = newRow.insertCell(0);
        const cell2 = newRow.insertCell(1);
        const cell3 = newRow.insertCell(2);
        const cell4 = newRow.insertCell(3);
        const cell5 = newRow.insertCell(4);
        const cell6 = newRow.insertCell(5);
        const cell7 = newRow.insertCell(6);
        const cell8 = newRow.insertCell(7);
        const cell9 = newRow.insertCell(8);
        const cell10 = newRow.insertCell(9);

        // Insert content into the new row's cells
        cell1.innerHTML = '<select class="form-control"><option value="Cash">Cash</option><option value="Accounts Receivable">Accounts Receivable</option></select>';
        cell2.innerHTML = '<input type="text" class="form-control" placeholder="Audit objective will appear here">';
        cell3.innerHTML = '<input type="text" class="form-control" placeholder="Audit findings will appear here">';
        cell4.innerHTML = '<input type="text" class="form-control" placeholder="Action required will appear here">';
        cell5.innerHTML = '<select class="dropdown-select"><option value="unresolved">Unresolved</option><option value="resolved">Resolved</option></select>';
        cell6.innerHTML = '<input type="text" class="form-control" placeholder="Next steps">';
        cell7.innerHTML = '<input type="text" class="form-control" placeholder="Amount difference">';
        cell8.innerHTML = '<input type="text" class="form-control" placeholder="Reviewer name">';
        cell9.innerHTML = '<input type="checkbox" class="form-check-input"> Signed';
        cell10.innerHTML = '<textarea class="form-control" placeholder="Add remarks here..." rows="2"></textarea>';
    }
</script>

<?php include('footer.php'); ?>
<?php include('header.php'); ?>
kkkk
<?php include('header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    /* Standard Audit List Page Styling */
    .container {
        max-width: 1200px;
    }

    .content-header {
        font-size: 2rem;
        color: #224abe;
        font-weight: bold;
        text-align: center;
        margin-top: 30px;
        margin-bottom: 50px;
    }

    .audit-card {
        margin-bottom: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .audit-card-header {
        background-color: #28a745;
        color: white;
        padding: 15px;
        font-weight: bold;
        font-size: 1.3rem;
    }

    .audit-card-body {
        padding: 25px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .audit-section-title {
        font-size: 1.4rem;
        color: #224abe;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .audit-item {
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }

    .audit-item h5 {
        font-size: 1.2rem;
        color: #333;
        margin-bottom: 5px;
    }

    .audit-checklist input[type="checkbox"] {
        margin-right: 10px;
    }

    .remarks-area {
        margin-top: 15px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .remarks-area h5 {
        font-size: 1.1rem;
        color: #224abe;
    }

    .btn-custom {
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1.1rem;
    }

    .btn-outline-info {
        color: #007bff;
        border-color: #007bff;
    }

    .btn-outline-info:hover {
        background-color: #007bff;
        color: white;
    }

    /* Mobile Responsiveness */
    @media (max-width: 767px) {
        .content-header {
            font-size: 1.5rem;
        }

        .audit-card {
            margin-bottom: 20px;
        }

        .audit-section-title {
            font-size: 1.1rem;
        }
    }

    .audit-table td, .audit-table th {
        padding: 8px;
        text-align: left;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table th {
        background-color: #f2f2f2;
        color: #224abe;
    }

    .audit-signature {
        display: flex;
        justify-content: space-between;
    }

    .audit-signature .signature-section {
        width: 45%;
    }
</style>

<div class="container">
    <h2 class="content-header">Ongoing Audits</h2>

    <!-- List of Ongoing Audits -->
    <div class="row">
        <!-- Example Audit Card 1 -->
        <div class="col-md-6">
            <div class="card audit-card">
                <div class="card-header audit-card-header">
                    Audit #12345 - Financial Review
                </div>
                <div class="card-body">
                    <div class="audit-section-title">Balance Sheet Items</div>
                    <div class="audit-item">
                        <h5>Item #1: Cash</h5>
                        <p>Ensure that cash balances are accurate and reflect the correct amounts as per the records.</p>
                    </div>
                    <div class="audit-item">
                        <h5>Item #2: Accounts Receivable</h5>
                        <p>Review all outstanding balances and confirm their validity.</p>
                    </div>

                    <div class="audit-section-title">Profit & Loss Items</div>
                    <div class="audit-item">
                        <h5>Item #1: Revenue</h5>
                        <p>Confirm that all income streams are appropriately reported in the financial statements.</p>
                    </div>
                    <div class="audit-item">
                        <h5>Item #2: Expenses</h5>
                        <p>Verify that all expenses are valid and correspond to the recorded financial transactions.</p>
                    </div>

                    <div class="audit-section-title">Other Audit Items</div>
                    <div class="audit-item">
                        <h5>Item #1: Compliance</h5>
                        <p>Ensure the company adheres to all regulatory requirements for financial reporting.</p>
                    </div>
                    <div class="audit-item">
                        <h5>Item #2: Tax Returns</h5>
                        <p>Verify that tax filings are accurate and up-to-date with the tax authority.</p>
                    </div>

                    <div class="audit-section-title">Audit Checklist</div>
                    <div class="audit-item audit-checklist">
                        <label><input type="checkbox" id="checklist1" /> Verify Balance Sheet Accuracy</label>
                        <p>Ensure all balance sheet items are properly classified and accounted for.</p>
                    </div>
                    <div class="audit-item audit-checklist">
                        <label><input type="checkbox" id="checklist2" /> Inspect Profit & Loss Accuracy</label>
                        <p>Check that all P&L items reflect accurate financial performance.</p>
                    </div>

                    <div class="audit-section-title">Audit Findings</div>
                    <div class="audit-item">
                        <h5>Finding #1: Tax Filing Issue</h5>
                        <p>Discrepancies found in tax filings, needs further investigation.</p>
                        <table class="audit-table">
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Differences</th>
                                <th>Resolved/Unresolved</th>
                                <th>Further Steps</th>
                            </tr>
                            <tr>
                                <td>Discrepancy in Tax Filing</td>
                                <td>$5,000</td>
                                <td>$500 Difference</td>
                                <td>Unresolved</td>
                                <td>Investigate with tax authority</td>
                            </tr>
                        </table>
                    </div>

                    <div class="audit-section-title">Assessment and Identification</div>
                    <div class="audit-item">
                        <h5>Assessment: Moderate Risk</h5>
                        <p>After review, this audit reveals a moderate level of risk due to reporting errors.</p>
                    </div>

                    <!-- Signature Section -->
                    <div class="audit-signature">
                        <div class="signature-section">
                            <h5>Auditor's Signature</h5>
                            <input type="text" class="form-control" placeholder="Auditor's Name">
                            <small>Date: <input type="date" class="form-control"></small>
                        </div>
                        <div class="signature-section">
                            <h5>Supervisor's Signature</h5>
                            <input type="text" class="form-control" placeholder="Supervisor's Name">
                            <small>Date: <input type="date" class="form-control"></small>
                        </div>
                    </div>

                    <!-- Financial Year -->
                    <div class="audit-item">
                        <h5>Financial Year: 2024</h5>
                    </div>

                    <!-- Additional Information -->
                    <div class="remarks-area">
                        <h5>Additional Remarks</h5>
                        <textarea class="form-control" rows="4" placeholder="Add your remarks here..."></textarea>
                    </div>

                    <a href="audit_details.php?audit_id=12345" class="btn btn-outline-info btn-custom">View Full Details</a>
                </div>
            </div>
        </div>

        <!-- Example Audit Card 2 -->
        <div class="col-md-6">
            <div class="card audit-card">
                <div class="card-header audit-card-header">
                    Audit #12346 - Operational Efficiency
                </div>
                <div class="card-body">
                    <div class="audit-section-title">Balance Sheet Items</div>
                    <div class="audit-item">
                        <h5>Item #1: Inventory</h5>
                        <p>Verify that the inventory balance is accurately reported and substantiated by physical counts.</p>
                    </div>
                    <div class="audit-item">
                        <h5>Item #2: Liabilities</h5>
                        <p>Confirm that all liabilities are properly recorded and classified.</p>
                    </div>

                    <div class="audit-section-title">Profit & Loss Items</div>
                    <div class="audit-item">
                        <h5>Item #1: Cost of Goods Sold</h5>
                        <p>Review the COGS calculation and ensure it is accurately applied.</p>
                    </div>

                    <!-- More content for this audit -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
kkkkkkkkk


kkkkk
<?php include('header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    .form-section-title {
        font-size: 1.5rem;
        color: #224abe;
        font-weight: bold;
        margin-top: 20px;
    }

    .form-row {
        margin-bottom: 15px;
    }

    .form-control {
        border-radius: 5px;
        padding: 10px;
    }

    .btn-custom {
        background-color: #28a745;
        color: #fff;
        border-radius: 25px;
        font-weight: bold;
        padding: 10px 20px;
    }
</style>

<div class="container mt-4">
    <h2 class="form-section-title">Balance Sheet Items</h2>

    <form action="process_audit.php" method="POST">
        <!-- Example Item -->
        <div class="form-row">
            <label for="account-select-1" class="form-label">Account:</label>
            <select id="account-select-1" name="account[]" class="form-control">
                <option value="">Select an account</option>
                <option value="cash">Cash</option>
                <option value="accounts_receivable">Accounts Receivable</option>
                <option value="inventory">Inventory</option>
                <option value="prepaid_expenses">Prepaid Expenses</option>
                <option value="ppe">Property, Plant, and Equipment</option>
            </select>
        </div>

        <div class="form-row">
            <label for="discrepancy-1" class="form-label">Discrepancy:</label>
            <textarea id="discrepancy-1" name="discrepancy[]" class="form-control" placeholder="Describe the discrepancy..."></textarea>
        </div>

        <div class="form-row">
            <label for="amount-1" class="form-label">Amount:</label>
            <input type="text" id="amount-1" name="amount[]" class="form-control" placeholder="Enter the discrepancy amount">
        </div>

        <div class="form-row">
            <label>Status:</label><br>
            <label><input type="radio" name="status[1]" value="resolved"> Resolved</label>
            <label><input type="radio" name="status[1]" value="unresolved"> Unresolved</label>
            <label><input type="radio" name="status[1]" value="pending"> Pending</label>
        </div>

        <hr>

        <!-- Repeatable Section for Other Accounts -->
        <div id="additional-items"></div>

        <button type="button" class="btn btn-outline-info" id="add-more-items">Add More Items</button>
        <button type="submit" class="btn btn-custom">Submit Audit</button>
    </form>
</div>

<script>
    document.getElementById('add-more-items').addEventListener('click', function() {
        const itemIndex = document.querySelectorAll('.form-row').length / 4 + 1;
        const newItem = `
            <div class="form-row">
                <label for="account-select-${itemIndex}" class="form-label">Account:</label>
                <select id="account-select-${itemIndex}" name="account[]" class="form-control">
                    <option value="">Select an account</option>
                    <option value="cash">Cash</option>
                    <option value="accounts_receivable">Accounts Receivable</option>
                    <option value="inventory">Inventory</option>
                    <option value="prepaid_expenses">Prepaid Expenses</option>
                    <option value="ppe">Property, Plant, and Equipment</option>
                </select>
            </div>
            <div class="form-row">
                <label for="discrepancy-${itemIndex}" class="form-label">Discrepancy:</label>
                <textarea id="discrepancy-${itemIndex}" name="discrepancy[]" class="form-control" placeholder="Describe the discrepancy..."></textarea>
            </div>
            <div class="form-row">
                <label for="amount-${itemIndex}" class="form-label">Amount:</label>
                <input type="text" id="amount-${itemIndex}" name="amount[]" class="form-control" placeholder="Enter the discrepancy amount">
            </div>
            <div class="form-row">
                <label>Status:</label><br>
                <label><input type="radio" name="status[${itemIndex}]" value="resolved"> Resolved</label>
                <label><input type="radio" name="status[${itemIndex}]" value="unresolved"> Unresolved</label>
                <label><input type="radio" name="status[${itemIndex}]" value="pending"> Pending</label>
            </div>
            <hr>`;
        document.getElementById('additional-items').insertAdjacentHTML('beforeend', newItem);
    });
</script>


kkkkk

<table class="audit-table">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Description</th>
            <th>Category</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Cash Discrepancy</td>
            <td>Balance Sheet</td>
            <td>Unresolved</td>
            <td>Discrepancy between ledger and bank statement</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Unreported Sales</td>
            <td>Profit and Loss</td>
            <td>Pending</td>
            <td>Revenue understated by $10,000</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Tax Filing Error</td>
            <td>Other Issues</td>
            <td>Unresolved</td>
            <td>Difference in declared and payable tax</td>
        </tr>
    </tbody>
</table>

kkkkkkk
<?php include('header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    .container {
        max-width: 1200px;
        margin: 50px auto;
    }
    .content-header {
        text-align: center;
        color: #224abe;
        font-weight: bold;
        margin-bottom: 30px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background-color: #f2f2f2;
        color: #224abe;
    }
    .dropdown, .amount-input, .status-radio {
        display: inline-block;
        margin-right: 10px;
    }
    .btn-submit {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn-submit:hover {
        background-color: #218838;
    }
</style>
<?php include('header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    .container {
        max-width: 1200px;
    }

    .content-header {
        font-size: 2rem;
        color: #224abe;
        font-weight: bold;
        text-align: center;
        margin-top: 30px;
        margin-bottom: 50px;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .audit-table th, .audit-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .audit-table th {
        background-color: #f2f2f2;
        color: #224abe;
    }

    .audit-table select, .audit-table input, .audit-table textarea {
        width: 100%;
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 0.9rem;
    }

    .status-options {
        display: flex;
        gap: 10px;
    }

    .btn-custom {
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1.1rem;
        margin-top: 20px;
    }
</style>

<div class="container">
    <h2 class="content-header">Ongoing Audit Records</h2>
    <table class="audit-table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Description</th>
                <th>Options</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><textarea placeholder="Enter description..."></textarea></td>
                <td>
                    <select>
                        <option value="" selected disabled>Select option</option>
                        <option value="Discrepancy">Discrepancy</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Pending Review">Pending Review</option>
                    </select>
                </td>
                <td><input type="text" placeholder="Enter amount..." /></td>
                <td>
                    <div class="status-options">
                        <label><input type="radio" name="status-1" value="Resolved" /> Resolved</label>
                        <label><input type="radio" name="status-1" value="Unresolved" /> Unresolved</label>
                        <label><input type="radio" name="status-1" value="Pending" /> Pending</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td><textarea placeholder="Enter description..."></textarea></td>
                <td>
                    <select>
                        <option value="" selected disabled>Select option</option>
                        <option value="Discrepancy">Discrepancy</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Pending Review">Pending Review</option>
                    </select>
                </td>
                <td><input type="text" placeholder="Enter amount..." /></td>
                <td>
                    <div class="status-options">
                        <label><input type="radio" name="status-2" value="Resolved" /> Resolved</label>
                        <label><input type="radio" name="status-2" value="Unresolved" /> Unresolved</label>
                        <label><input type="radio" name="status-2" value="Pending" /> Pending</label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <button class="btn btn-custom btn-primary">Save Changes</button>
</div>

<div class="container">
    <h2 class="content-header">Audit Findings Table</h2>
    <form action="process_audit.php" method="POST">
        <table class="table">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Description</th>
                    <th>Option</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 -->
                <tr>
                    <td>1</td>
                    <td>Discrepancy in Tax Filing</td>
                    <td>
                        <select class="dropdown" name="option_1">
                            <option value="Further Investigation">Further Investigation</option>
                            <option value="Follow Up">Follow Up</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="amount-input" name="amount_1" placeholder="Enter amount">
                    </td>
                    <td>
                        <label><input type="radio" name="status_1" value="Resolved"> Resolved</label>
                        <label><input type="radio" name="status_1" value="Unresolved"> Unresolved</label>
                        <label><input type="radio" name="status_1" value="Pending"> Pending</label>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr>
                    <td>2</td>
                    <td>Inaccuracies in Accounts Payable</td>
                    <td>
                        <select class="dropdown" name="option_2">
                            <option value="Further Investigation">Further Investigation</option>
                            <option value="Follow Up">Follow Up</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="amount-input" name="amount_2" placeholder="Enter amount">
                    </td>
                    <td>
                        <label><input type="radio" name="status_2" value="Resolved"> Resolved</label>
                        <label><input type="radio" name="status_2" value="Unresolved"> Unresolved</label>
                        <label><input type="radio" name="status_2" value="Pending"> Pending</label>
                    </td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>
        <button type="submit" class="btn-submit">Submit Audit Findings</button>
    </form>
</div>
