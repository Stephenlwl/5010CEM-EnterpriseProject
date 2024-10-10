<?php 
session_start();

require_once 'auth/config/database.php';
require_once 'auth/models/user.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

if (isset($_SESSION['user_id'])) {
    $UserID = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE UserID = :UserID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT); 
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $userData = null;
}

if (!isset($_SESSION['user_id']) || empty($userData)) {
    // Redirect to login page or show an error
    header("Location: login.php"); 
    exit;
}

$userId = $_SESSION['user_id']; 

// Fetch cart items for the logged-in user
$query = "SELECT c.CartID, c.ItemID, c.Quantity, c.PersonalItemID, m.ItemName, m.ItemPrice
          FROM cart AS c
          JOIN menu AS m ON c.ItemID = m.ItemID
          WHERE c.UserID = :userID AND c.Status = 'Active'";

$stmt = $db->prepare($query);
$stmt->bindParam(':userID', $userId);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize totals
$subTotal = 0;

// Calculate sub-total and SST
foreach ($items as $item) {
    $subTotal += $item['ItemPrice'] * $item['Quantity'];
}
$sst = $subTotal * 0.08; 
$total = $subTotal + $sst;

?>
<!-- get the total of the amount -->
<script>
    var totalAmount = <?= $total ?>;
    const cartItems = <?php echo json_encode($items); ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/orderSummary.css">
    <link rel="stylesheet" href="css/publicDefault.css">
    <script src="js/orderSummary.js"></script>
    <script src="https://sandbox.paypal.com/sdk/js?client-id=ARaTuG1FMkQcMQep9aCDEBOnf8s4-WNCqOdGuTzTd7h5CB5cZrU9iG0DRofd9ixjTaz4OqULeI5e7syR&currency=MYR"></script>
</head>
<body>
    <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH."nav.php");
    ?>

    <div class="container mt-3">
        <div class="row">
            <input type="hidden" id="userId" value="<?php echo htmlspecialchars($userData['UserID']); ?>">
            <div class="mb-3">
                <button class="btn btn-secondary shadow" onclick="window.history.back()"> <!-- back to preious history screen -->
                    <i class="bi bi-arrow-left"></i> Back
                </button>
            </div>
            <!-- Left Column: Order Summary -->
            <div class="col-md-7">
                <div class="p-4 border rounded box-float">
                    <h2 class="summary-header mb-3">Order Summary</h2>
                    <h4 class="mb-3">Your Order</h4>

                    <!-- Order Table -->
                    <table class="table table-bordered order-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price (RM)</th>
                                <th>Quantity</th>
                                <th>Total (RM)</th>
                            </tr>
                        </thead>
                        <?php if (empty($items)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                No Item in Cart!
                            </div>
                        <?php else: ?>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['ItemName']) ?></td>
                                    <td><?= number_format($item['ItemPrice'], 2) ?></td>
                                    <td><?= htmlspecialchars($item['Quantity']) ?></td>
                                    <td><?= number_format($item['ItemPrice'] * $item['Quantity'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>

                    <!-- Summary Section -->
                    <div class="total-section text-end mt-4">
                        <p><strong>Sub-Total:</strong> RM <?= number_format($subTotal, 2) ?></p>
                        <p><strong>SST 8%:</strong> RM <?= number_format($sst, 2) ?></p>
                        <h4 class="border-top pt-2">Total: RM <?= number_format($total, 2) ?></h4>
                    </div>
                </div>
            </div>

            <!-- Right Column: Delivery and Payment Method -->
            <div class="col-md-4 border-start ms-md-5">
                <div class="p-2 ms-md-5">
                    <h2 class="mb-4">Delivery & Payment</h2>
                    
                    <?php if (empty($items)): ?>
                        <div class="alert alert-warning text-center" role="alert" style="font-size: 1.2rem; padding: 1.5rem; border: 2px solid #ffc107;">
                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: #ff9900;"></i>
                            <strong>Important Notice:</strong><br>
                                Your cart does not contain any items to proceed with the order. <br>
                            <span style="font-weight: 500;">Please add an item before selecting a delivery method and payment option!</span>
                        </div>
                    <?php else: ?>
                        <!-- Delivery Method Section -->
                        <h5>Delivery Method</h5>
                        <div class="delivery-method">
                            <div class="form-check card p-2 mb-2 shadow-sm">
                                <input class="form-check-input" type="radio" name="deliveryMethod" id="deliveryHome" value="home" checked>
                                <label class="form-check-label" for="deliveryHome">
                                    <i class="bi bi-house-door me-2"></i>Home Delivery
                                </label>
                                <div id="addressSection" style="display: block;">
                                    <div id="addressOptions" class="mt-2"></div>
                                    <button type="button" class="btn btn-primary me-2 mx-auto" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New Address</button> 
                                </div>
                            </div>
                            <div class="form-check card p-2 mb-2 shadow-sm">
                                <input class="form-check-input" type="radio" name="deliveryMethod" id="deliveryPickup" value="pickup">
                                <label class="form-check-label" for="deliveryPickup">                    
                                    <i class="bi bi-shop-window me-2"></i>Store Pickup                                
                                </label>
                                <div id="storeAddressSection" style="display: block;">
                                    <div id="storeAddressOptions" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                            
                        <!-- Payment Option Section -->
                        <h5>Payment Option</h5>
                        <div class="payment-option">
                            <div class="form-check card p-2 mb-2 shadow-sm">
                                <input class="form-check-input" type="radio" name="paymentOption" id="paymentCard" value="Card" checked>
                                <label class="form-check-label" for="paymentCard">
                                    <i class="bi bi-credit-card me-2"></i>Credit/Debit Card
                                </label>
                            </div>
                            <div class="form-check card p-2 mb-2 shadow-sm">
                                <input class="form-check-input" type="radio" name="paymentOption" id="paymentPaypal" value="Paypal">
                                <label class="form-check-label" for="paymentPaypal">
                                    <i class="bi bi-paypal me-2"></i>Paypal
                                </label>
                                <!-- PayPal Button Container -->
                                <div id="paypal-button-container" class="mt-2" style="display:none;"></div>
                            </div>
                            <div class="form-check card p-2 mb-2 shadow-sm">
                                <input class="form-check-input" type="radio" name="paymentOption" id="paymentTNG" value="E-Wallet">
                                <label class="form-check-label" for="paymentTNG">
                                    <i class="bi bi-wallet me-2"></i>Touch 'n Go eWallet
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAddressForm">
                        <div class="mb-3">
                            <label for="addAddressName" class="form-label">Address Name</label>
                            <input type="text" class="form-control" id="addAddressName" name="address_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="addAddress1" name="address1" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="addAddress2" name="address2">
                        </div>
                        <div class="mb-3">
                            <label for="addPostalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="addPostalCode" name="postal_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="addState" class="form-label">State</label>
                            <input type="text" class="form-control" id="addState" name="state" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Address</button>
                    </form>
                </div>
            </div>
        </div>
    </div>   

</body>
</html>
