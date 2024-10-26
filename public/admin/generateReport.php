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

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch daily sales data
$salesQuery = "SELECT m.ItemName, SUM(rd.ItemQuantity) as QuantitySold, SUM(rd.TotalPrice) as TotalSales
               FROM receipt_details rd 
               JOIN menu m ON rd.ItemID = m.ItemID 
               JOIN receipt r ON rd.ReceiptID = r.ReceiptID
               WHERE DATE(r.ReceiptCreatedAt) = :date
               GROUP BY rd.ItemID";
$salesStmt = $db->prepare($salesQuery);
$salesStmt->bindParam(':date', $date);
$salesStmt->execute();
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch daily inventory data
$inventoryQuery = "SELECT ItemName, ItemQuantity, ItemPrice as UnitPrice, StockThreshold, AutoRestockQuantity, RestockDate as LastRestockDate FROM menu";
$inventoryStmt = $db->prepare($inventoryQuery);
$inventoryStmt->execute();
$inventoryData = $inventoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <script src="js/generateReport.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Navigation -->
            <div class="col-md-2">
                <?php include_once "inc/nav.php"; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="main-content">
                <h1 class="mb-3 text-center">Daily Sales and Inventory Report Dashboard</h1>
                <hr>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <div class="row justify-content-center mb-4">
                        <div class="col-md-4 text-left">
                            <label for="reportDate" class="form-label">Select by Date</label>
                            <input type="date" id="reportDate" class="form-control text-center" value="<?= htmlspecialchars($date) ?>">
                        </div>
                    </div>

                    <!-- Sales Report Table -->
                    <div class="mb-5">
                        <h3 class="mb-3">Sales Report for <span class="text-danger"><?= htmlspecialchars($date) ?></span></h3>
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity Sold</th>
                                    <th>Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($salesData)): ?>
                                    <?php foreach ($salesData as $sale): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($sale['ItemName']) ?></td>
                                            <td><?= htmlspecialchars($sale['QuantitySold']) ?></td>
                                            <td>RM<?= htmlspecialchars(number_format($sale['TotalSales'], 2)) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <div class="alert alert-info" role="alert">
                                                <h5>No Sales for Current Date</h5>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <!-- Print Button -->
                        <div class="text-center">
                            <button onclick="printSalesReport()" class="btn btn-primary btn-lg"><i class="bi bi-printer-fill"></i> Print Sales Report</button>
                        </div>
                    </div>

                    <hr>
                    <!-- Inventory Report Table -->
                    <div class="mb-5">
                        <h3 class="mb-3 text-center">Current Inventory Stock Status</h3>
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">Item Name</th>
                                    <th class="text-center">Current Stock Quantity</th>
                                    <th class="text-center">Stock Threshold</th>
                                    <th class="text-center">Auto Restock Quantity</th>
                                    <th class="text-center">Last Restock Date</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inventoryData as $inventory): ?>
                                    <tr>
                                        <td class="text-left"><?= htmlspecialchars($inventory['ItemName']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($inventory['ItemQuantity']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($inventory['StockThreshold']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($inventory['AutoRestockQuantity']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($inventory['LastRestockDate']) ?></td>
                                        <td class="text-center">RM <?= htmlspecialchars(number_format($inventory['UnitPrice'], 2)) ?></td>
                                        <td class="text-center">RM <?= htmlspecialchars(number_format($inventory['ItemQuantity'] * $inventory['UnitPrice'], 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Print Button -->
                    <div class="text-center">
                        <button onclick="printInventoryReport()" class="btn btn-primary btn-lg"><i class="bi bi-printer-fill"></i> Print Inventory Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
