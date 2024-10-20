<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../auth/config/database.php'; 

$database = new Database_Auth();
$db = $database->getConnection();

$query = "SELECT 
            ItemID, 
            ItemName, 
            ItemPrice, 
            ItemType 
          FROM menu
          WHERE ItemType = 'coffeebean'
          ORDER BY ItemType";  

$stmt = $db->prepare($query);
$stmt->execute();
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$coffeeBeanItems = $menuItems;

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
    <!-- Coffee Bean Section -->
    <div class="section-title">Coffee Beans Selection</div>
    <div class="menu-container">
        <?php
        if ($coffeeBeanItems) {
            foreach ($coffeeBeanItems as $item) {
                echo "<div class='menu-item'>";
                
                // Placeholder image, replace with actual image paths for coffee bean items
                echo "<img src='../img/coffeebean-placeholder.jpg' alt='" . htmlspecialchars($item["ItemName"]) . "'>";
                
                echo "<h2>" . htmlspecialchars($item["ItemName"]) . "</h2>";
                echo "<p class='price'>$" . number_format($item["ItemPrice"], 2) . "</p>";
                echo "<p class='type'>" . htmlspecialchars($item["ItemType"]) . "</p>";
                
                // Add to Cart button
                echo "<button type='button' onclick='addToCart(" . $item["ItemID"] . ")'>Add to Cart</button>";

                echo "</div>";
            }
        } else {
            echo "<p>No coffee bean items available at the moment.</p>";
        }
        ?>
    </div>
</body>
</html>
