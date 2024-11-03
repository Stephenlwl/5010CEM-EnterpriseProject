<?php
session_start();

require_once '../auth/config/database.php';  

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

// Fetch favorite items with item details
$query = "SELECT 
            p.PersonalItemID, 
            p.Temperature, 
            p.Sweetness, 
            p.AddShot, 
            p.MilkType, 
            p.CoffeeBeanType,
            m.ItemID, 
            m.ItemName, 
            m.ItemPrice, 
            m.ItemType, 
            m.ImagePath,
            m.ItemQuantity
          FROM personal_item AS p
          JOIN menu AS m ON p.ItemID = m.ItemID
          WHERE p.UserID = :UserID AND p.Favourite = 1
          ORDER BY m.ItemName";

$stmt = $db->prepare($query);
$stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT);
$stmt->execute();
$favoriteItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorite Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/allMenu.css">
    <script src="js/menuSettings.js"></script>
</head>

<body>
    <div class="section-title">Your Favorite Items</div>
    <div class="menu-container">
        <input type="hidden" id="userId" value="<?php echo htmlspecialchars($UserID); ?>">

        <?php if ($favoriteItems): ?>
            <?php foreach ($favoriteItems as $item): ?>
                <input type="hidden" id="item-stock-quantity-<?php echo htmlspecialchars($item['ItemID']); ?>" value="<?php echo htmlspecialchars($item['ItemQuantity']); ?>">
                <input type="hidden" id="item-quantity-in-cart-<?php echo htmlspecialchars($item['ItemID']); ?>" value="<?php echo htmlspecialchars($item['Quantity'] ?? 0); ?>">

                <div class="menu-item card m-2 p-1" style="width: 15rem;">
                    <div class="card-body text-center">
                        <img src="../auth/api/get_image_from_menu.php?ItemID=<?php echo htmlspecialchars($item['ItemID']); ?>" 
                            onerror="this.onerror=null; this.src='../img/coffee-placeholder.jpg';" 
                            alt="<?php echo htmlspecialchars($item['ItemName']); ?>" 
                            class="img-thumbnail mb-3" 
                            style="max-width: 170px;">
                        
                        <input type="hidden" id="favouriteId" value="<?php echo htmlspecialchars($item['PersonalItemID']); ?>">

                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($item['ItemName']); ?></h5>

                        <div class="mb-2">
                            <span class="price">Price: RM<?php echo number_format($item['ItemPrice'], 2); ?></span>
                        </div>

                        <ul class="list-unstyled text-start small mb-3">
                            <li><i class="bi bi-thermometer-half me-2"></i><strong>Temperature:</strong> <?php echo htmlspecialchars($item['Temperature']); ?></li>
                            <li><i class="bi bi-droplet me-2"></i><strong>Sweetness:</strong> <?php echo htmlspecialchars($item['Sweetness']); ?></li>
                            <li><i class="bi bi-cup-straw me-2"></i><strong>Extra Shot:</strong> <?php echo htmlspecialchars($item['AddShot'] ?? 'N/A'); ?></li>
                            <li><i class="bi bi-cup-hot me-2"></i><strong>Milk Type:</strong> <?php echo htmlspecialchars($item['MilkType']); ?></li>
                            <!-- custom SVG for coffee bean -->
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M4 0C2.36 0 1 1.35 1 3 1 8 8 16 13 16c1.65 0 3-1.36 3-3C16 8 8 0 4 0zm8.8 13.5c-.62 0-1.25-.12-1.85-.36C8.87 11.78 6.04 9.23 3.1 5.3a6.7 6.7 0 0 1-.44-.6c1.3-.33 2.67-.5 4.04-.5 3.56 0 6.56 1.5 8.06 3.64.29.44.5.92.6 1.42-.33 1.5-1.98 3.24-3.56 3.24z"/>
                                </svg>
                                <strong>Coffee Bean:</strong> <?php echo htmlspecialchars($item['CoffeeBeanType']); ?>
                            </li>
                        </ul>


                        <?php if ($item["ItemQuantity"] > 0 && (!isset($item["Quantity"]) || $item["Quantity"] < $item["ItemQuantity"])): ?>
                            <button type="button" class="btn btn-primary w-100" 
                                onclick="addFavouriteToCart(<?php echo $item['ItemID']; ?>, <?php echo htmlspecialchars($item['ItemQuantity']); ?>, <?php echo htmlspecialchars($item['Quantity'] ?? 0); ?>)">
                                Add to Cart
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary w-100" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">You haven't added any favorites yet.</p>
        <?php endif; ?>
    </div>
</body>

</html>
