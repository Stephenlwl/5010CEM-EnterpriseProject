<?php
session_start();
header('Content-Type: application/json');

require_once '../auth/config/database.php';  // Adjust path as needed

$database = new Database_Auth();
$db = $database->getConnection();

// Ensure cart exists in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    $itemID = isset($data['itemID']) ? $data['itemID'] : null;
    $itemName = isset($data['itemName']) ? $data['itemName'] : null;
    $itemPrice = isset($data['itemPrice']) ? $data['itemPrice'] : null;
    $temperature = isset($data['temperature']) ? $data['temperature'] : null;
    $milkType = isset($data['milkType']) ? $data['milkType'] : null;
    $sweetness = isset($data['sweetness']) ? $data['sweetness'] : null;
    $addShot = isset($data['addShot']) ? $data['addShot'] : null;
    $coffeeBean = isset($data['coffeeBean']) ? $data['coffeeBean'] : null;
    $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
    $userID = isset($data['userID']) ? intval($data['userID']) : null; // Assuming UserID is provided
    $personalItemID = isset($data['personalItemID']) ? intval($data['personalItemID']) : null; // Custom personal item if needed

    // Check for valid item data
    if ($itemID && $itemName && $itemPrice && $userID) {
        // Create a customization array for comparison
        $customization = [
            'Temperature' => $temperature,
            'MilkType' => $milkType,
            'Sweetness' => $sweetness,
            'AddShot' => $addShot,
            'CoffeeBean' => $coffeeBean
        ];

        // Check if the item already exists in the cart (with same customizations)
        $itemExists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['itemID'] == $itemID && $item['customization'] == $customization) {
                // If the item exists, update the quantity
                $item['quantity'] += $quantity;
                $itemExists = true;
                break;
            }
        }

        // If the item doesn't exist, add it to the cart
        if (!$itemExists) {
            $_SESSION['cart'][] = [
                'itemID' => $itemID,
                'itemName' => $itemName,
                'itemPrice' => $itemPrice,
                'quantity' => $quantity,
                'customization' => $customization
            ];
        }

        // Prepare a SQL query to insert this data into the `cart` table
        $query = "INSERT INTO cart (UserID, ItemID, Quantity, PersonalItemID, AddedDate, Status)
                  VALUES (:UserID, :ItemID, :Quantity, :PersonalItemID, NOW(), 'Active')";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':UserID', $userID);
        $stmt->bindParam(':ItemID', $itemID);
        $stmt->bindParam(':Quantity', $quantity);
        $stmt->bindParam(':PersonalItemID', $personalItemID);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid item data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

