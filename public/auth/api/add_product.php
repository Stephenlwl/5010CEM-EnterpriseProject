<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$response = array('success' => false, 'message' => '');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get and decode JSON data
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);

        // Log received data for debugging
        error_log("Received data: " . print_r($data, true));

        // Validate all required fields are present and not empty
        if (empty($data['ItemName']) || 
            !isset($data['ItemPrice']) || 
            // !isset($data['ItemQuantity']) || 
            empty($data['ItemType']) || 
            empty($data['ImagePath'])) {
            throw new Exception('All fields are required');
        }

        // Sanitize and validate input
        $itemName = trim(strip_tags($data['ItemName']));
        $itemPrice = filter_var($data['ItemPrice'], FILTER_VALIDATE_FLOAT);
        // $itemQuantity = filter_var($data['ItemQuantity'], FILTER_VALIDATE_INT);
        $itemType = trim(strip_tags($data['ItemType']));
        $imagePath = trim(strip_tags($data['ImagePath']));

        // Additional validation
        if ($itemPrice === false || $itemPrice < 0) {
            throw new Exception('Invalid price value');
        }
        // if ($itemQuantity === false || $itemQuantity < 0) {
        //     throw new Exception('Invalid quantity value');
        // }

        // Initialize database connection
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Begin transaction
        $db->beginTransaction();

        try {
            // Check for duplicate product
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM menu WHERE ItemName = ?");
            $checkStmt->execute([$itemName]);
            
            if ($checkStmt->fetchColumn() > 0) {
                throw new Exception('A product with this name already exists');
            }

            // Prepare INSERT statement
            $query = "INSERT INTO menu (ItemName, ItemPrice, ItemType, ImagePath) 
                     VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($query);
            
            // Execute with parameters
            if (!$stmt->execute([
                $itemName,
                $itemPrice,
                // $itemQuantity,
                $itemType,
                $imagePath
            ])) {
                throw new Exception('Failed to insert product');
            }

            // Commit transaction
            $db->commit();
            
            $response['success'] = true;
            $response['message'] = 'Product added successfully!';
            $response['product_id'] = $db->lastInsertId();
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    } else {
        throw new Exception('Invalid request method');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Error in add_product.php: " . $e->getMessage());
}

// Ensure proper JSON response
echo json_encode($response);
exit;
?>