<?php
session_start();
require_once '../auth/config/database.php';

$database = new Database_Auth();
$db = $database->getConnection();

// use today's date by default
$date = isset($_GET['reportDate']) ? $_GET['reportDate'] : date("Y-m-d");

$salesQuery = "SELECT m.ItemName, SUM(rd.ItemQuantity) as QuantitySold, SUM(rd.TotalPrice) as TotalSales, r.ReceiptCreatedAt
               FROM receipt_details rd 
               JOIN menu m ON rd.ItemID = m.ItemID 
               JOIN receipt r ON rd.ReceiptID = r.ReceiptID
               WHERE DATE(r.ReceiptCreatedAt) = :date
               GROUP BY rd.ItemID";
$salesStmt = $db->prepare($salesQuery);
$salesStmt->bindParam(':date', $date);
$salesStmt->execute();
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
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
        <span class="text-end text-muted">Generated On (<?= date("Y-m-d") ?>)</span>
        <img src="../img/logo.png" alt="logo" class="img-fluid" style="width: 250px; height: auto; display: block; margin: 0 auto;">
        <h3 class="text-center m-4">Sales Report: <?= htmlspecialchars($date)?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Sales (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesData as $sale): ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['ItemName']) ?></td>
                        <td><?= htmlspecialchars($sale['QuantitySold']) ?></td>
                        <td>RM <?= htmlspecialchars(number_format($sale['TotalSales'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary no-print" onclick="window.print()">Print Sales Report</button>
    </div>
</body>
</html>
