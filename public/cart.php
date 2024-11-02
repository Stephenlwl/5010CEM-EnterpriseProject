<?php
session_start();

require_once 'auth/config/database.php';
require_once 'auth/models/user.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

if (!isset($_SESSION['user_id'])) {
    $UserID = null;
 } else {
     $UserID = $_SESSION['user_id'];
     $query = "SELECT * FROM users WHERE UserID = :UserID";
     $stmt = $db->prepare($query);
     $stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT); 
     $stmt->execute();
 
     $userData = $stmt->fetch(PDO::FETCH_ASSOC);
 
 }
 
// Fetch cart items for the logged-in user
$query = "SELECT c.CartID, c.ItemID, c.Quantity, c.PersonalItemID, m.ItemName, m.ItemPrice,
                 pi.Temperature, pi.MilkType, pi.CoffeeBeanType, pi.Sweetness, pi.AddShot, m.ItemQuantity
          FROM cart AS c
          JOIN menu AS m ON c.ItemID = m.ItemID
          LEFT JOIN personal_item pi ON c.PersonalItemID = pi.PersonalItemID
          WHERE c.UserID = :userID AND c.Status = 'Active'";

$stmt = $db->prepare($query);
$stmt->bindParam(':userID', $UserID);
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/publicDefault.css">
    <link rel="stylesheet" href="css/cart.css">
    <script src="js/cart.js"></script>

</head>

<body>
<?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH."nav.php"); 
    ?>
    <div class="container mt-5">
        <div class="row"> 
            <div class="col-md-10">
                <h2 class="summary-header mb-3"><div class="bi bi-cart"> Current Order</div></h2>
                <div class="p-4 border rounded box-float">
                    <h4 class="mb-3">Your chosen items, freshly prepared by Rimberio Café.</h4>
                    <!-- Order Table -->
                    <table class="table table-bordered order-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price (RM)</th>
                                <th>Quantity</th>
                                <th>Total (RM)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <?php if (empty($items)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                No Item in Cart!
                            </div>
                        <?php else: ?>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <input type="hidden" id="item-stock-quantity-<?= $item['ItemID'] ?>" value="<?= htmlspecialchars($item['ItemQuantity']) ?>">
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['ItemName']) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= !empty($item['Temperature']) ? htmlspecialchars($item['Temperature']) : 'Default' ?> 
                                            <?= !empty($item['MilkType']) ? '| ' . htmlspecialchars($item['MilkType']) : '' ?> 
                                            <?= !empty($item['CoffeeBeanType']) ? '| ' . htmlspecialchars($item['CoffeeBeanType']) : '' ?> 
                                            <?= !empty($item['Sweetness']) ? '| ' . htmlspecialchars($item['Sweetness']) : '' ?> 
                                            <?= !empty($item['AddShot']) ? '| ' . htmlspecialchars($item['AddShot']) : '' ?>
                                        </small>
                                    </td>
                                    <td><?= number_format($item['ItemPrice'], 2) ?></td>
                                    <td>
                                        <div class="input-group"  style="max-width: 120px;">
                                            <!-- minus button -->
                                            <button class="btn btn-outline-secondary btn-minus" type="button" onclick="updateQuantity('minus', <?= $item['ItemID'] ?>, <?= $UserID ?>, <?= $item['CartID']?>)">&#8722;</button>

                                            <!-- quantity input -->
                                            <input type="text" class="form-control text-center" id="quantity_<?= $item['ItemID'] ?>" value="<?= htmlspecialchars($item['Quantity']) ?>" readonly>

                                            <!-- plus button -->
                                            <button class="btn btn-outline-secondary btn-plus" type="button" onclick="updateQuantity('plus', <?= $item['ItemID'] ?>, <?= $UserID ?>, <?= $item['CartID']?>)">&#43;</button>
                                        </div>
                                    </td>
                                    <td><?= number_format($item['ItemPrice'] * $item['Quantity'], 2) ?></td>
                                    <td>
                                        <button class="btn btn-danger" onclick="removeItem(<?= $item['ItemID'] ?>, <?= $UserID ?>, <?= $item['CartID']?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>

                    <!-- Summary Section -->
                    <div class="total-section text-end mt-4">
                        <p><strong>Sub-Total:</strong> RM <?= number_format($subTotal, 2) ?></p>
                        <p><strong>SST 8%:</strong> RM <?= number_format($sst, 2) ?></p>
                        <h4 class="border-top pt-2">Total: RM <span id="final-total"><?= number_format($total, 2) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100 mb-3" onclick="window.location.href='menu.php'">
                    <i class="bi bi-arrow-left"></i> Continue to Order
                </button>
                <button class="btn btn-success w-100" onclick="window.location.href='orderSummary.php'">
                    Proceed to Checkout <i class="bi bi-arrow-right"></i>
            </div>
        </div>
    </div>
    <?php include($IPATH."footer.html"); ?>

</body>

</html>