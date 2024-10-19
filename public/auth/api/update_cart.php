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
