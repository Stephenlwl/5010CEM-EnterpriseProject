<?php
session_start();

require_once '../auth/config/database.php';

$database = new Database_Auth();
$db = $database->getConnection();

$admin_id = $_SESSION['AdminID'];

if (!$admin_id){
    header("Location: adminLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/salesDashboard.css">
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <script src="js/salesDashboard.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <?php 
                $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/admin/inc/";
                include($IPATH."nav.php"); 
                ?>
            </div>
            <div class="col-md-10">
                <div class="main-content">

                    <div class="row text-center">
                        <div class="col-md-12">
                            <h1>Sales Dashboard</h1>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-5 p-3 shadow bg-body rounded">
                        <div class="col-sm-6 ">
                            <h3>Top Products (Most Ordered)</h3>
                            <canvas id="topProductsChart"></canvas>
                        </div>
                        <div class="col-sm-6">
                           <h4>Top 5 Products Details</h4>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity Sold</th>
                                    </tr>
                                </thead>
                                <tbody id="topProductsTable">
                                    <!-- top products details -->
                                </tbody>
                            </table>
                         </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-5 chart-container shadow p-5 mb-5 bg-body rounded">
                            <h5>Sales Over Time (Daily Sales)</h5>
                            <canvas id="salesOverTimeChart"></canvas>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-5 p-2 chart-container shadow mb-5 bg-body rounded">
                            <h5>Weekly Sales Chart</h5>
                            <canvas id="weeklySalesChart"></canvas>
                        </div>

                        <div class="col-sm-5 p-2 chart-container shadow mb-5 bg-body rounded">
                            <h5>Monthly Sales Chart</h5>
                            <canvas id="monthlySalesChart"></canvas>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
