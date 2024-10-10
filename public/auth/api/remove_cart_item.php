<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../models/user.php';

$response = array('success' => false, 'message' => '');
$data = json_decode(file_get_contents('php://input'), true);

ob_start();

try {
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Check if UserID is set in session
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'User not logged in.';
            echo json_encode($response);
            exit();
        }

        // Get UserID from session
        $UserID = $_SESSION['user_id'];

        // Check if status field is provided in the request data
        if (isset($data['status'])) {
            
            $Status = $data['status']; 
            
            $query = "DELETE FROM cart WHERE Status = :Status AND UserID = :UserID";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':Status', $Status);
            $stmt->bindParam(':UserID', $UserID);

            // Execute the statement and check the result
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Cart item deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete cart item.';
            }
        } else {
            $response['message'] = 'No status provided.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Flush output buffer
ob_end_clean();

// Return the JSON response
echo json_encode($response);
?>
