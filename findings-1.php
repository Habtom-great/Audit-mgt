<!DOCTYPE html>
<html lang="en">

<head>
                 <meta charset="UTF-8">
                 <title>Audit Findings Register</title>
                 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                 <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
                 <style>
                 /* ... other styles ... */
                 th:first-child,
                 td:first-child {
                                  width: 6px;
                                  max-width: 6px;
                                  text-align: center;
                 }

                 .save-btn {
                                  margin-top: 5px;
                 }

                 body {
                                  background-color: #f8f9fa;
                                  margin: 0;
                                  padding: 20px;
                 }

                 .container-full {
                                  max-width: 100%;
                                  margin: auto;
                                  background-color: white;
                                  padding: 20px;
                                  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                 }

                 h2 {
                                  text-align: center;
                                  color: #003366;
                                  margin-bottom: 20px;
                 }

                 th {
                                  background-color: #003366;
                                  color: white;
                                  text-align: center;
                                  vertical-align: middle;
                                  white-space: nowrap;
                 }

                 td,
                 select,
                 input,
                 textarea {
                                  font-size: 0.9rem;
                                  min-width: 150px;
                 }

                 textarea {
                                  resize: vertical;
                 }

                 .btn {
                                  margin-right: 5px;
                 }

                 .table td,
                 .table th {
                                  vertical-align: middle;
                 }

                 .wide-cell {
                                  min-width: 200px;
                 }

                 .form-select,
                 .form-control {
                                  min-width: 160px;
                 }

                 .top-controls {
                                  display: flex;
                                  justify-content: space-between;
                                  align-items: center;
                                  margin-bottom: 15px;
                                  flex-wrap: wrap;
                 }

                 .company-select {
                                  width: 300px;
                 }

                 @media print {

                                  .btn,
                                  .top-controls {
                                                   display: none !important;
                                  }
                 }
                 </style>
</head>

