<?php
session_start();
header('Content-Type: application/json');

// Ensure that cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $itemID = isset($_POST['itemID']) ? $_POST['itemID'] : null;
    $itemName = isset($_POST['itemName']) ? $_POST['itemName'] : null;
    $itemPrice = isset($_POST['itemPrice']) ? $_POST['itemPrice'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    $customization = [
        'temperature' => isset($_POST['temperature']) ? $_POST['temperature'] : null,
        'milk' => isset($_POST['milk']) ? $_POST['milk'] : null,
        'size' => isset($_POST['size']) ? $_POST['size'] : null,
        'syrup' => isset($_POST['syrup']) ? $_POST['syrup'] : null
    ];

    // Check for valid item data
    if ($itemID && $itemName && $itemPrice) {
        // Check if the item already exists in the cart
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

        echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid item data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
