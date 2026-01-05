<?php 
include 'header.php';
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = ""; // To store success or error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = trim($_POST['role']);

    if (empty($username) || empty($email) || empty($_POST['password']) || empty($role)) {
        $message = "<div class='alert alert-danger text-center'>All fields are required.</div>";
    } else {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $message = "<div class='alert alert-danger text-center'>SQL Error: " . $conn->error . "</div>";
        } else {
            $stmt->bind_param("ssss", $username, $email, $password, $role);

            if ($stmt->execute()) {
                // Auto-login after successful registration
                session_start();
                $_SESSION['user_id'] = $conn->insert_id; // Get the last inserted user ID
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // Set a session message for successful registration
                $message = "<div class='alert alert-success text-center'>Successfully Registered! Redirecting to your dashboard...</div>";

                // Redirect after a short delay
                echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 2000);</script>";
            } else {
                $message = "<div class='alert alert-danger text-center'>Error: Unable to create account. " . $stmt->error . "</div>";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Create an Account</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                            <div class="invalid-feedback">Please enter your username.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter a secure password" required>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled selected>Select a role</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-1">Already have an account? <a href="login.php" class="text-primary">Login here</a></p>
                    <p class="mb-0"><a href="reset_password.php" class="text-secondary">Forgot your password?</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<script>
    // Bootstrap form validation
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
