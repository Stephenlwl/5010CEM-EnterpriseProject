<?php
session_start();

// Ensure session variables are set
require_once '../auth/config/database.php';
require_once '../auth/models/user.php';
require_once '../auth/objects/pagination.php';
// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// fetch user ID from session
$UserID = $_SESSION['user_id'];

// setting up the pagination 
$ordersPerPage = 1;  
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $ordersPerPage;

// query to count total orders for pagination
$countQuery = "SELECT COUNT(*) as totalOrders
               FROM `Order` o
               INNER JOIN Receipt r ON o.ReceiptID = r.ReceiptID
               WHERE r.UserID = :UserID
               AND o.OrderStatus <> 'Delivered'";

$countStmt = $db->prepare($countQuery);
$countStmt->bindParam(':UserID', $UserID, PDO::PARAM_INT);
$countStmt->execute();
$totalOrders = $countStmt->fetch(PDO::FETCH_ASSOC)['totalOrders'];

// calculating total pages
$totalPages = ceil($totalOrders / $ordersPerPage);

// query to fetch order details based on the user
$query = "SELECT o.OrderID, o.OrderStatus, o.CreatedAt AS OrderDate, r.ReceiptID, r.TotalPrice, r.PaymentType, r.ReceiveMethod, a.AddressName 
          FROM `Order` o 
          INNER JOIN Receipt r ON o.ReceiptID = r.ReceiptID
          INNER JOIN address a ON r.AddressID = a.AddressID
          WHERE r.UserID = :UserID
          AND o.OrderStatus <> 'Delivered'
          ORDER BY o.CreatedAt DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
$stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT);
$stmt->bindParam(':limit', $ordersPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT); 
$stmt->execute();
$orderData = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="../css/orderTracking.css">
    <link rel="stylesheet" href="../css/publicDefault.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="js/orderTracking.js"></script>
</head>

<body>
    <div class="container mt-5 mb-4">
        <h1 class="text-center">Orders & Tracking</h1>
        <h2 class="text-center mb-4">Order Tracking</h2>
        <hr>
        <?php if (empty($orderData)): ?>
            <div class="alert alert-warning text-center" role="alert">
                No orders have been made yet. Please place an order to track your status.
            </div>
        <?php else: ?>
            <?php renderPagination($currentPage, $totalPages); ?>
        <div class="container-borderframe">
            <div class="row mb-4 align-items-center">
                <div class="col-sm-3">
                    <h4>Current Order Status</h4>
                </div>
                <div class="col-sm-6">
                    <label class="form-label status current-status-highlight"><?php echo htmlspecialchars($orderData['OrderStatus']); ?></label>
                </div>
                <div class="col-sm-3 text-end">
                    <label class="form-label fw-semibold" id="receiveMethod"><?php echo htmlspecialchars($orderData['ReceiveMethod']); ?></label>
                </div>
            </div>
            <?php $currentOrderStatus = $orderData['OrderStatus']; ?>
                <div class="order_status_frame">
                    <input type="hidden" id="currentStatus" value="<?php echo $currentOrderStatus; ?>">
                    <div class="row text-center">
                        <div class="col-sm-2 status-item" data-status="Order Placed">
                            <i class="bi bi-bag-check"></i>
                            <p>Order Placed</p>
                        </div>
                        <div class="col-sm-2 status-item" data-status="Preparing">
                            <i class="bi bi-gear"></i>
                            <p>Preparing</p>
                        </div>
                        <div class="col-sm-2 status-item" data-status="Crafting">
                            <i class="bi bi-cup"></i>
                            <p>Crafting</p>
                        </div>
                        <div class="col-sm-2 status-item" data-status="Packing">
                            <i class="bi bi-box-seam"></i>
                            <p>Packing</p>
                        </div>
                        <!-- check the receive method to showing the correspoding status -->
                        <?php if ($orderData['ReceiveMethod'] === 'Pickup'): ?>
                            <div class="col-sm-2 status-item" data-status="Ready to Pickup">
                                <i class="bi bi-shop"></i>
                                <p>Ready to Pickup</p>
                            </div>
                        <?php elseif ($orderData['ReceiveMethod'] === 'Delivery'): ?>
                            <div class="col-sm-2 status-item" data-status="Out for Delivery">
                                <i class="bi bi-truck"></i>
                                <p>Out for Delivery</p>
                            </div>
                        <?php endif; ?>
                        <div class="col-sm-2 status-item" data-status="Delivered">
                            <i class="bi bi-truck"></i>
                            <p>Delivered</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                <div class="row mb-3 align-items-center">
                    <div class="col-8 col-sm-8">
                        <label class="form-label">Order ID: <?php echo htmlspecialchars($orderData['OrderID']); ?></label>
                        <p><?php echo htmlspecialchars($orderData['OrderDate']); ?></p>
                    </div>
                    <div class="col-4 col-sm-4 text-end">
                        <label>Receipt</label>
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="window.open('print_receipt.php?receipt_id=<?= $orderData['ReceiptID'] ?>', '_blank')">Print</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <label for="payment_type" class="form-label">Payment Type:</label>
                        <p><?php echo htmlspecialchars($orderData['PaymentType']); ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label for="order_type" class="form-label">Receive Method:</label>
                        <p><?php echo htmlspecialchars($orderData['ReceiveMethod']); ?></p>
                    </div>
                    <div class="col-sm-3">
                        <label for="order_type" class="form-label">Address Name:</label>
                        <p><?php echo htmlspecialchars($orderData['AddressName']); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mt-4">
                    <a href="#" id="viewOrderDetailsLink" data-order-id="<?php echo htmlspecialchars($orderData['OrderID']); ?>" data-bs-toggle="modal" data-bs-target="#viewOrderDetailsModal">View Order Details</a>
                    </div>
                    <div class="col-sm-9 text-end">
                        <label for="order_total" class="form-label">Total:</label>
                        <h3>RM <?php echo htmlspecialchars($orderData['TotalPrice']); ?></h3>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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
