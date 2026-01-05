<?php  
session_start();
include('header.php'); 

$host = 'localhost';
$dbname = 'audit_management';
$username = 'root';
$password = "";

try {
    // Create a PDO instance and set it to handle exceptions
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, show an error message
    die("Connection failed: " . $e->getMessage());
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit;
}

// Query to get the user's data
$sql = "SELECT * FROM users WHERE id = :user_id";  // Ensure the correct column name is used
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

// Fetch the user's data




?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white sidebar vh-100 p-4" style="width: 250px;">
        <h4 class="text-center mb-4">Audit Dashboard</h4>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="dashboard.php">
                    <i class="bi bi-house-door-fill me-2"></i>Home
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="completed_audits_list.php">
                    <i class="bi bi-check-circle-fill me-2"></i>Completed Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="ongoing_audits_list.php">
                    <i class="bi bi-hourglass-split me-2"></i>Ongoing Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="pending_audits_list.php">
                    <i class="bi bi-x-circle-fill me-2"></i>Pending Audits
                </a>
            </li>
            <!-- Adding reference section -->
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="audit_references.php">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>Audit References
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="international_reports.php">
                    <i class="fas fa-globe me-2"></i>International Reports
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 flex-grow-1">
        <h2 class="text-center mb-5 text-primary">Audit Dashboard</h2>

        <!-- Audit Status Section -->
        <div class="row">
            <?php
            $statuses = [
                [
                    'title' => 'Completed Audits',
                    'count' => 15,
                    'description' => 'Audits completed successfully, ready for reporting and final assessment.',
                    'color' => 'success',
                    'icon' => 'bi bi-check-circle-fill',
                    'link' => 'completed_audits_list.php'
                ],
                [
                    'title' => 'Ongoing Audits',
                    'count' => 7,
                    'description' => 'Audits in progress. Teams are actively gathering and reviewing data.',
                    'color' => 'warning',
                    'icon' => 'bi bi-hourglass-split',
                    'link' => 'ongoing_audits_list.php'
                ],
                [
                    'title' => 'Pending Audits',
                    'count' => 3,
                    'description' => 'Audits pending due to incomplete documentation or client delays.',
                    'color' => 'danger',
                    'icon' => 'bi bi-x-circle-fill',
                    'link' => 'pending_audits_list.php'
                ]
            ];
            
            foreach ($statuses as $status) {
                echo "
                <div class='col-md-4'>
                    <div class='card shadow-lg mb-4 border-{$status['color']}'>
                        <div class='card-header bg-{$status['color']} text-white text-center'>
                            <h5>{$status['title']}</h5>
                        </div>
                        <div class='card-body text-center'>
                            <i class='{$status['icon']} display-3 mb-3 text-{$status['color']}'></i>
                            <h3 class='display-4'>{$status['count']}</h3>
                            <p>{$status['description']}</p>
                            <a href='{$status['link']}' class='btn btn-outline-{$status['color']} btn-block'>View Details</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>

        <!-- Detailed Analysis Section -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg border-success">
                    <div class="card-header bg-success text-white">
                        <h5>Completed Audits Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Reasons for Completion:</strong></h6>
                        <ul>
                            <li>All necessary documents provided</li>
                            <li>Full client cooperation</li>
                            <li>No major discrepancies found</li>
                        </ul>
                        <a href="international_reports.php" class="btn btn-outline-success btn-block">View Reports</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg border-warning">
                    <div class="card-header bg-warning text-white">
                        <h5>Ongoing Audits Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Ongoing Issues:</strong></h6>
                        <ul>
                            <li>Pending document reviews</li>
                            <li>Financial verifications in progress</li>
                        </ul>
                        <a href="ongoing_audits_list.php" class="btn btn-outline-warning btn-block">View Clients</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5>Pending Audits Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Pending Issues:</strong></h6>
                        <ul>
                            <li>Waiting for client documents</li>
                            <li>Backlog delays</li>
                        </ul>
                        <a href="pending_audits_list.php" class="btn btn-outline-danger btn-block">View Clients</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

kkkkkkkkkkkkkkkkkkk
<?php
// Include database connection
require_once 'db.php'; // Ensure this file establishes the $conn variable

// Initialize variables
$client = null;
$error_message = null;

// Check if a client ID is provided
if (isset($_GET['client_id']) && !empty($_GET['client_id'])) {
    $client_id = intval($_GET['client_id']); // Sanitize input to ensure it's an integer

    // Define the SQL query
    $sql = "SELECT * FROM clients WHERE client_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameter (i for integer)
        $stmt->bind_param('i', $client_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->get_result();

            // Check if any data is returned
            if ($result->num_rows > 0) {
                $client = $result->fetch_assoc(); // Fetch the client data
            } else {
                $error_message = "No client found with the given ID.";
            }
        } else {
            $error_message = "Error executing query: " . htmlspecialchars($stmt->error);
        }

        // Close the statement
        $stmt->close();
    } else {
        $error_message = "Error preparing query: " . htmlspecialchars($conn->error);
    }
} else {
    $error_message = "Client ID not provided or invalid.";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Client Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 20px;
        }
        .card-body {
            font-size: 16px;
        }
        table {
            width: 100%;
        }
        th {
            width: 30%;
            text-align: left;
            color: #495057;
        }
        td {
            color: #6c757d;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($client)): ?>
            <div class="card">
                <div class="card-header">
                    Client Details
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Client ID:</th>
                            <td><?= htmlspecialchars($client['client_id']); ?></td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td><?= htmlspecialchars($client['name']); ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= htmlspecialchars($client['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><?= htmlspecialchars($client['phone']); ?></td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td><?= htmlspecialchars($client['address']); ?></td>
                        </tr>
                        <!-- Add more fields as needed -->
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?= date("Y"); ?> Your Company Name. All Rights Reserved.
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>



<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar vh-100 p-4" style="width: 280px;">
        <h4>Audit Dashboard</h4>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-house-door-fill me-2"></i>Home
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="completed_audits_list.php">
                    <i class="bi bi-check-circle-fill me-2"></i>Completed Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="ongoing_audits_list.php">
                    <i class="bi bi-hourglass-split me-2"></i>Ongoing Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="pending_audits_list.php">
                    <i class="bi bi-x-circle-fill me-2"></i>Pending Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="international_reports.php">
                    <i class="fas fa-globe me-2"></i>International Reports
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="references.php">
                    <i class="bi bi-journal-text me-2"></i>References
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link" href="news_youtube.php">
                    <i class="bi bi-youtube me-2"></i>News & YouTube
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 flex-grow-1">
        <h2 class="content-header">Audit Dashboard</h2>

        <!-- Audit Status Section -->
        <div class="row">
            <!-- Completed Audits -->
            <div class="col-md-4">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-success">
                        <h5>Completed Audits</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="display-4">15</h3>
                        <p>Audits completed successfully, ready for reporting and final assessment.</p>
                        <a href="completed_audits_list.php" class="btn btn-outline-success">View Details</a>
                    </div>
                </div>
            </div>

            <!-- Ongoing Audits -->
            <div class="col-md-4">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-warning">
                        <h5>Ongoing Audits</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="display-4">7</h3>
                        <p>Audits in progress. Teams are actively gathering and reviewing data.</p>
                        <a href="ongoing_audits_list.php" class="btn btn-outline-warning">View Details</a>
                    </div>
                </div>
            </div>

            <!-- Pending Audits -->
            <div class="col-md-4">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-danger">
                        <h5>Pending Audits</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="display-4">3</h3>
                        <p>Audits pending due to incomplete documentation or client delays.</p>
                        <a href="pending_audits_list.php" class="btn btn-outline-danger">View Details</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- References and News Section -->
        <div class="row mt-5">
            <!-- References Section -->
            <div class="col-md-6">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-info text-white">
                        <h5>References</h5>
                    </div>
                    <div class="card-body">
                        <p>Browse and access articles, audit manuals, and guidelines.</p>
                        <a href="references.php" class="btn btn-outline-info">View References</a>
                    </div>
                </div>
            </div>

            <!-- News & YouTube Section -->
            <div class="col-md-6">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5>News & YouTube</h5>
                    </div>
                    <div class="card-body">
                        <p>Stay updated with the latest audit-related news and watch educational videos.</p>
                        <a href="news_youtube.php" class="btn btn-outline-secondary">Explore News & YouTube</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>


kkkkkkk
<?php include('header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white sidebar vh-100 p-4" style="width: 250px;">
        <h4 class="text-center mb-4">Audit Dashboard</h4>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="dashboard.php">
                    <i class="bi bi-house-door-fill me-2"></i>Home
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="completed_audits_list.php">
                    <i class="bi bi-check-circle-fill me-2"></i>Completed Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="ongoing_audits_list.php">
                    <i class="bi bi-hourglass-split me-2"></i>Ongoing Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="pending_audits_list.php">
                    <i class="bi bi-x-circle-fill me-2"></i>Pending Audits
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-white" href="international_reports.php">
                    <i class="fas fa-globe me-2"></i>International Reports
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 flex-grow-1">
        <h2 class="text-center mb-5">Audit Dashboard</h2>

        <!-- Audit Status Section -->
        <div class="row">
            <?php
            $statuses = [
                [
                    'title' => 'Completed Audits',
                    'count' => 15,
                    'description' => 'Audits completed successfully, ready for reporting and final assessment.',
                    'color' => 'success',
                    'icon' => 'bi bi-check-circle-fill',
                    'link' => 'completed_audits_list.php'
                ],
                [
                    'title' => 'Ongoing Audits',
                    'count' => 7,
                    'description' => 'Audits in progress. Teams are actively gathering and reviewing data.',
                    'color' => 'warning',
                    'icon' => 'bi bi-hourglass-split',
                    'link' => 'ongoing_audits_list.php'
                ],
                [
                    'title' => 'Pending Audits',
                    'count' => 3,
                    'description' => 'Audits pending due to incomplete documentation or client delays.',
                    'color' => 'danger',
                    'icon' => 'bi bi-x-circle-fill',
                    'link' => 'pending_audits_list.php'
                ]
            ];
            
            foreach ($statuses as $status) {
                echo "
                <div class='col-md-4'>
                    <div class='card shadow-lg mb-4'>
                        <div class='card-header bg-{$status['color']} text-white text-center'>
                            <h5>{$status['title']}</h5>
                        </div>
                        <div class='card-body text-center'>
                            <i class='{$status['icon']} display-3 mb-3 text-{$status['color']}'></i>
                            <h3 class='display-4'>{$status['count']}</h3>
                            <p>{$status['description']}</p>
                            <a href='{$status['link']}' class='btn btn-outline-{$status['color']} btn-block'>View Details</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>

        <!-- Detailed Analysis Section -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-success text-white">
                        <h5>Completed Audits Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Reasons for Completion:</strong></h6>
                        <ul>
                            <li>All necessary documents provided</li>
                            <li>Full client cooperation</li>
                            <li>No major discrepancies found</li>
                        </ul>
                        <a href="international_reports.php" class="btn btn-outline-success btn-block">View Reports</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-warning text-white">
                        <h5>Ongoing Audits Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Ongoing Issues:</strong></h6>
                        <ul>
                            <li>Pending document reviews</li>
                            <li>Financial verifications in progress</li>
                        </ul>
                        <a href="ongoing_audits_list.php" class="btn btn-outline-warning btn-block">View Clients</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-danger text-white">
                        <h5>Pending Audits Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><strong>Pending Issues:</strong></h6>
                        <ul>
                            <li>Waiting for client documents</li>
                            <li>Backlog delays</li>
                        </ul>
                        <a href="pending_audits_list.php" class="btn btn-outline-danger btn-block">View Clients</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
