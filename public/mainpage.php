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
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mainpage.css">
    <link rel="stylesheet" href="css/publicDefault.css">
    <script src="js/mainpage.js"></script>
</head>
<body>
    
    <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/"; 
        include($IPATH."nav.php"); 
    ?>

    <main class="scroll-container row">
        <!-- Hero Section -->
        <section class="hero">
            <div class="video-overlay"></div> <!-- Dark overlay -->
            <video autoplay muted loop class="hero-video">
                <source src="/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/img/rimberiohome.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="hero-content">
                <h1>Welcome to Rimberio</h1>
                <p>Brew It Your Way – Your Coffee, Your Rules.</p>
                <a href="#features" class="btn">Get Started</a>
            </div>
        </section>

        <!-- Rimberio Sales Section -->
        <section id="coffee-sales" class="container py-5">
             <!-- Background Video -->
                <video autoplay muted loop class="video-background">
                    <source src="img/coffee-sold-bg-video.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <div class="row justify-content-center position-relative">
               

                <div class="col-sm-10 position-relative">
                    <!-- Sales Card -->
                    <div class="card shadow border-0">
                        <div class="card-body text-center">
                            <h3 class="card-title mb-4">Total Coffee Drink Sold So Far</h3>
                            <div class="display-4" id="totalCoffeeSold">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
            

       <!-- Features Section -->
        <section id="features" class="features">
            <video autoplay muted loop class="features-video">
                <source src="/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/img/background2.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <div class="features-content">
                <h2>Drinks</h2>
                <div id="featureBox" class="feature-box">
                    <div class="feature-item">
                        <h3>coffee 1</h3>
                        <p>Description of coffee 1.</p>
                    </div>
                    <div class="feature-item">
                        <h3>coffee 2</h3>
                        <p>Description of coffee 2.</p>
                    </div>
                    <div class="feature-item">
                        <h3>coffee 3</h3>
                        <p>Description of coffee 3.</p>
                    </div>
                </div>
                
                <div id="featureBox2" class="feature-box hidden">
                    <div class="feature-item">
                        <h3>coffee 4</h3>
                        <p>Description of coffee 4.</p>
                    </div>
                    <div class="feature-item">
                        <h3>coffee 5</h3>
                        <p>Description of coffee 5.</p>
                    </div>
                    <div class="feature-item">
                        <h3>coffee 6</h3>
                        <p>Description of coffee 6.</p>
                    </div>
                </div>


                    <!-- Navigation Buttons -->
                <button class="prev" onclick="showPrev()">Previous</button>
                <button class="next" onclick="showNext()">Next</button>
                    
                    
                </div>
            </div>
        </section>


        <!-- Services Section -->
        <section class="services">
            <h1></h1>
            <div class="services-container">
                <div class="service-item service-coffee">
                    <h3>Customizable Coffee Creations</h3>
                    <p>Experience the joy of crafting your perfect cup of coffee by choosing from a variety of premium coffee beans and additional flavors or milk options to suit your taste.</p>
                </div>
                <div class="service-item service-dessert">
                    <h3>Decadent Desserts</h3>
                    <p>Indulge in our selection of delicious desserts, freshly baked daily, ranging from classic pastries to unique creations that satisfy any sweet tooth.</p>
                </div>
                <div class="service-item service-merchandise">
                    <h3>Merchandise Sales</h3>
                    <p>Explore our collection of café merchandise, including stylish bottles, mugs, and other items that let you take a piece of our café experience home with you.</p>
                </div>
            </div>
        </section>

    </main>
    <!-- footer -->
    <?php include($IPATH."footer.html"); ?>

</body>
</html> 