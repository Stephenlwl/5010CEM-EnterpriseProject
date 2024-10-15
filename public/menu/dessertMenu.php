<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../auth/config/database.php';  // Adjust the path based on your folder structure

$database = new Database_Auth();
$db = $database->getConnection();

// Fetch all menu items from the database and order them by type
$query = "SELECT 
            ItemID, 
            ItemName, 
            ItemPrice, 
            ItemType 
          FROM menu
          ORDER BY ItemType";  // Sorting by ItemType

$stmt = $db->prepare($query);
$stmt->execute();
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group items by their type (Coffee or Food)
$coffeeItems = [];
$foodItems = [];

foreach ($menuItems as $item) {
    if (strtolower($item['ItemType']) == 'coffee') {
        $coffeeItems[] = $item;
    } else {
        $foodItems[] = $item;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Menu</title>
    <link rel="stylesheet" href="../css/allMenu.css">
</head>
<body>
    <!-- Food Section -->
    <div class="section-title">Taste of Rimberio</div>
    <div class="menu-container">
        <?php
        if ($foodItems) {
            foreach ($foodItems as $item) {
                echo "<div class='menu-item'>";
                
                // Placeholder image, replace with actual image paths for food items
                echo "<img src='../img/food-placeholder.jpg' alt='" . htmlspecialchars($item["ItemName"]) . "'>";
                
                echo "<h2>" . htmlspecialchars($item["ItemName"]) . "</h2>";
                echo "<p class='price'>$" . number_format($item["ItemPrice"], 2) . "</p>";
                echo "<p class='type'>" . htmlspecialchars($item["ItemType"]) . "</p>";
                
                // Add to Cart button
                echo "<button type='button' onclick='addToCart(" . $item["ItemID"] . ")'>Add to Cart</button>";

                echo "</div>";
            }
        } else {
            echo "<p>No food items available at the moment.</p>";
        }
        ?>
    </div>
</body>
</html>