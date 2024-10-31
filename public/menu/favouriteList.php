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

                <div class="menu-item card m-2 p-3" style="width: 18rem;">
                    <div class="card-body">
                        <img src="../auth/api/get_image_from_menu.php?ItemID=<?php echo htmlspecialchars($item['ItemID']); ?>" 
                            onerror="this.onerror=null; this.src='../img/coffee-placeholder.jpg';" 
                            alt="<?php echo htmlspecialchars($item['ItemName']); ?>" 
                            class="img-thumbnail mb-3" 
                            style="max-width: 200px;">
                        <input type="hidden" id="favouriteId" value="<?php echo htmlspecialchars($item['PersonalItemID']); ?>">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['ItemName']); ?></h5>
                        <p class="card-text">Price: RM<?php echo number_format($item['ItemPrice'], 2); ?></p>
                        <p class="card-text">Temperature: <?php echo htmlspecialchars($item['Temperature']); ?></p>
                        <p class="card-text">Sweetness: <?php echo htmlspecialchars($item['Sweetness']); ?></p>
                        <p class="card-text">Extra Shot: <?php echo $item['AddShot'] ? 'Yes' : 'No'; ?></p>
                        <p class="card-text">Milk Type: <?php echo htmlspecialchars($item['MilkType']); ?></p>
                        <p class="card-text">Coffee Bean: <?php echo htmlspecialchars($item['CoffeeBeanType']); ?></p>
                        
                        <?php if ($item["ItemQuantity"] > 0 && (!isset($item["Quantity"]) || $item["Quantity"] < $item["ItemQuantity"])): ?>
                            <button type="button" class="btn btn-primary me-2 rounded" onclick="addFavouriteToCart(<?php echo $item['ItemID']; ?>, <?php echo htmlspecialchars($item['ItemQuantity']); ?>, <?php echo htmlspecialchars($item['Quantity'] ?? 0); ?>)">Add to Cart</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary rounded" disabled>Out of Stock</button>
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
