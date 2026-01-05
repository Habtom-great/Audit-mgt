<?php
// Include database connection
include 'db.php';


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all supervisors
function fetchSupervisors($conn)
{
    $sql = "SELECT * FROM supervisors ORDER BY last_name, first_name";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error in query: " . $conn->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Add or Edit a supervisor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    if (isset($_POST['add_supervisor'])) {
        // Add a new supervisor
        $sql = "INSERT INTO supervisors (first_name, last_name, email, phone) 
                VALUES ('$firstName', '$lastName', '$email', '$phone')";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Staff added successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $conn->error;
        }
    }

    if (isset($_POST['edit_supervisors'])) {
        // Edit an existing supervisor
        $supervisorId = $_POST['supervisors_id'];
        $sql = "UPDATE supervisors
                SET first_name='$firstName', last_name='$lastName', email='$email', phone='$phone'
                WHERE id='$supervisorId'";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Supervisor updated successfully.";
        } else {
            $_SESSION['error'] = "Error: " . $conn->error;
        }
    }

    header('Location: supervisors.php');
    exit;
}

// Delete a supervisor
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM supervisors WHERE id='$id'";
    if ($conn->query($sql)) {
        $_SESSION['message'] = "Supervisor deleted successfully.";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
    header('Location: supervisors.php');
    exit;
}

// Fetch supervisors
$supervisors = fetchSupervisors($conn);

// Fetch supervisor details for editing
if (isset($_GET['edit_id'])) {
    $editId = $_GET['edit_id'];
    $sql = "SELECT * FROM supervisors WHERE id='$editId'";
    $result = $conn->query($sql);
    $supervisor = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>Supervisors</title>
</head>
<body>
    <!-- Header -->
  

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Management Software</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header>
    <div class="container">
        <h1>Audit Management Software</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <li class="user-info">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                <?php else: ?>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main>



    <div class="container mt-4">
        <h1 class="text-center mb-4">Manage Supervisors</h1>

        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Form for Adding or Editing Supervisor -->
        <form method="POST" class="bg-light p-4 rounded mb-4 mx-auto" style="max-width: 100%;">
            <h3 class="text-center mb-3"><?= isset($supervisor) ? 'Edit Supervisor' : 'Add Supervisor'; ?></h3>
            <div class="row g-3">
                <div class="col">
                    <label for="supervisor_id" class="form-label">Supervisor ID</label>
                    <input type="number" id="supervisor_id" name="supervisor_id" placeholder="Enter Supervisor ID" class="form-control form-control-sm" 
                           value="<?= isset($supervisor) ? $supervisor['id'] : ''; ?>" readonly>
                </div>
                <div class="col">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter First Name" class="form-control form-control-sm" 
                           value="<?= isset($supervisor) ? $supervisor['first_name'] : ''; ?>" required>
                </div>
                <div class="col">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter Last Name" class="form-control form-control-sm" 
                           value="<?= isset($supervisor) ? $supervisor['last_name'] : ''; ?>" required>
                </div>
                <div class="col">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" class="form-control form-control-sm" 
                           value="<?= isset($supervisor) ? $supervisor['email'] : ''; ?>" required>
                </div>
                <div class="col">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter Phone Number" class="form-control form-control-sm" 
                           value="<?= isset($supervisor) ? $supervisor['phone'] : ''; ?>" required>
                </div>
            </div>
            <div class="text-center mt-4">
                <?php if (isset($supervisor)): ?>
                    <button type="submit" name="edit_supervisor" class="btn btn-warning btn-sm">Edit Supervisor</button>
                <?php else: ?>
                    <button type="submit" name="add_supervisor" class="btn btn-primary btn-sm">Add Supervisor</button>
                <?php endif; ?>
            </div>
        </form>

        <!-- Supervisors Table -->
        <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($supervisors as $supervisor): ?>
                    <tr>
                        <td><?= $supervisor['id']; ?></td>
                        <td><?= $supervisor['first_name']; ?></td>
                        <td><?= $supervisor['last_name']; ?></td>
                        <td><?= $supervisor['email']; ?></td>
                        <td><?= $supervisor['phone']; ?></td>
                        <td>
                            <a href="supervisors.php?delete_id=<?= $supervisor['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                            <a href="supervisors.php?edit_id=<?= $supervisor['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include('footer.php'); ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

