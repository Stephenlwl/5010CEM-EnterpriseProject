<?php
session_start();

// Ensure session variables are set
require_once '../auth/config/database.php';
require_once '../auth/models/user.php';
require_once '../auth/objects/pagination.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Fetch user ID from session
$UserID = $_SESSION['user_id'];

// setting up the pagination 
$ordersPerPage = 3;  
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $ordersPerPage;

// query to count total orders for pagination
$countQuery = "SELECT COUNT(*) as totalOrders
               FROM `Order` o
               INNER JOIN Receipt r ON o.ReceiptID = r.ReceiptID
               WHERE r.UserID = :UserID";

$countStmt = $db->prepare($countQuery);
$countStmt->bindParam(':UserID', $UserID, PDO::PARAM_INT);
$countStmt->execute();
$totalOrders = $countStmt->fetch(PDO::FETCH_ASSOC)['totalOrders'];

// calculating total pages
$totalPages = ceil($totalOrders / $ordersPerPage);

// Query to fetch order details based on the user
$query = "SELECT o.OrderID, o.OrderStatus, o.CreatedAt AS OrderDate, r.ReceiptID, r.TotalPrice, r.PaymentType, r.ReceiveMethod, a.AddressName 
          FROM `Order` o 
          INNER JOIN Receipt r ON o.ReceiptID = r.ReceiptID
          INNER JOIN address a ON r.AddressID = a.AddressID
          WHERE r.UserID = :UserID
          ORDER BY o.CreatedAt DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
$stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT); 
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
    <title>Order Tracking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/orderHistory.css">
    <link rel="stylesheet" href="../css/publicDefault.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="js/orderHistory.js"></script>
</head>

<body>
    <div class="container mt-5 mb-4">
        <h1 class="text-center">Orders & Tracking</h1>
        <h2 class="text-center mb-4">Order History</h2>
        <hr>
        <?php if (!empty($order_data)): ?>
            <div class="row">
                <div class="col-sm-4">
                    <label for="month" class="form-label">Filter by Month:</label>
                    <select class="form-select" id="month">
                        <option selected>All</option>
                        <option>January</option>
                        <option>February</option>
                        <option>March</option>
                        <option>April</option>
                        <option>May</option>
                        <option>June</option>
                        <option>July</option>
                        <option>August</option>
                        <option>September</option>
                        <option>October</option>
                        <option>November</option>
                        <option>December</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="receiveMethod" class="form-label">Filter by Receive Method:</label>
                    <select class="form-select" id="month">
                        <option selected>All</option>
                        <option>Delivery</option>
                        <option>Pickup</option>
                    </select>
                </div>
            </div>
            <?php foreach ($order_data as $order): ?>
                <div class="container-borderframe p-3 mb-3">
                    <div class="row">
                        <div class="col-8 col-sm-8">
                            <label class="form-label">Order ID: <?= $order['OrderID'] ?></label>
                            <p class="mb-1"><?= date('d M Y - H:i', strtotime($order['OrderDate'])) ?></p>
                        </div>
                        <div class="col-4 col-sm-4 text-end">
                            <div class="mb-2">
                            <!-- Order Status with dynamic background color -->
                            <label class="form-label">Status:</label>
                            <span class="badge" style="
                                background-color: 
                                    <?php 
                                        switch ($order['OrderStatus']) {
                                            case 'Delivered':
                                                echo '#28a745'; // green for delivered
                                                break;
                                            case 'Packing':
                                            case 'Crafting':
                                            case 'Preparing':
                                                echo '#ffc107'; // yellow for Packing, Crafting, Preparing
                                                break;
                                            case 'Ready to Pickup':
                                                echo '#fd7e14'; // orange for Ready to Pickup
                                                break;
                                            default:
                                                echo '#f8f9fa'; // default background color (the order placed)
                                        }
                                    ?>; 
                                color: <?= ($order['OrderStatus'] === 'Order Placed') ? '#000' : '#fff' ?>;
                                box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1);
                                padding: 5px 10px;
                                border-radius: 5px;
                            "><?= htmlspecialchars($order['OrderStatus']) ?></span>
                            </div>
                            <label class="form-label">Receipt</label>
                            <button type="button" class="btn btn-primary btn-sm me-2" onclick="window.open('print_receipt.php?receipt_id=<?= $order['ReceiptID'] ?>', '_blank')">Print</button>                            
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="payment_type" class="form-label">Payment Type:</label>
                            <p><?php echo htmlspecialchars($order['PaymentType']); ?></p>
                        </div>
                        <div class="col-sm-3">
                            <label for="order_type" class="form-label">Receive Method:</label>
                            <p><?php echo htmlspecialchars($order['ReceiveMethod']); ?></p>
                        </div>
                        <div class="col-sm-3">
                            <label for="order_type" class="form-label">Address Name:</label>
                            <p><?php echo htmlspecialchars($order['AddressName']); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 p-3">
                            <a href="#" id="viewOrderDetailsLink" data-order-id="<?php echo htmlspecialchars($order['OrderID']); ?>" data-bs-toggle="modal" data-bs-target="#viewOrderDetailsModal">View Order Details</a>
                        </div>
                        <div class="col-sm-6 text-end">
                            <label for="order_total" class="form-label">Total:</label>
                            <h4 class="mb-0">RM <?= number_format($order['TotalPrice'], 2) ?></h4>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php renderPagination($currentPage, $totalPages); ?>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                No orders have been made yet.
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal for Order Details -->
    <div class="modal fade" id="viewOrderDetailsModal" tabindex="-1" aria-labelledby="viewOrderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOrderDetailsModalLabel">Your Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class=row>
                        <div class="col-8 col-sm-8">
                            <h6>Your Order</h6>
                        </div>  
                        <div class="col-4 col-sm-4">
                            <h6>Quantity</h6> 
                        </div>
                    </div>
                    <div id="order-details-body" class="order-details-body">
                        <!-- order details will be dynamically populated under this section -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
