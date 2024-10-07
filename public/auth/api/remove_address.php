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
// Check for the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Check if UserID is set in session
        if (!isset($_SESSION['UserID'])) {
            $response['message'] = 'User not logged in.';
            echo json_encode($response);
            exit();
        }

        // Get UserID from session
        $UserID = $_SESSION['UserID'];
        // $addressId = $_POST['AddressID'];
        // 
        // Delete address
        if (isset($data['address_id'])) {
            $addressId = intval($data['address_id']);
            $query = "DELETE FROM Address WHERE AddressID = :AddressID AND UserID = :UserID";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':AddressID', $addressId);
            $stmt->bindParam(':UserID', $UserID);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Address deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete address.';
            }
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

// Return JSON response
echo json_encode($response);
?>
