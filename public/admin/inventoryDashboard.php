<?php
session_start();
require_once '../auth/config/database.php';
require_once '../auth/objects/pagination.php';

$database = new Database_Auth();
$db = $database->getConnection();

$admin_id = $_SESSION['AdminID'];

if (!$admin_id){
    header("Location: adminLogin.php");
    exit();
}

// pagination settings
$itemsPerPage = 5;  
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// fetch total number of products
$countQuery = "SELECT COUNT(*) as totalItems FROM menu";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['totalItems'];

$totalPages = ceil($totalItems / $itemsPerPage);

// fetch the products from menu table
$query = "SELECT * FROM menu ORDER BY CreatedAt DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT); 
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$anyRestocked = false; // flag to track if any product was auto restocked

// function to auto restock an item
function autoRestock($itemID, $restockAmount) {
    global $db; 
    global $anyRestocked;
    // fetch current stock for the item
    $query = "SELECT ItemQuantity FROM menu WHERE ItemID = :itemID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':itemID', $itemID);
    $stmt->execute();
    $currentStock = $stmt->fetchColumn();

    // perform restock
    $newQuantity = $currentStock + $restockAmount;
    
    // update the stock quantity in db
    $updateQuery = "UPDATE menu SET ItemQuantity = :newQuantity, RestockDate = NOW() WHERE ItemID = :itemID";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':newQuantity', $newQuantity);
    $updateStmt->bindParam(':itemID', $itemID);
    if ($updateStmt->execute()) {
        $anyRestocked = true;
        return true; // restock was successful
    }
    return false; // restock failed
}

// fetch the total quantity sold from receipt_detail to deduct from stock
foreach ($products as &$product) {

    $currentStock = $product['ItemQuantity'];
    $stockThreshold = $product['StockThreshold'];  
    
    if ($currentStock < $stockThreshold) {
        $product['needsRestock'] = true; 
        autoRestock($product['ItemID'], $product['AutoRestockQuantity']);
    } else {
        $product['needsRestock'] = false;
    }
}

$autoRestockMessage = '';
if ($anyRestocked) {
    $autoRestockMessage = 'Our system has detected that some products were low in stock. As a result, it has automatically restocked those items. Please refresh the page to see the updated stock quantities.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <script src="js/inventoryDashboard.js"></script>
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
                    <h1 class="mb-3 text-center">Inventory Dashboard</h1>
                    <hr>

                    <!-- Product Stock Management Table -->
                    <div class="row mb-4">
                        <div class="col-md-11 container shadow p-2 rounded">
                            <h4 class="mb-3">Manage Stock</h4>
                            <?php if (!empty($autoRestockMessage)): ?>
                                <div class="alert alert-info" role="alert">
                                    <?= htmlspecialchars($autoRestockMessage) ?>
                                </div>
                            <?php endif; ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Item Type</th>
                                        <th>Current Stock Qty</th>
                                        <th>Restock Warning</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as &$product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['ItemName']) ?></td>
                                        <td><?= htmlspecialchars($product['ItemType']) ?></td>
                                        <td><?= htmlspecialchars($product['ItemQuantity']) ?></td>
                                        <td>
                                            <?php if ($product['needsRestock']): ?>
                                                <span class="badge bg-danger p-2">Needs Restock</span>
                                            <?php else: ?>
                                                <span class="badge bg-success p-2">Sufficient Stock</span> 
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y - H:i', strtotime($product['CreatedAt'])) ?></td>
                                        <td><?= date('d M Y - H:i', strtotime($product['UpdatedAt'])) ?></td>
                                        <td>
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editStockModal" onclick="editStock('<?= $product['ItemID'] ?>', '<?= $product['ItemQuantity'] ?>', '<?= $product['StockThreshold'] ?>', '<?= htmlspecialchars($product['ItemName']) ?>', '<?= htmlspecialchars($product['AutoRestockQuantity']) ?>')"><span class="bi bi-pen"> Add Stock</span></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php renderPagination($currentPage, $totalPages); ?>
                        </div>
                    </div>

                    <!-- Edit Stock Modal -->
                    <div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editStockModalLabel">Add <span class="text-danger" id="productName"></span> Stock Quantity</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editStockForm">
                                        <input type="hidden" id="itemID" name="itemID">
                                        <div class="mb-3">
                                            <label for="currentQuantity" class="form-label">Current Stock Quantity</label>
                                            <input type="number" class="form-control" id="currentQuantity" name="currentQuantity" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="newQuantity" class="form-label">Add Stock Quantity</label>
                                            <input type="number" class="form-control" id="newQuantity" name="newQuantity" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stockThreshold" class="form-label">Stock Threshold</label>
                                            <input type="number" class="form-control" id="stockThreshold" name="stockThreshold" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="autoRestockQuantity" class="form-label">Set auto restock quantity</label>
                                            <input type="number" class="form-control" id="autoRestockQuantity" name="autoRestockQuantity" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
