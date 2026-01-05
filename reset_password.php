<?php
include 'header.php';



session_start();
include('db.php'); // Ensure this file contains your database connection setup

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $token = $_POST['token'];

    if ($password != $confirmPassword) {
        $_SESSION['status'] = "Passwords do not match!";
        header("Location: reset_password.php?token=" . $token);
        exit();
    }

    // Check if the token is valid
    $query = "SELECT * FROM password_resets WHERE token = ? AND expires > ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("ERROR: Could not prepare query: " . $conn->error);
    }

    $currentTime = date("U");
    $stmt->bind_param("si", $token, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $resetRequest = $result->fetch_assoc();
        $email = $resetRequest['email'];

        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update the user's password
        $query = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die("ERROR: Could not prepare query: " . $conn->error);
        }

        $stmt->bind_param("ss", $hashedPassword, $email);
        if ($stmt->execute()) {
            // Delete the used reset token
            $query = "DELETE FROM password_resets WHERE token = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $_SESSION['status'] = "Password reset successfully! You can now log in with your new password.";
            header('Location: login.php');
        } else {
            $_SESSION['status'] = "Error resetting password. Please try again.";
        }
    } else {
        $_SESSION['status'] = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php
    if (isset($_SESSION['status'])) {
        echo "<p>" . $_SESSION['status'] . "</p>";
        unset($_SESSION['status']);
    }

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    } else {
        die("Invalid token");
    }
    ?>
    <form action="reset_password.php" method="POST">
        <input type="password" name="password" placeholder="New password" required>
        <input type="password" name="confirmPassword" placeholder="Confirm new password" required>
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>


<?php include 'footer.php'; ?>
