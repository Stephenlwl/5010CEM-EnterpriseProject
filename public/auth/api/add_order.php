<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../models/user.php';

$response = array('success' => false, 'message' => '');

// Start output buffering
ob_start();

try {
    // Ensure that the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ensure the user is logged in
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'User not logged in.';
            echo json_encode($response);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate receipt_id in JSON data
        if (!isset($input['ReceiptID']) || empty($input['ReceiptID'])) {
            $response['message'] = 'Receipt ID is required.';
            echo json_encode($response);
            exit();
        }

        // Establish a database connection
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Retrieve UserID from the session
        $OrderStatus = "Order Placed";
        $ReceiptID = $input['ReceiptID'];
        $CreatedAt = date('Y-m-d H:i:s'); 

        // Prepare the insert query
        $query = "INSERT INTO `order` (OrderStatus, ReceiptID, CreatedAt) VALUES (:OrderStatus, :ReceiptID, :CreatedAt)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':OrderStatus', $OrderStatus);
        $stmt->bindParam(':ReceiptID', $ReceiptID);
        $stmt->bindParam(':CreatedAt', $CreatedAt);

        // Execute the query
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Order added successfully!';
        } else {
            $response['message'] = 'Failed to add order.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Clear any unwanted output
ob_end_clean();

// Return JSON response
echo json_encode($response);
?>
