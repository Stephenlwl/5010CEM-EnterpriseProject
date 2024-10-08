<?php
session_start();

require_once 'auth/config/database.php';
require_once 'auth/models/user.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Fetch user data
if (isset($_SESSION['user_id'])) {
    $UserID = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE UserID = :UserID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT); // Bind UserID as an integer
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $userData = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/mainpage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/mainpage.css">
    <link rel="stylesheet" href="css/publicDefault.css">
</head>
<body>
    
    <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/"; 
        include($IPATH."nav.php"); 
    ?>

    <main class="scroll-container row">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Our Website</h1>
                <p>Explore our amazing content and features.</p>
                <a href="#features" class="btn">Get Started</a>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features">
            <h2>Our Features</h2>
            <div class="feature-box">
                <div class="feature-item">
                    <h3>Feature 1</h3>
                    <p>Description of feature 1.</p>
                </div>
                <div class="feature-item">
                    <h3>Feature 2</h3>
                    <p>Description of feature 2.</p>
                </div>
                <div class="feature-item">
                    <h3>Feature 3</h3>
                    <p>Description of feature 3.</p>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about">
            <h2>About Us</h2>
            <p>We are dedicated to providing the best service possible. Learn more about our mission and values.</p>
        </section>
    </main>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
