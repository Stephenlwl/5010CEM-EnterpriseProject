<?php

if (!isset($_SESSION['AdminID'])) {
    header('Location: ../../admin/adminLogin.php.php'); 
    exit();
}

require_once '../auth/config/database.php';
require_once '../auth/models/user.php';

$database = new Database_Auth();
$db = $database->getConnection();

// Fetch user data
$AdminID = $_SESSION['AdminID'];
$query = "SELECT * FROM admin WHERE AdminID = :AdminID";
$stmt = $db->prepare($query);
$stmt->bindParam(':AdminID', $AdminID, PDO::PARAM_INT);
$stmt->execute();

$adminData = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navigation.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <title>Admin Dashboard</title>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar bg-dark text-light flex-column p-4" style="height: 100vh; width: 250px;">
            <img src="../img/logo.png" alt="Rimberio Cafe" width="190" height="50">
            <div class="d-flex align-items-center mb-4 text-white text-decoration-none text-center">
                <span class="fs-4"><?php echo htmlspecialchars($adminData['AdminName']); ?> Dashboard</span>
            </div>
            <hr class="text-light">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="orderDashboard.php" class="nav-link text-white text-start">
                        <i class="bi bi-table"></i> Order Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="salesDashboard.php" class="nav-link text-white text-start">
                        <i class="bi bi-bar-chart"></i> Sales Dashboard
                    </a>
                </li>
                <hr>
                <li class="nav-item">
                    <a href="inventoryDashboard.php" class="nav-link text-white text-start">
                        <i class="bi bi-box"></i> Inventory Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="productManagement.php" class="nav-link text-white text-start">
                        <i class="bi bi-box-seam"></i> Product Management
                    </a>
                </li>
                <hr>
                <li class="nav-item">
                    <a href="generateReport.php" class="nav-link text-white text-start">
                        <i class="bi bi-file-earmark-text"></i> Reports
                    </a>
                </li>
                <li class="nav-item dropdown mb-2">
                    <a class="nav-link dropdown-toggle text-white text-start" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                        <li>
                            <a href="promotionSettings.php" class="dropdown-item nav-link text-dark text-start">
                                <i class="bi bi-tags"></i> Promotion Settings
                            </a>
                        </li>
                        <li>
                            <a href="locationSettings.php" class="dropdown-item nav-link text-dark text-start">
                                <i class="bi bi-geo-alt"></i> Location Settings
                            </a>
                        </li>
                    </ul>
                </li>
                <hr>
                <li class="nav-item mt-2">
                    <a href="../auth/objects/admin_logout.php" class="nav-link text-white text-start">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>
