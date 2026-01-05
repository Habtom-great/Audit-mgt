<?php
include 'header.php';
include 'db.php';

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Check if the email exists in the users (staff or admin) table
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("ERROR: Could not prepare query: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50));
        $expires = date("U") + 1800; // Token expires in 30 minutes

        // Save the token and expiry in the database
        $query = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("ERROR: Could not prepare query: " . $conn->error);
        }
        $stmt->bind_param("ssi", $email, $token, $expires);
        $stmt->execute();

        // Send the reset link to the user's email
        $resetLink = "http://yourwebsite.com/reset_password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password: " . $resetLink;
        $headers = "From: no-reply@yourwebsite.com";
        
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['status'] = "Check your email for the reset link!";
            header('Location: login.php'); // Redirect to login page or show message
        } else {
            $_SESSION['status'] = "Error sending email. Try again.";
        }
    } else {
        $_SESSION['status'] = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <?php
    if (isset($_SESSION['status'])) {
        echo "<p>" . $_SESSION['status'] . "</p>";
        unset($_SESSION['status']);
    }
    ?>
    <form action="forgot_password.php" method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>


<?php include 'footer.php'; ?>
