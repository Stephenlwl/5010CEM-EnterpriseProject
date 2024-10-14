<?php
session_start();

// Ensure session variables are set
require_once '../auth/config/database.php';
require_once '../auth/objects/pagination.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Fetch admin ID from session
$admin_id = $_SESSION['AdminID'];

// setting up pagination 
$ordersPerPage = 9; 

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $ordersPerPage;

// query to count total orders for pagination purposes
$countQuery = "SELECT COUNT(*) as totalOrders
               FROM `Order` o
               INNER JOIN Receipt r ON o.ReceiptID = r.ReceiptID";

$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalOrders = $countStmt->fetch(PDO::FETCH_ASSOC)['totalOrders'];

// calculating total pages
$totalPages = ceil($totalOrders / $ordersPerPage);

// query to fetch order details
$query = "SELECT o.OrderID, o.OrderStatus, o.CreatedAt AS OrderDate, 
                 r.ReceiveMethod, a.AddressName, r.ReceiptID,
                 rd.ItemID, rd.ItemQuantity, m.ItemName,
                 pi.Temperature, pi.MilkType, pi.CoffeeBeanType, pi.Sweetness, pi.AddShot,
                 u.Username, u.Email
            FROM `Order` o 
            LEFT JOIN Receipt r ON o.ReceiptID = r.ReceiptID
            LEFT JOIN address a ON r.AddressID = a.AddressID
            LEFT JOIN receipt_details rd ON r.ReceiptID = rd.ReceiptID
            LEFT JOIN menu m ON rd.ItemID = m.ItemID
            LEFT JOIN personal_item pi ON rd.PersonalItemID = pi.PersonalItemID
            LEFT JOIN users u ON r.UserID = u.UserID
            ORDER BY o.CreatedAt DESC
            LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
