<?php
session_start();

// Ensure session variables are set
require_once '../auth/config/database.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Get ReceiptID from the URL parameter
$ReceiptID = isset($_GET['receipt_id']) ? $_GET['receipt_id'] : 0;

// Query to fetch receipt details
$query = "SELECT r.ReceiptID, r.TotalPrice, r.PaymentType, r.ReceiveMethod, r.ReceiptCreatedAt, r.ReferenceNo,
                 a.Address1, a.Address2, a.PostalCode, a.State, rd.ItemID, rd.ItemQuantity, rd.ItemPrice, rd.TotalPrice AS ItemTotal, 
                 m.ItemName, pi.Temperature, pi.MilkType, pi.CoffeeBeanType, pi.Sweetness, pi.AddShot,
                 u.Username, r.DiscountAmount
          FROM Receipt r 
          INNER JOIN receipt_details rd ON r.ReceiptID = rd.ReceiptID 
          LEFT JOIN personal_item pi ON rd.PersonalItemID = pi.PersonalItemID
          LEFT JOIN users u ON r.UserID = u.UserID
          INNER JOIN menu m ON rd.ItemID = m.ItemID 
          INNER JOIN address a ON r.AddressID = a.AddressID
          WHERE r.ReceiptID = :ReceiptID";

$stmt = $db->prepare($query);
$stmt->bindParam(':ReceiptID', $ReceiptID, PDO::PARAM_INT);
$stmt->execute();
$receipt_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure receipt data exists
if (!$receipt_data) {
    echo "No receipt found.";
    exit;
}

// Initialize totals
$subTotal = 0;

// Calculate sub-total and SST
foreach ($receipt_data as $item) {
    $subTotal += $item['ItemPrice'] * $item['ItemQuantity'];
}
$sst = $subTotal * 0.08; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/print_receipt.css">
</head>
<body>
    <div class="container">
        <img src="../img/logo.png" alt="logo" class="img-fluid" style="width: 250px; height: auto; display: block; margin: 0 auto;">
        <h2 class="receipt-header">Receipt</h2>
        <div class="mt-3">
            <div class="row mb-4 mt-5">
                <div class="col-7 col-md-9">
                    <p><strong>Receipt ID:</strong> <?= htmlspecialchars($receipt_data[0]['ReceiptID']) ?></p>
                    <p><strong>Reference No:</strong> <?= htmlspecialchars($receipt_data[0]['ReferenceNo']) ?></p>
                    <p><strong>Payment Type:</strong> <?= htmlspecialchars($receipt_data[0]['PaymentType']) ?></p>
                    <p><strong>Ordered By:</strong> <?= htmlspecialchars($receipt_data[0]['Username']) ?></p>
                </div>
                <div class="col-5 col-md-3 text-start">
                    <p><strong>Date:</strong> <?= date('d M Y - H:i', strtotime($receipt_data[0]['ReceiptCreatedAt'])) ?></p>
                    <p><strong>Receive Method:</strong> <?= htmlspecialchars($receipt_data[0]['ReceiveMethod']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($receipt_data[0]['Address1']); htmlspecialchars($receipt_data[0]['Address2']) ?></p>
                    <p><?= htmlspecialchars($receipt_data[0]['PostalCode']) ?>, <?= htmlspecialchars($receipt_data[0]['State']) ?></p>
                </div>
            </div>
        </div>

        <table class="table table-bordered receipt-table">
            <thead class="table-light">
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipt_data as $item): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($item['ItemName']) ?><br>
                            <small class="text-muted">
                                <?= !empty($item['Temperature']) ? htmlspecialchars($item['Temperature']) : 'Default' ?> 
                                <?= !empty($item['MilkType']) ? '| ' . htmlspecialchars($item['MilkType']) : '' ?> 
                                <?= !empty($item['CoffeeBeanType']) ? '| ' . htmlspecialchars($item['CoffeeBeanType']) : '' ?> 
                                <?= !empty($item['Sweetness']) ? '| ' . htmlspecialchars($item['Sweetness']) : '' ?> 
                                <?= !empty($item['AddShot']) ? '| Add Shot' : '' ?>
                            </small>
                        </td>
                        <td><?= htmlspecialchars($item['ItemQuantity']) ?></td>
                        <td>RM <?= number_format($item['ItemPrice'], 2) ?></td>
                        <td>RM <?= number_format($item['ItemTotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h6 class="text-end">Sub-Total: <span class="text-muted">RM <?= number_format($subTotal, 2) ?></span></h6>
        <h6 class="text-end">SST (8%): <span class="text-muted">RM <?= number_format($sst, 2) ?></span></h6>
        <h6 class="text-end">Discount Amount: <span class="text-muted">RM <?= number_format($receipt_data[0]['DiscountAmount'], 2) ?></span></h6>
        <h4 class="mt-4 text-end">Total: RM <?= number_format($receipt_data[0]['TotalPrice'], 2) ?></h4>

        <div class="footer">
            <button onclick="window.print();" class="btn btn-primary mt-3"><i class="bi-printer-fill"> Print Receipt</i></button>
        </div>
    </div>
</body>
</html>
