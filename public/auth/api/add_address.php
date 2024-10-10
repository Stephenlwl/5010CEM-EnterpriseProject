<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../models/user.php';

$response = array('success' => false, 'message' => '');

ob_start();
// Check for the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
        $addressName = $_POST['address_name'];
        $address1 = $_POST['address1'];
        $address2 = $_POST['address2'];
        $postalCode = $_POST['postal_code'];
        $state = $_POST['state'];

        // Add new address
        if (isset($_POST['address_name']) && isset($_POST['address1']) && isset($_POST['postal_code'])) {
            
            $query = "INSERT INTO Address (UserID, AddressName, Address1, Address2, PostalCode, State) VALUES (:UserID, :AddressName, :Address1, :Address2, :PostalCode, :State)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':UserID', $UserID);
            $stmt->bindParam(':AddressName', $addressName);
            $stmt->bindParam(':Address1', $address1);
            $stmt->bindParam(':Address2', $address2);
            $stmt->bindParam(':PostalCode', $postalCode);
            $stmt->bindParam(':State', $state);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Address added successfully!';
            } else {
                $response['message'] = 'Failed to add address.';
            }
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

// Return JSON response
echo json_encode($response);
?>
