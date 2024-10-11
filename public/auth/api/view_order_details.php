<?php
session_start();

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$database = new Database_Auth();
$db = $database->getConnection();

// Check for the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['orderId'])) {
            throw new Exception('Order ID is required.');
        }
        
        $orderId = $data['orderId'];

        // retrieve the ReceiptID referring to OrderID
        $query = "SELECT ReceiptID FROM `order` WHERE OrderID = :orderId";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            $receiptId = $result['ReceiptID'];

            // fetch the order details from the receipt_details table using ReceiptID
            $queryDetails = "SELECT 
                    rd.ItemID,
                    rd.ItemQuantity,
                    rd.ItemPrice,
                    m.ItemName,  
                    COALESCE(pi.Temperature, 'N/A') AS Temperature,  -- returns the first non-null value among its arguments
                    COALESCE(pi.Sweetness, 'N/A') AS Sweetness,
                    COALESCE(pi.AddShot, 'N/A') AS AddShot,      
                    COALESCE(pi.MilkType, 'N/A') AS MilkType,     
                    COALESCE(pi.CoffeeBeanType, 'N/A') AS CoffeeBeanType,   
                    rd.PersonalItemID
                FROM receipt_details rd 
                LEFT JOIN personal_item pi ON rd.ItemID = pi.ItemID 
                JOIN menu m ON rd.ItemID = m.ItemID 
                WHERE rd.ReceiptID = :receiptId";

            $stmtDetails = $db->prepare($queryDetails);
            $stmtDetails->bindParam(':receiptId', $receiptId, PDO::PARAM_INT);
            $stmtDetails->execute();
            
            $orderDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);
                        
            // add the receiptID and orderDetails in the response
            echo json_encode(['ReceiptID' => $receiptId, 'orderDetails' => $orderDetails]);
        } else {
            echo json_encode(['ReceiptID' => null, 'orderDetails' => []]); // output empty array if no result
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]); // return error message
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']); // output error for invalid request method
}

