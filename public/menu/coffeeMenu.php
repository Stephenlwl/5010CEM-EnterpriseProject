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
    <link rel="stylesheet" href="../css/defaultCatelog.css">
    <script src=js/menuSettings.js></script>
</head>
<body>
    <!-- Coffee Section -->
    <div class="section-title">Rimberio’s Drink Selections</div>
    <div class="menu-container">
        <?php
        if ($coffeeItems) {
            foreach ($coffeeItems as $item) {
                echo "<div class='menu-item'>";
                
                // Placeholder image, replace with actual image paths for coffee items
                echo "<img src='../img/coffee-placeholder.jpg' alt='" . htmlspecialchars($item["ItemName"]) . "' onclick='showDetails(\"" . htmlspecialchars($item["ItemName"]) . "\", " . $item["ItemPrice"] . ", " . $item["ItemID"] . ")'>";
                
                echo "<h2>" . htmlspecialchars($item["ItemName"]) . "</h2>";
                echo "<p class='price'>$" . number_format($item["ItemPrice"], 2) . "</p>";
                echo "<p class='type'>" . htmlspecialchars($item["ItemType"]) . "</p>";
                
                // Add to Cart button
                echo "<button type='button' onclick='addToCart(" . $item["ItemID"] . ")'>Add to Cart</button>";

                echo "</div>";
            }
        } else {
            echo "<p>No coffee items available at the moment.</p>";
        }
        ?>
    </div>

    <!-- The Modal -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalItemName"></h2>
            <p id="modalItemPrice"></p>

            <!-- Customization form -->
            <form class="customization-form">
                <label for="temperature">Temperature:</label>
                <select id="temperature">
                    <option value="hot">Hot</option>
                    <option value="iced">Iced</option>
                </select>

                <label for="milk">Milk Type:</label>
                <select id="milk">
                    <option value="whole">Whole Milk</option>
                    <option value="almond">Almond Milk</option>
                    <option value="oat">Oat Milk</option>
                    <option value="soy">Soy Milk</option>
                </select>

                <label for="size">Size:</label>
                <select id="size">
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>

                <label for="syrup">Syrup:</label>
                <select id="syrup">
                    <option value="none">None</option>
                    <option value="vanilla">Vanilla</option>
                    <option value="caramel">Caramel</option>
                    <option value="hazelnut">Hazelnut</option>
                </select>

                <button type="button" onclick="addToCartWithCustomization()">Add to Cart</button>
            </form>
        </div>
    </div>

</body>
</html>
