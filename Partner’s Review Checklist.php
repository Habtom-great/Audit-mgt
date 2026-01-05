<style>
/* General Table Styles */
    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
        margin-bottom: 30px;
    }
    
    .table {
        font-size: 18px;
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background-color: #f8f9fa;
    }

    .table th,
    .table td {
        text-align: center;
        padding: 16px;
        vertical-align: middle;
        word-wrap: break-word;
    }

    .table th {
        background-color: #343a40;
        color: #fff;
        font-weight: bold;
        font-size: 16px;
    }

    .table td {
        background-color: #ffffff;
        color: #495057;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
    }

    /* Adjust column widths */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 5%;
    }

    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: 60%;
    }

    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 15%;
    }

    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 20%;
    }

    /* Form Styling */
    .form-section .form-group {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group .form-select,
    .form-group .form-control {
        width: 100%;
        font-size: 14px;
        padding: 12px;
        border-radius: 5px;
        border: 1px solid #ced4da;
        transition: border 0.3s ease, box-shadow 0.3s ease;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 8px rgba(38, 143, 255, 0.5);
    }

    .textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Button Styles */
    .btn {
        font-size: 16px;
        padding: 12px 20px;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        width: 100%;
        max-width: 250px;
        margin-top: 15px;
    }

    .btn-save {
        background-color: #007bff;
        color: #fff;
        border: none;
    }

    .btn-save:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    .btn-back {
        background-color: #6c757d;
        color: #fff;
        border: none;
    }

    .btn-back:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    /* Success Message Styling */
    .alert {
        display: none;
        margin-top: 20px;
        padding: 15px;
        background-color: #28a745;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        text-align: center;
    }

    /* Header Styling */
    .header-spacing {
        margin-bottom: 40px;
    }

    .form-section h1 {
        font-size: 32px;
        font-weight: 700;
        color: #343a40;
        margin-bottom: 15px;
    }

    .form-section p {
        font-size: 18px;
        color: #555;
    }

    /* Side-by-Side Layout for "Prepared by" and "Reviewed by" */
    .form-group.row {
        display: flex;
        gap: 20px;
        justify-content: space-between;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table th,
        .table td {
            font-size: 14px;
            padding: 10px;
        }

        .btn {
            font-size: 14px;
            padding: 10px;
        }

        .form-select,
        .textarea {
            font-size: 12px;
            padding: 8px;
        }

        .form-group.row {
            flex-direction: column;
        }
    }
</style>

<?php
include('header.php');
include('db.php');

// Ensure database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$audit_id = $_GET['audit_id'] ?? null;
$completion_date = null;
$audit_clients = null;

// Fetch audit details
if ($audit_id) {
    $sql = "SELECT id, company_name, created_at AS completion_date FROM audit_clients WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $audit_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $audit_clients = $result->fetch_assoc();
        $completion_date = $audit_clients['completion_date'] ?? null;
    } else {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();
}

// Fetch staff names for dropdowns
$names_sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM supervisors";
$names_result = $conn->query($names_sql);

if (!$names_result) {
    die("Query for staff names failed: " . $conn->error);
}

// Checklist items
$checklist = [
    "Have all working papers been completed and cross-referenced to the lead schedules and the draft financial statements?",
    "Have all material findings been reported in the Matters for Partner’s Attention and in the Manager’s Conclusion?",
    "Are the conclusions from the final analytical review consistent with our understanding of the business?",
    "Have all material and risky account areas been subjected to audit?",
    "Are you satisfied there are no indications of fraud arising from the audit work?",
    "Have procedures been performed to ensure the accounting policies adopted are in accordance with IFRS?",
    "Do the financial statements comply with all applicable statutory regulations?",
    "Is other information issued consistent with the financial statements?",
    "Have all consultations been adequately recorded and implemented?",
    "Was an engagement quality control review required and points cleared?",
    "Do you believe that an unqualified audit report is appropriate?",
    "Do you believe there are no circumstances affecting independence?",
    "Ensure all major points have been discussed and cleared before signing the audit report."
];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner's Review Checklist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-5">
    <div class="form-section">
        <!-- Header Section -->
        <div class="header-spacing text-center">
            <h1>Partner’s Review Checklist</h1>
            <p><strong>Client:</strong> <?php echo htmlspecialchars($audit_clients['company_name'] ?? 'N/A'); ?></p>
            <p><strong>Period Ended:</strong> <?php echo htmlspecialchars($completion_date ?? 'N/A'); ?></p>
        </div>

        <!-- Checklist Items -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Answer</th>
                    <th>Notes</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($checklist as $index => $item): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($item); ?></td>
                        <td>
                            <select name="answers[<?php echo $index; ?>]" class="form-select" required>
                                <option value="">Select...</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="notes[<?php echo $index; ?>]" class="form-control" placeholder="Add notes"></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Checklist Form -->
        <form action="save_checklist.php" method="POST" id="checklist-form">
            <input type="hidden" name="audit_id" value="<?php echo htmlspecialchars($audit_id); ?>">

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="prepared_by" class="form-label"><strong>Prepared by:</strong></label>
                    <select name="prepared_by" id="prepared_by" class="form-select" required>
                        <option value="">Select...</option>
                        <?php while ($name = $names_result->fetch_assoc()): ?>
                            <option value="<?php echo $name['id']; ?>"><?php echo htmlspecialchars($name['full_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="reviewed_by" class="form-label"><strong>Reviewed by:</strong></label>
                    <select name="reviewed_by" id="reviewed_by" class="form-select" required>
                        <option value="">Select...</option>
                        <?php
                        mysqli_data_seek($names_result, 0);
                        while ($name = $names_result->fetch_assoc()): ?>
                            <option value="<?php echo $name['id']; ?>"><?php echo htmlspecialchars($name['full_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-save btn-lg" id="save-button"><i class="bi bi-save"></i> Save Checklist</button>
            </div>
        </form>

        <!-- Success Message -->
        <div class="alert" id="success-message">Checklist saved successfully!</div>
    </div>

<!-- Back Button -->
<div class="text-center mt-4">
    <a href="audit_working_paper.php?audit_id=<?php echo htmlspecialchars($audit_id); ?>" class="btn btn-back btn-outline-dark">
        <i class="bi bi-arrow-left"></i> Back to Audit Working Paper
    </a>
</div>
<!-- Footer -->
<?php include('footer.php'); ?>

<!-- JavaScript to Show Success Message -->
<script>
    document.getElementById('checklist-form').addEventListener('submit', function (e) {
        e.preventDefault();
        // Simulate a save action (replace with actual logic)
        setTimeout(function () {
            document.getElementById('success-message').style.display = 'block';
        }, 500);
    });
</script>

</body>
</html>
