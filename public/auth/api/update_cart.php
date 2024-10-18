<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php'; 

$response = array('status' => 'error', 'message' => '');

try {
    $database = new Database_Auth();
    $db = $database->getConnection();

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $itemID = $data['itemID'] ?? null;
        $quantity = isset($data['quantity']) ? intval($data['quantity']) : null;
        $userID = $data['userID'] ?? null;
        $cartID = $data['cartID'] ?? null;

        if ($itemID && $quantity !== null && $userID && $cartID) {
            try {
                $itemExists = false;
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['itemID'] == $itemID) {
                        // if the item exists then update the quantity
                        $item['quantity'] = $quantity;
                        $itemExists = true;
                        break;
                    }
                }

                // if the item exists in the session cart then proceed to update the database
                if ($itemExists) {
                    // update quantity in the cart db
                    $query = "UPDATE cart SET Quantity = :Quantity WHERE UserID = :UserID AND ItemID = :ItemID AND CartID = :CartID";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':Quantity', $quantity);
                    $stmt->bindParam(':UserID', $userID);
                    $stmt->bindParam(':ItemID', $itemID);
                    $stmt->bindParam(':CartID', $cartID);

                    if ($stmt->execute()) {
                        $response['status'] = 'success';
                        $response['message'] = 'Quantity updated successfully';
                    } else {
                        $response['message'] = 'Database error: Unable to update quantity';
                    }
                } else {
                    $response['message'] = 'Item not found in the session cart';
                }
            } catch (Exception $e) {
                $response['message'] = 'An error occurred: ' . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid item data provided';
        }
    } else {
        $response['message'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>
