<?php
session_start();

require_once '../auth/config/database.php';
require_once '../auth/models/user.php';

// Check if the user is logged in
$userID = $_SESSION['user_id'] ?? null;
if (!$userID) {
    die("User not logged in");
}

try {
    $database = new Database_Auth();
    $db = $database->getConnection();

    // retrieve favorite items
    $query = "SELECT pi.*, m.ItemName, m.ItemPrice, m.ItemType 
              FROM personal_item AS pi
              JOIN menu AS m ON pi.ItemID = m.ItemID
              WHERE pi.UserID = :userID AND pi.Favourite = 1
              ORDER BY pi.CreatedAt DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();

    // fetch all favorite items with their details
    $favoriteItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorite Items</title>
    <script src="js/favouriteList.js"></script>
    <link rel="stylesheet" href="../css/favouriteList.css">
</head>
<body>
    <h1>Your Favorite Items</h1>

    <div class="favorite-items">
        <?php if (empty($favoriteItems)): ?>
            <p class="no-favorites">You haven't added any favorites yet.</p>
        <?php else: ?>
            <?php 
            foreach ($favoriteItems as $item): 
            ?>
                <div class="favorite-item" data-personalitemid="<?php echo htmlspecialchars($item['PersonalItemID']); ?>">
                    <h2><?php echo htmlspecialchars($item['ItemName'] ?? 'Unknown Item'); ?></h2>
                    <p><strong>Price:</strong> RM<?php echo number_format($item['ItemPrice'] ?? 0, 2); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($item['ItemType'] ?? 'N/A'); ?></p>
                    <p><strong>Temperature:</strong> <?php echo htmlspecialchars($item['Temperature'] ?? 'N/A'); ?></p>
                    <p><strong>Sweetness:</strong> <?php echo htmlspecialchars($item['Sweetness'] ?? 'N/A'); ?></p>
                    <p><strong>Extra Shot:</strong> <?php echo htmlspecialchars($item['AddShot'] ?? 'N/A'); ?></p>
                    <p><strong>Milk Type:</strong> <?php echo htmlspecialchars($item['MilkType'] ?? 'N/A'); ?></p>
                    <p><strong>Coffee Bean:</strong> <?php echo htmlspecialchars($item['CoffeeBeanType'] ?? 'N/A'); ?></p>
                    <button class="remove-btn" onclick="removeFavorite(<?php echo $item['PersonalItemID']; ?>)">Remove from Favorites</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
