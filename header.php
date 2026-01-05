<header class="bg-dark text-white text-center py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo and Title -->
        <div class="d-flex align-items-center">
            <img src="assets/images/th.jpeg" alt="Logo" style="width: 50px; height: 50px; margin-right: 10px;">
            <h1 class="h4 mb-0">Audit Management Software</h1>
        </div>

        <!-- Navigation -->
        <nav>
            <ul class="list-unstyled d-flex mb-0">
                <li class="mx-2"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="mx-2"><a href="dashboard.php" class="text-white text-decoration-none">Dashboard</a></li>
                    <li class="mx-2"><a href="logout.php" class="text-white text-decoration-none">Logout</a></li>
                    <li class="user-info mx-2">
                        <span>Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?></span>
                    </li>
                <?php else: ?>
                    <li class="mx-2"><a href="register.php" class="text-white text-decoration-none">Register</a></li>
                    <li class="mx-2"><a href="login.php" class="text-white text-decoration-none">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Main Content -->
<div class="container mt-4">
    <!-- Content goes here -->
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

