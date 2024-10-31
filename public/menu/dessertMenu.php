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


$query = "SELECT 
            m.ItemID, 
            m.ItemName, 
            m.ItemPrice, 
            m.ItemType,
            m.ImagePath,
            m.ItemQuantity,
            c.Quantity 
          FROM menu AS m
          LEFT JOIN cart AS c ON m.ItemID = c.ItemID
          WHERE m.ItemType = 'food'
          GROUP BY m.ItemID, m.ItemName, m.ItemPrice, m.ItemType, m.ImagePath, m.ItemQuantity
          ORDER BY m.ItemName";

$stmt = $db->prepare($query);
$stmt->execute();
$foodItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Food Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/allMenu.css">
    <script src="js/menuSettings.js"></script>
</head>

<body>
    <!-- Food Section -->
    <div class="section-title">Taste of Rimberio</div>
    <div class="menu-container">
    <input type="hidden" id="userId" value="<?php echo htmlspecialchars($UserID); ?>">

        <?php
        if ($foodItems) {
            foreach ($foodItems as $item) {
                echo "<div class='menu-item'>";
                echo "<input type='hidden' id='item-stock-quantity-" . $item['ItemID'] . "' value='" . htmlspecialchars($item['ItemQuantity']) . "'>";
                echo "<input type='hidden' id='item-quantity-in-cart-" . $item['ItemID'] . "' value='" . htmlspecialchars($item['Quantity']) . "'>";
                echo "<img src='../auth/api/get_image_from_menu.php?ItemID=" . htmlspecialchars($item['ItemID']) . "'  
                    onerror=\"this.onerror=null; this.src='../img/coffee-placeholder.jpg';\"
                    alt='" . htmlspecialchars($item['ItemName']) . "'
                    class='img-thumbnail' 
                    style='max-width: 200px;'>";
                
                echo "<h2>" . htmlspecialchars($item["ItemName"]) . "</h2>";
                echo "<p class='price'>RM" . number_format($item["ItemPrice"], 2) . "</p>";
                echo "<p class='type'>" . htmlspecialchars($item["ItemType"]) . "</p>";
                
                if ($item["ItemQuantity"] > 0 && ($item["Quantity"] < $item["ItemQuantity"])) {
                    // Add to Cart button
                    echo "<button type='button' class='btn btn-primary me-2 rounded' onclick='addFoodToCart(" . $item["ItemID"] . ", " . htmlspecialchars($item["ItemQuantity"]) . ", " . htmlspecialchars($item["Quantity"]) .")'>Add to Cart</button>";
                } else {
                    // Out of Stock message
                    echo "<button type='button' class='btn btn-secondary rounded' disabled>Out of Stock</button>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No food items available at the moment.</p>";
        }
        ?>
    </div>
</body>

</html>