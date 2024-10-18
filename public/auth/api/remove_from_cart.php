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
        $userID = $data['userID'] ?? null;
        $cartID = $data['cartID'] ?? null;

        if ($itemID && $userID  && $cartID) {
            try {
                
                    // delete the item from the cart database table
                    $query = "DELETE FROM cart WHERE UserID = :UserID AND ItemID = :ItemID AND CartID = :CartID";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':UserID', $userID);
                    $stmt->bindParam(':ItemID', $itemID);
                    $stmt->bindParam(':CartID', $cartID);

                    if ($stmt->execute()) {
                        $response['status'] = 'success';
                        $response['message'] = 'Item deleted successfully from the cart';
                    } else {
                        $response['message'] = 'Database error: Unable to delete item';
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
