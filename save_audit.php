<?php
// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Database connection
    $conn = new PDO("mysql:host=localhost;dbname=audit_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get data from form (use null if not set)
    $finding_id = $_POST['finding_id'] ?? null;
    $account_item = $_POST['account_item'] ?? null;
    $audit_objective = $_POST['audit_objective'] ?? null;
    $audit_findings = $_POST['audit_findings'] ?? null;
    $reference = $_POST['reference'] ?? null;
    $date = $_POST['date'] ?? null;
    $amount_difference = $_POST['amount_difference'] ?? null;
    $remarks = $_POST['remarks'] ?? null;
    $action_required = $_POST['action_required'] ?? null;
    $audit_status = $_POST['audit_status'] ?? null;
    $next_steps = $_POST['next_steps'] ?? null;
    $reviewer_name = $_POST['reviewer_name'] ?? null;
    $client_rep_name = $_POST['client_rep_name'] ?? null;
    $client_management_comment = $_POST['client_management_comment'] ?? null;

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO audit_findings (
        account_item, audit_objective, audit_findings, reference, date, 
        amount_difference, remarks, action_required, audit_status, 
        next_steps, reviewer_name, client_rep_name, client_management_comment
    ) VALUES (
        :account_item, :audit_objective, :audit_findings, :reference, :date, 
        :amount_difference, :remarks, :action_required, :audit_status, 
        :next_steps, :reviewer_name, :client_rep_name, :client_management_comment
    )");

    // Bind parameters
    $stmt->execute([
        ':account_item' => $account_item,
        ':audit_objective' => $audit_objective,
        ':audit_findings' => $audit_findings,
        ':reference' => $reference,
        ':date' => $date,
        ':amount_difference' => $amount_difference,
        ':remarks' => $remarks,
        ':action_required' => $action_required,
        ':audit_status' => $audit_status,
        ':next_steps' => $next_steps,
        ':reviewer_name' => $reviewer_name,
        ':client_rep_name' => $client_rep_name,
        ':client_management_comment' => $client_management_comment,
    ]);

    echo "Audit data inserted successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

