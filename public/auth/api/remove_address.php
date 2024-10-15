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

        // Delete address
        if (isset($data['address_id'], $data['user_id'])) {
            $addressId = intval($data['address_id']);
            $UserID = intval($data['user_id']);
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
        } else if (isset($data['address_id'], $data['admin_id'])) {
            $addressId = intval($data['address_id']);
            $AdminID = intval($data['admin_id']);
            $query = "DELETE FROM Address WHERE AddressID = :AddressID AND AdminID = :AdminID";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':AddressID', $addressId);
            $stmt->bindParam(':AdminID', $AdminID);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Address deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete address.';
            }
        } else {
            $response['message'] = 'Address ID and User ID or Admin ID required.';
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

// Return JSON response
echo json_encode($response);
?>
