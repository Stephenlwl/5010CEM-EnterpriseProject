<?php
session_start();

require_once '../auth/config/database.php';
require_once '../auth/models/user.php';

$userID = $_SESSION['user_id'] ?? null;

if (!$userID) {
    die("User not logged in");
}

try {
    $database = new Database_Auth();
    $db = $database->getConnection();

    // Query only the personal_item table first
    $query = "SELECT * FROM personal_item 
              WHERE UserID = :userID AND Favourite = 1";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();

    $favoriteItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Now, let's get the item details for each favorite item
    foreach ($favoriteItems as &$item) {
        $itemQuery = "SELECT ItemName, ItemPrice, ItemType FROM menu WHERE ItemID = :itemID";
        $itemStmt = $db->prepare($itemQuery);
        $itemStmt->bindParam(':itemID', $item['ItemID']);
        $itemStmt->execute();
        $itemDetails = $itemStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($itemDetails) {
            $item = array_merge($item, $itemDetails);
        }
    }

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
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f4f4; }
        h1 { color: #333; text-align: center; }
        .favorite-items { display: flex; flex-wrap: wrap; justify-content: center; }
        .favorite-item { 
            background-color: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            margin: 10px; 
            padding: 20px; 
            width: 300px; 
        }
        .favorite-item h2 { margin-top: 0; color: #2c3e50; }
        .favorite-item p { margin: 5px 0; color: #34495e; }
        .no-favorites { text-align: center; color: #7f8c8d; font-style: italic; }
        .remove-btn { 
            background-color: #e74c3c; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s;
        }
        .remove-btn:hover { background-color: #c0392b; }
    </style>
</head>
<body>
    <h1>Your Favorite Items</h1>

    <div class="favorite-items">
    <?php if (empty($favoriteItems)): ?>
        <p class="no-favorites">You haven't added any favorites yet.</p>
    <?php else: ?>
        <?php foreach ($favoriteItems as $item): ?>
            <div class="favorite-item" data-personalitemid="<?php echo htmlspecialchars($item['PersonalItemID']); ?>">
                <h2><?php echo htmlspecialchars($item['ItemName'] ?? 'Unknown Item'); ?></h2>
                <p><strong>Price:</strong> $<?php echo number_format($item['ItemPrice'] ?? 0, 2); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($item['ItemType'] ?? 'N/A'); ?></p>
                <p><strong>Temperature:</strong> <?php echo htmlspecialchars($item['Temperature'] ?? 'N/A'); ?></p>
                <p><strong>Sweetness:</strong> <?php echo htmlspecialchars($item['Sweetness'] ?? 'N/A'); ?></p>
                <p><strong>Extra Shot:</strong> <?php echo $item['AddShot'] ? 'Yes' : 'No'; ?></p>
                <p><strong>Milk Type:</strong> <?php echo htmlspecialchars($item['MilkType'] ?? 'N/A'); ?></p>
                <p><strong>Coffee Bean:</strong> <?php echo htmlspecialchars($item['CoffeeBeanType'] ?? 'N/A'); ?></p>
                <button class="remove-btn" onclick="removeFavorite(<?php echo $item['PersonalItemID']; ?>)">Remove from Favorites</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>

    <script>
    function removeFavorite(personalItemID) {
        if (confirm('Are you sure you want to remove this item from your favorites?')) {
            fetch('remove_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ PersonalItemID: personalItemID })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.querySelector(`[data-personalitemid="${personalItemID}"]`).remove();
                    if (document.querySelectorAll('.favorite-item').length === 0) {
                        document.querySelector('.favorite-items').innerHTML = '<p class="no-favorites">You haven\'t added any favorites yet.</p>';
                    }
                } else {
                    alert('Failed to remove item from favorites. Please try again.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }
    </script>
</body>
</html>