<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Audit Management Software</title>
    <style>
        /* Custom Styling */
        body {
            font-family: 'Arial', sans-serif;
        }
        .hero-section {
            background: url('assets/images/OIP.jpeg') center/cover no-repeat;
            height: 100vh;
            color: white;
            text-shadow: 1px 1px 10px black;
        }
        .hero-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .hero-content h1 {
            font-size: 3.5rem;
        }
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .audit-functions .card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .audit-functions .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .stats-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }
        .stats-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .stats-card h3 {
            margin-top: 15px;
            font-size: 2rem;
        }
        .footer {
            background-color: #343a40;
            color: white;
        }
        .footer a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="home.php">
            <img src="assets/images/th.jpeg" alt="Logo" width="30" height="30" class="d-inline-block align-top">
            Audit Management Software
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#functions">Audit Functions</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content text-center">
        <h1>Streamline Your Auditing Process</h1>
        <p>Comprehensive solutions for audit planning, execution, and reporting.</p>
        <a href="login.php" class="btn btn-light btn-lg">Get Started</a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>About Our Audit Management Software</h2>
                <p>Our software is designed to help audit firms enhance their productivity and efficiency. With advanced tools for planning, execution, and reporting, we simplify the audit process while maintaining compliance with industry standards.</p>
            </div>
            <div class="col-md-6">
                <img src="assets/images/OIP (1).jpeg" alt="Audit Image" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Audit Functions Section -->
<section id="functions" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Key Features</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/planning.jpg" class="card-img-top" alt="Audit Planning">
                    <div class="card-body">
                        <h5 class="card-title">Audit Planning</h5>
                        <p class="card-text">Design detailed audit plans with customizable templates and scheduling tools.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/execution.jpg" class="card-img-top" alt="Audit Execution">
                    <div class="card-body">
                        <h5 class="card-title">Audit Execution</h5>
                        <p class="card-text">Monitor audit progress with real-time data and collaborative tools.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="assets/images/reporting.jpg" class="card-img-top" alt="Audit Reporting">
                    <div class="card-body">
                        <h5 class="card-title">Audit Reporting</h5>
                        <p class="card-text">Generate professional audit reports with actionable insights and analytics.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <h2 class="text-center mb-4">Our Achievements</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-users fa-3x text-primary"></i>
                    <h3>500+</h3>
                    <p>Satisfied Clients</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-check-circle fa-3x text-primary"></i>
                    <h3>1,200+</h3>
                    <p>Completed Audits</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-trophy fa-3x text-primary"></i>
                    <h3>50+</h3>
                    <p>Awards Won</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
    <div class="container text-center">
        <h2>Contact Us</h2>
        <p class="lead">We'd love to hear from you! Reach out with any questions or inquiries.</p>
        <form>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="email" class="form-control" placeholder="Your Email" required>
                </div>
            </div>
            <textarea class="form-control mb-3" rows="4" placeholder="Your Message" required></textarea>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="footer text-center py-4">
    <div class="container">
        <p>&copy; 2024 Audit Management Software. All Rights Reserved.</p>
        <div class="social-icons">
            <a href="#" class="text-white mx-2"><i class="fab fa-facebook fa-lg"></i></a>
            <a href="#" class="text-white mx-2"><i class="fab fa-twitter fa-lg"></i></a>
            <a href="#" class="text-white mx-2"><i class="fab fa-linkedin fa-lg"></i></a>
            <a href="#" class="text-white mx-2"><i class="fab fa-instagram fa-lg"></i></a>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