<body>

                 <div class="container-full">
                                  <h2>Audit Findings Register</h2>

                                  <div class="top-controls mb-3">
                                                   <div>
                                                                    <label class="fw-bold me-2">Select Company:</label>
                                                                    <select id="companySelect"
                                                                                     class="form-select d-inline-block company-select">
                                                                                     <option value="">-- Choose Company
                                                                                                      --</option>
                                                                                     <option
                                                                                                      value="ABC Manufacturing PLC">
                                                                                                      ABC Manufacturing
                                                                                                      PLC</option>
                                                                                     <option
                                                                                                      value="Global Logistics Ltd">
                                                                                                      Global Logistics
                                                                                                      Ltd</option>
                                                                                     <option
                                                                                                      value="Nile Trading Enterprise">
                                                                                                      Nile Trading
                                                                                                      Enterprise
                                                                                     </option>
                                                                                     <option
                                                                                                      value="Horizon Import & Export">
                                                                                                      Horizon Import &
                                                                                                      Export</option>
                                                                    </select>
                                                   </div>
                                                   <div>
                                                                    <button class="btn btn-success"
                                                                                     onclick="addRow()">Add Row</button>
                                                                    <button class="btn btn-primary"
                                                                                     onclick="exportTableToExcel()">Export
                                                                                     to Excel</button>
                                                                    <button class="btn btn-secondary"
                                                                                     onclick="window.print()">Print</button>
                                                                    <button class="btn btn-dark"
                                                                                     onclick="saveTableData()">Save</button>
                                                   </div>
                                  </div>

                                  <div class="table-responsive">
                                                   <table class="table table-bordered table-hover" id="auditTable">
                                                                    <thead>
                                                                                     <tr>
                                                                                                      <th>#</th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Reference
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Date
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Audit
                                                                                                                       Area
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Audit
                                                                                                                       Objective
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Client
                                                                                                                       Details
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Findings
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Amount
                                                                                                                       Differences
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Auditor’s
                                                                                                                       Recommendation
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Client
                                                                                                                       Comments
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Management
                                                                                                                       Action
                                                                                                                       Plan
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Action
                                                                                                                       Required
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Additional
                                                                                                                       Remarks
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Person
                                                                                                                       Responsible
                                                                                                      </th>
                                                                                                      <th
                                                                                                                       class="wide-cell">
                                                                                                                       Due
                                                                                                                       Date
                                                                                                      </th>
                                                                                                      <th>Status</th>
                                                                                                      <th>Actions</th>
                                                                                     </tr>
                                                                    </thead>
                                                                    <tbody id="auditBody">
                                                                                     <!-- Rows added dynamically -->
                                                                    </tbody>
                                                   </table>
                                  </div>
                 </div>

                 <script>
                 const auditObjectives = {
                                  'Inventory': 'Verify physical existence and valuation of inventory.',
                                  'Accounts Payable': 'Ensure completeness and accuracy of liabilities.',
                                  'Accounts Receivable': 'Verify existence and collectability of receivables.',
                                  'Expenses': 'Assess validity and classification of expenses.',
                                  'COGS': 'Confirm proper calculation of cost of goods sold.',
                                  'Revenue': 'Ensure all revenue is recorded and accurate.',
                                  'Cash & Bank': 'Verify cash existence and reconciliation accuracy.',
                                  'Fixed Assets': 'Confirm ownership, existence and valuation of assets.',
                                  'Loans Payable': 'Verify completeness and accuracy of loan obligations.',
                                  'Equity': 'Confirm proper classification and disclosure of equity.',
                                  'Payroll': 'Check compliance and accuracy of payroll records.',
                                  'Tax Payable': 'Ensure accurate tax calculation and compliance.'
                 };

                 const sampleFindings = {
                                  'Inventory': 'Discrepancy noted between physical count and records.',
                                  'Accounts Payable': 'Unrecorded supplier invoices at year-end.',
                                  'Accounts Receivable': 'Overdue receivables not followed up for collection.',
                                  'Expenses': 'Missing supporting documents for travel expenses.',
                                  'COGS': 'Incorrect classification of freight as direct cost.',
                                  'Revenue': 'Unrecorded revenue from online platform.',
                                  'Cash & Bank': 'Unreconciled differences in bank reconciliation.',
                                  'Fixed Assets': 'Fixed asset register not updated for disposals.',
                                  'Loans Payable': 'Loan agreement missing for related party loan.',
                                  'Equity': 'Inconsistent classification of retained earnings.',
                                  'Payroll': 'Staff contracts not on file for some employees.',
                                  'Tax Payable': 'Late submission of VAT returns noted.'
                 };

                 function addRow() {
                                  const tbody = document.getElementById("auditBody");
                                  const row = tbody.insertRow();
                                  const rowIndex = tbody.rows.length;

                                  const auditAreaOptions = Object.keys(auditObjectives)
                                                   .map(area => `<option value="${area}">${area}</option>`).join('');

                                  row.innerHTML = `
      <td>${rowIndex}</td>
      <td><input type="text" class="form-control" placeholder="e.g. REF123"></td>
      <td><input type="date" class="form-control"></td>
      <td><select class="form-select audit-area" onchange="updateRowData(this)">
        ${auditAreaOptions}</select></td>
      <td><input type="text" class="form-control audit-objective" readonly></td>
      <td><input type="text" class="form-control" placeholder="Client Name, FY..."></td>
      <td><textarea class="form-control finding" placeholder="Findings..."></textarea></td>
      <td><input type="number" class="form-control" placeholder="Amount..."></td>
      <td><textarea class="form-control" placeholder="Recommendation..."></textarea></td>
      <td><textarea class="form-control" placeholder="Client comments..."></textarea></td>
      <td><textarea class="form-control" placeholder="Action plan..."></textarea></td>
      <td><textarea class="form-control" placeholder="Required actions..."></textarea></td>
      <td><textarea class="form-control" placeholder="Remarks..."></textarea></td>
      <td><input type="text" class="form-control" placeholder="Responsible person..."></td>
      <td><input type="date" class="form-control"></td>
      <td>
        <select class="form-select">
          <option>Open</option>
          <option>In Progress</option>
          <option>Closed</option>
        </select>
      </td>
      <td><button class="btn btn-sm btn-danger" onclick="deleteRow(this)">Delete</button></td>
    `;

                                  updateRowData(row.querySelector('.audit-area'));
                 }

                 function updateRowData(selectElement) {
                                  const selected = selectElement.value;
                                  const row = selectElement.closest('tr');
                                  row.querySelector('.audit-objective').value = auditObjectives[selected] || '';
                                  row.querySelector('.finding').value = sampleFindings[selected] || '';
                 }

                 function deleteRow(button) {
                                  const row = button.closest('tr');
                                  row.remove();
                                  renumberRows();
                 }

                 function renumberRows() {
                                  document.querySelectorAll("#auditBody tr").forEach((row, index) => {
                                                   row.cells[0].innerText = index + 1;
                                  });
                 }

                 function exportTableToExcel() {
                                  const table = document.getElementById('auditTable');
                                  const workbook = XLSX.utils.table_to_book(table, {
                                                   sheet: "Audit Findings"
                                  });
                                  XLSX.writeFile(workbook, 'Audit_Findings_Report.xlsx');
                 }

                 function saveTableData() {
                                  const company = document.getElementById("companySelect").value;
                                  if (!company) {
                                                   alert("Please select a company first.");
                                                   return;
                                  }

                                  const rows = document.querySelectorAll("#auditBody tr");
                                  const data = [];

                                  rows.forEach(row => {
                                                   const cells = row.querySelectorAll(
                                                                    "input, select, textarea"
                                                   );
                                                   const rowData = [company];
                                                   cells.forEach(cell => rowData.push(cell.value || cell
                                                                    .innerText
                                                   ));
                                                   data.push(rowData);
                                  });

                                  console.log("Saved Data:", data);
                                  alert(
                                                   `Audit data for "${company}" captured in console. (Link to DB storage if needed.)`
                                  );
                 }
                 </script>

</body>

</html>