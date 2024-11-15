<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page instead of editProfile.php
    exit();
}

require_once 'auth/config/database.php';
require_once 'auth/models/user.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Fetch user data
$UserID = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE UserID = :UserID";
$stmt = $db->prepare($query);
$stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT); // Bind UserID as an integer
$stmt->execute();

$userData = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riberio Cafe Profile Page</title>
    <link rel="stylesheet" href="css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/publicDefault.css">
    <script src="js/profile.js"></script>
<style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        main {
            flex: 1;
        }

        .col-sm-8 {
            padding-bottom: 100px;
        }

        iframe {
            width: 100%;
            border: none;
        }
    </style>
</head>

<body>
    <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH."nav.php"); 
    ?>
    
    <main>
        <div class="container mt-5">
            <div class="row">
                <!--Account Settings Sidebar -->
                <div class="col-sm-4">
                    <h3>Welcome Back!</h3>
                    <h3><?php echo htmlspecialchars($userData['Username']); ?></h3>
                    <div class="profile-settings-frame">
                        <h3>My Account</h3>
                        <hr>

                        <button class="btn btn-secondary d-lg-none mb-3" type="button" data-bs-toggle="collapse"
                            data-bs-target="#profile-nav" aria-expanded="false" aria-controls="profile-nav">
                            Account Settings
                        </button>

                        <div id="profile-nav" class="collapse d-lg-block">
                            <ul class="nav nav-pills flex-column">
                                <label for="profile">Account Information</label>
                                <li class="nav-item">
                                    <a class="nav-link profile-settings-nav" href="profile.php?page=editProfile">Edit Profile</a>
                                </li>
                                <label for="profile">Delivery</label>
                                <li class="nav-item">
                                    <a class="nav-link profile-settings-nav" href="profile.php?page=deliveryAddress">Delivery Address</a>
                                </li>
                                <label for="profile">Orders & Tracking</label>
                                <li class="nav-item">
                                    <a class="nav-link profile-settings-nav" href="profile.php?page=orderTracking">Order Tracking</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link profile-settings-nav" href="profile.php?page=orderHistory">Order History</a>
                                </li>
                                <label for="profile">Favorite & Preference</label>
                                <li class="nav-item">
                                    <a class="nav-link profile-settings-nav" href="profile.php?page=favouriteList">Favorite List</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--dynamic right screen -->
                <div class="col-sm-8 d-flex" style="height: 100vh;">
                    <iframe id="contentFrame" width="100%" height="100%" src="userProfileSettings/editProfile.php" frameborder="0" name="profile-iframe" style="flex-grow: 1;"></iframe>
                </div>
            </div>
        </div>
    </main>
    <!-- footer -->
    <?php include($IPATH."footer.html"); ?>

</body>

</html>