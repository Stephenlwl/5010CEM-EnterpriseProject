<?php
session_start();
require_once '../auth/config/database.php';

date_default_timezone_set('Asia/Kuala_Lumpur');

$database = new Database_Auth();
$db = $database->getConnection();

$inventoryQuery = "SELECT ItemName, ItemQuantity, StockThreshold, AutoRestockQuantity, ItemPrice, 
                   (ItemQuantity * ItemPrice) as TotalValue
                   FROM menu";
$inventoryStmt = $db->prepare($inventoryQuery);
$inventoryStmt->execute();
$inventoryData = $inventoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <img src="../img/logo.png" alt="logo" class="img-fluid" style="width: 250px; height: auto; display: block; margin: 0 auto;">
        <h3 class="text-center mb-4">Inventory Report: <?= date("Y-m-d H:i:s") ?></h3>
        <button class="btn btn-primary no-print m-4" onclick="window.print()">Print Inventory Report</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Current Stock Quantity</th>
                    <th>Stock Threshold</th>
                    <th>Auto Restock Quantity</th>
                    <th>Unit Price (RM)</th>
                    <th>Total Value (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventoryData as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['ItemName']) ?></td>
                        <td><?= htmlspecialchars($item['ItemQuantity']) ?></td>
                        <td><?= htmlspecialchars($item['StockThreshold']) ?></td>
                        <td><?= htmlspecialchars($item['AutoRestockQuantity']) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($item['ItemPrice'], 2)) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($item['TotalValue'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
