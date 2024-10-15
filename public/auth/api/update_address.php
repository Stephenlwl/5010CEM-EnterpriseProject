<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$response = array('success' => false, 'message' => '');

ob_start();
// Check for the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database_Auth();
        $db = $database->getConnection();

        $AdminID = $_POST['admin_id'] ?? null;
        $UserID = $_POST['user_id'] ?? null;
        $addressId = $_POST['address_id'];
        $addressName = $_POST['address_name'];
        $address1 = $_POST['address1'];
        $address2 = $_POST['address2'];
        $postalCode = $_POST['postal_code'];
        $state = $_POST['state'];

        // Add new address from admin panel
        if ($AdminID) {
            if (isset($_POST['address_name']) && isset($_POST['address1']) && isset($_POST['postal_code'])) {
            
                $query = "UPDATE Address SET AddressName = :addressName, Address1 = :Address1, Address2 = :Address2, PostalCode = :PostalCode, State = :State, AdminID = :AdminID WHERE AddressID = :addressId";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':AdminID', $AdminID);
                $stmt->bindParam(':addressId', $addressId); 
                $stmt->bindParam(':addressName', $addressName);
                $stmt->bindParam(':Address1', $address1);
                $stmt->bindParam(':Address2', $address2);
                $stmt->bindParam(':PostalCode', $postalCode);
                $stmt->bindParam(':State', $state);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Address updated successfully!';
                } else {
                    $response['message'] = 'Failed to update address.';
                }
            }
        } else if ($UserID) {
            // Add new address from user panel
            if (isset($_POST['address_name']) && isset($_POST['address1']) && isset($_POST['postal_code'])) {
                
                $query = "UPDATE Address SET AddressName = :addressName, Address1 = :Address1, Address2 = :Address2, PostalCode = :PostalCode, State = :State WHERE AddressID = :addressId AND UserID = :UserID";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':UserID', $UserID);
                $stmt->bindParam(':addressId', $addressId);  
                $stmt->bindParam(':addressName', $addressName);
                $stmt->bindParam(':Address1', $address1);
                $stmt->bindParam(':Address2', $address2);
                $stmt->bindParam(':PostalCode', $postalCode);
                $stmt->bindParam(':State', $state);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Address updated successfully!';
                } else {
                    $response['message'] = 'Failed to update address.';
                }
            }
        } else {
            throw new Exception('AdminID or UserID is required.');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
