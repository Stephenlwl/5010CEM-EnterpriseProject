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
            m.ItemID, 
            m.ItemName, 
            m.ItemPrice, 
            m.ItemType,
            m.ImagePath,
            m.ItemQuantity,
            c.Quantity 
          FROM menu AS m
          LEFT JOIN cart AS c ON m.ItemID = c.ItemID
          GROUP BY m.ItemID, m.ItemName, m.ItemPrice, m.ItemType, m.ImagePath, m.ItemQuantity
          ORDER BY m.ItemName";   // sorting by itemName

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/allMenu.css">
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
                    echo "<button type='button' class='btn btn-primary me-2 rounded' onclick='addCoffeeToCart(" . $item["ItemID"] . ", " . htmlspecialchars($item["ItemQuantity"]) . ", " . htmlspecialchars($item["Quantity"]) .")'>Add to Cart</button>";
                    // Customize button
                    echo "<button type='button' class='btn btn-secondary rounded' data-bs-toggle='modal' data-bs-target='#itemModal' onclick='showDetails(\"" . htmlspecialchars($item["ItemName"]) . "\", " . $item["ItemPrice"] . ", " . $item["ItemID"] . ")'>Customize</button>";
                } else {
                    // Out of Stock message
                    echo "<button type='button' class='btn btn-secondary rounded' disabled>Out of Stock</button>";
                }

                echo "</div>";
                
            }
        } else {
            echo "<p>No coffee items available at the moment.</p>";
        }
        ?>
    </div>

    <!-- The Modal -->
    <div id="itemModal" class="modal fade" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Customize your Coffee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h2 id="modalItemName"></h2>
                    <h5 class="text-danger">RM <span id="modalItemPrice"></span></h5>

                    <!-- Customization form -->
                    <form class="customization-form">
                    <input type="hidden" id="modalItemID">
                    <input type="hidden" id="userId" value="<?php echo htmlspecialchars($UserID); ?>">
                        <div class="row mt-3">
                            <div class="col-sm-4">
                                <label for="Temperature"><i class="bi bi-thermometer-half me-1"></i>Temperature:</label>
                                <select id="Temperature" class="form-select">
                                    <option value="Hot">Hot</option>
                                    <option value="Iced">Iced</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="Sweetness"><i class="bi bi-droplet-half me-1"></i>Sweetness:</label>
                                <select id="Sweetness" class="form-select">
                                    <option value="Regular">Regular</option>
                                    <option value="Less Sweet">Less Sweet</option>
                                </select>
                            </div>
                            <div class="col-sm-3">           
                                <label for="AddShot"><i class="bi bi-cup-straw me-1"></i>Add Shot:</label>
                                <select id="AddShot" class="form-select">
                                    <option value="No Add Shot">No</option>
                                    <option value="Add Shot">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-4">
                                <label for="MilkType"><i class="bi bi-cup-hot me-1"></i>Milk:</label>
                                <select id="MilkType" class="form-select">
                                    <option value="Dairy">Dairy</option>
                                    <option value="Soy">Soy</option>
                                    <option value="Almond">Almond</option>
                                    <option value="Oatly">Oatly</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="CoffeeBean">
                                    <!-- Custom SVG for Coffee Bean Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                        <path d="M4 0C2.36 0 1 1.35 1 3 1 8 8 16 13 16c1.65 0 3-1.36 3-3C16 8 8 0 4 0zm8.8 13.5c-.62 0-1.25-.12-1.85-.36C8.87 11.78 6.04 9.23 3.1 5.3a6.7 6.7 0 0 1-.44-.6c1.3-.33 2.67-.5 4.04-.5 3.56 0 6.56 1.5 8.06 3.64.29.44.5.92.6 1.42-.33 1.5-1.98 3.24-3.56 3.24z"/>
                                    </svg>
                                    Coffee Bean:
                                </label>
                                <select id="CoffeeBean" class="form-select">
                                    <option value="Boss">Boss</option>
                                    <option value="Roasted">Roasted</option>
                                    <option value="Lydia">Lydia</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <label for="Quantity"><i class="bi bi-basket me-1"></i>Quantity:</label>
                                <input id="Quantity" value="1" type="number" min="1" max="10" class="form-control" placeholder="Enter Quantity (min 1, max 10)">
                            </div>
                        </div>
                        <div class="modal-footer mt-3">
                        <button type="button" class="btn btn-secondary" id="favouriteButton" onclick="addToFavourite()">Add to Favourite</button>
                        <button type="button" class="btn btn-primary" onclick="addToCartWithCustomization()">Add to Cart</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</body>

</html>