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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/allMenu.css">
    <link rel="stylesheet" href="../css/defaultCatelog.css">
    <script src="js/menuSettings.js"></script>
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
                echo "<p class='price'>RM" . number_format($item["ItemPrice"], 2) . "</p>";
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
            <p>RM <div id="modalItemPrice"></div></p>
            <input type="hidden" id="modalItemID">
            <input type="hidden" id="userId" value="<?php echo htmlspecialchars($UserID); ?>">

            <!-- Customization form -->
            <form class="customization-form">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="Temperature">Temperature:</label>
                        <select id="Temperature">
                            <option value="Hot">Hot</option>
                            <option value="Iced">Iced</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="Sweetness">Sweetness:</label>
                        <select id="Sweetness">
                            <option value="Regular">Regular</option>
                            <option value="Less Sweet">Less Sweet</option>
                        </select>
                    </div>
                    <div class="col-sm-3">           
                        <label for="AddShot">Add Shot:</label>
                        <select id="AddShot">
                            <option value="True">Yes</option>
                            <option value="False">No</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                    <label for="MilkType">Milk:</label>
                    <select id="MilkType">
                            <option value="Diary">Diary</option>
                            <option value="Soy">Soy</option>
                            <option value="Almond">Almond</option>
                            <option value="Oatly">Oatly</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="CoffeeBean">Coffee Bean:</label>
                        <select id="CoffeeBean">
                            <option value="Boss">Boss</option>
                            <option value="Roasted">Roasted</option>
                            <option value="Lydia">Lydia</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="Quantity">Quantity</label>
                        <input id="Quantity" value="1" type="number" min="1" max="10" Placeholder="Enter Quantity (min 1, max 10)"></div>
                    </div>
                    <button type="button" onclick="addToCartWithCustomization()">Add to Cart</button>
                    <button type="button" onclick="addToFavourite()">Favourite</button>
                </div>
            
            </form>
        </div>
    </div>

</body>

</html>