$stmt->bindParam(':limit', $ordersPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT); 
$stmt->execute();
$order_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <script src="js/orderDashboard.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <!-- sidebar -->
                <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/admin/inc/";
                include($IPATH."nav.php"); ?>
            </div>
            <div class="col-md-10">
                <div class="main-content">
                    <h1 class="text-center mb-4">Admin - Order Management</h1>
                    <hr class="mb-4">

                    <!-- Filters Section -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <label for="orderStatus" class="form-label">Filter by Order Status:</label>
                            <select class="form-select" id="orderStatusFilter">
                                <option selected value="All">All</option>
                                <option value="Order Placed">Order Placed</option>
                                <option value="Preparing">Preparing</option>
                                <option value="Crafting">Crafting</option>
                                <option value="Packing">Packing</option>
                                <option value="Ready to Pickup">Ready to Pickup</option>
                                <option value="Out for Delivery">Out for Delivery</option>
                                <option value="Order Completed">Order Completed</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="receiveMethod" class="form-label">Filter by Receive Method:</label>
                            <select class="form-select" id="receiveMethodFilter">
                                <option selected value="All">All</option>
                                <option value="Delivery">Delivery</option>
                                <option value="Pickup">Pickup</option>
                            </select>
                        </div>
                    </div>
                                    
                    <!-- orders -->
                    <?php if (!empty($order_data)): ?>
                    <div class="row">
                        <?php 
                        // create an array to group orders by using OrderID
                        $groupedOrders = [];
                        foreach ($order_data as $order) {
                            $groupedOrders[$order['OrderID']][] = $order;
                        }
                        // iterate through the grouped orders
                        foreach ($groupedOrders as $orderID => $items): 
                            $firstItem = $items[0]; // get first item for displaying order details
                        ?>

                        <div class="col-4 col-lg-4 order"
                            orderStatus="<?= htmlspecialchars($firstItem['OrderStatus']) ?>" 
                            receiveMethod="<?= htmlspecialchars($firstItem['ReceiveMethod']) ?>">
                            <div class="card mb-4 shadow" style="padding-left: 20px;" >
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h5>Order ID: <?= htmlspecialchars($firstItem['OrderID']) ?></h5>
                                            <p>Order Date: <div class="text-muted"><?= date('d M Y - H:i', strtotime($firstItem['OrderDate'])) ?></div></p>
                                        </div>
                                        <div class="col-md-5 text-end">
                                        <span class="badge" style="
                                                background-color: 
                                                    <?php 
                                                        switch ($firstItem['OrderStatus']) {
                                                            case 'Order Completed':
                                                                echo '#28a745'; // green for order completed
                                                                break;
                                                            case 'Packing':
                                                            case 'Crafting':
                                                            case 'Preparing':
                                                                echo '#ffc107'; // yellow for Packing, Crafting, Preparing
                                                                break;
                                                            case 'Ready to Pickup':
                                                            case 'Out for Delivery':
                                                                echo '#fd7e14'; // orange for Ready to Pickup
                                                                break;
                                                            case 'Order Placed':
                                                                echo '#898989'; // grey for Order Placed
                                                                break;
                                                            default:
                                                                echo '#f8f9fa'; // default background color (the order placed)
                                                        }
                                                    ?>; 
                                                color: <?= ($order['OrderStatus'] === 'Order Placed') ? '#000' : '#fff' ?>;
                                                box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1);
                                                padding: 5px 10px;
                                                border-radius: 5px;
                                            "><?= htmlspecialchars($firstItem['OrderStatus']) ?></span>
                                            <button class="btn btn-primary btn-sm mt-3 shadow-sm" onclick="window.open('../userProfileSettings/print_receipt.php?receipt_id=<?= htmlspecialchars($firstItem['ReceiptID']) ?>', '_blank')">
                                                Print Receipt
                                            </button>
                                            <label class="text-muted">
                                                <?= htmlspecialchars($firstItem['ReceiveMethod']) ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Item Name</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Display order items -->
                                                    <?php foreach ($items as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <?= htmlspecialchars($item['ItemName']) ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?= !empty($item['Temperature']) ? htmlspecialchars($item['Temperature']) : 'Default' ?> 
                                                                <?= !empty($item['MilkType']) ? '| ' . htmlspecialchars($item['MilkType']) : '' ?> 
                                                                <?= !empty($item['CoffeeBeanType']) ? '| ' . htmlspecialchars($item['CoffeeBeanType']) : '' ?> 
                                                                <?= !empty($item['Sweetness']) ? '| ' . htmlspecialchars($item['Sweetness']) : '' ?> 
                                                                <?= !empty($item['AddShot']) ? '| Add Shot' : '' ?>
                                                            </small>
                                                        </td>
                                                        <td><?= htmlspecialchars($item['ItemQuantity']) ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row bg-warning-subtle text-warning-emphasis rounded">
                                        <div class="col-md-4 mt-4 text-end">
                                            <p><strong>Order Status:</strong></p>
                                        </div>
                                        <div class="col-md-8 mt-3 mb-3 text-end">
                                            <select class="form-select orderStatus" 
                                                    data-order-id="<?= htmlspecialchars($firstItem['OrderID']) ?>" 
                                                    data-receive-method="<?= htmlspecialchars($firstItem['ReceiveMethod']) ?>">
                                                <option value="Order Placed" <?= $firstItem['OrderStatus'] == 'Order Placed' ? 'selected' : '' ?> 
                                                        <?= $firstItem['OrderStatus'] != 'Order Placed' ? 'disabled' : '' ?>>
                                                    Order Placed
                                                </option>
                                                <option value="Preparing" <?= $firstItem['OrderStatus'] == 'Preparing' ? 'selected' : '' ?> 
                                                        <?= $firstItem['OrderStatus'] != 'Preparing' && $firstItem['OrderStatus'] != 'Order Placed' ? 'disabled' : '' ?>>
                                                    Preparing
                                                </option>
                                                <option value="Crafting" <?= $firstItem['OrderStatus'] == 'Crafting' ? 'selected' : '' ?> 
                                                        <?= $firstItem['OrderStatus'] != 'Crafting' && $firstItem['OrderStatus'] != 'Preparing' && $firstItem['OrderStatus'] != 'Order Placed' ? 'disabled' : '' ?>>
                                                    Crafting
                                                </option>
                                                <option value="Packing" <?= $firstItem['OrderStatus'] == 'Packing' ? 'selected' : '' ?> 
                                                        <?= $firstItem['OrderStatus'] != 'Packing' && $firstItem['OrderStatus'] != 'Crafting' && $firstItem['OrderStatus'] != 'Preparing' && $firstItem['OrderStatus'] != 'Order Placed' ? 'disabled' : '' ?>>
                                                    Packing
                                                </option>
                                                <?php if ($firstItem['ReceiveMethod'] == 'Pickup'): ?>
                                                    <option value="Ready to Pickup" <?= $firstItem['OrderStatus'] == 'Ready to Pickup' ? 'selected' : '' ?> 
                                                            <?= $firstItem['OrderStatus'] != 'Packing' && $firstItem['OrderStatus'] != 'Ready to Pickup' ? 'disabled' : '' ?>>
                                                        Ready to Pickup
                                                    </option>
                                                <?php else: ?>
                                                    <option value="Out for Delivery" <?= $firstItem['OrderStatus'] == 'Out for Delivery' ? 'selected' : '' ?> 
                                                            <?= $firstItem['OrderStatus'] != 'Packing' && $firstItem['OrderStatus'] != 'Out for Delivery' ? 'disabled' : '' ?>>
                                                        Out for Delivery
                                                    </option>
                                                <?php endif; ?>
                                                <option value="Order Completed" <?= $firstItem['OrderStatus'] == 'Order Completed' ? 'selected' : '' ?> 
                                                        <?= $firstItem['OrderStatus'] != 'Order Completed' && $firstItem['OrderStatus'] != 'Ready to Pickup' && $firstItem['OrderStatus'] != 'Out for Delivery' ? 'disabled' : '' ?>>
                                                        Order Completed
                                                </option>
                                            </select>
                                            <button class="btn btn-success btn-sm mt-2 update-status shadow-sm" id="updateStatusButon"
                                                    data-order-id="<?= htmlspecialchars($firstItem['OrderID']) ?>" 
                                                    data-user-name="<?= htmlspecialchars($firstItem['Username']) ?>"
                                                    data-user-email="<?= htmlspecialchars($firstItem['Email']) ?>">
                                                Update Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- pagination -->
                    <?php renderPagination($currentPage, $totalPages); ?>

                    <?php else: ?>
                    <div class="alert alert-warning text-center">
                        No order found.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>