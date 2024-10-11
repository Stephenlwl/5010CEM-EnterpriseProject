<?php
session_start();

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../models/user.php';

$response = array('success' => false, 'message' => '');

ob_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database_Auth();
        $db = $database->getConnection();

        $data = json_decode(file_get_contents("php://input"), true);
        $orderId = $data['orderId'];
        $newStatus = $data['newStatus'];        

            $query = "UPDATE order SET OrderStatus = :newStatus WHERE OrderID = :orderId";
            $query = "UPDATE `order` SET OrderStatus = :newStatus WHERE OrderID = :orderId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':newStatus', $newStatus);
            $stmt->bindParam(':orderId', $orderId);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Address updated successfully!';
            } else {
                $response['message'] = 'Failed to update address.';
            }
        } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
