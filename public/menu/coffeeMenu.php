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
    <style>
        /* Basic modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        .menu-item img {
            cursor: pointer;
        }

        .customization-form label {
            display: block;
            margin-top: 10px;
        }

        .customization-form select {
            width: 100%;
            padding: 5px;
            margin-top: 5px;
        }

        .customization-form button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        .customization-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Coffee Shop Menu</h1>

    <!-- Coffee Section -->
    <div class="section-title">Coffee</div>
    <div class="menu-container">
        <?php
        if ($coffeeItems) {
            foreach ($coffeeItems as $item) {
                echo "<div class='menu-item'>";
                
                // Placeholder image, replace with actual image paths for coffee items
                echo "<img src='images/coffee-placeholder.jpg' alt='" . htmlspecialchars($item["ItemName"]) . "' onclick='showDetails(\"" . htmlspecialchars($item["ItemName"]) . "\", " . $item["ItemPrice"] . ", " . $item["ItemID"] . ")'>";
                
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

    <script>
        let selectedItemId;

        // Function to show item details in a modal
        function showDetails(name, price, itemId) {
            document.getElementById('modalItemName').textContent = name;
            document.getElementById('modalItemPrice').textContent = "Price: $" + price.toFixed(2);

            selectedItemId = itemId; // Store the item ID for use in customization

            // Display the modal
            document.getElementById('itemModal').style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('itemModal').style.display = 'none';
        }

        // Function to add an item with customizations to the cart
        function addToCartWithCustomization() {
            const temperature = document.getElementById('temperature').value;
            const milk = document.getElementById('milk').value;
            const size = document.getElementById('size').value;
            const syrup = document.getElementById('syrup').value;

            alert("Item " + selectedItemId + " added to cart with customization:\n" +
                "Temperature: " + temperature + "\n" +
                "Milk: " + milk + "\n" +
                "Size: " + size + "\n" +
                "Syrup: " + syrup);

            // You can integrate actual cart functionality here
            closeModal();
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('itemModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>

<?php
// Close the connection
$db = null;
?>
