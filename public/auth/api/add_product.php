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
        // Check for uploaded file
        if (!isset($_FILES['ImagePath']) || $_FILES['ImagePath']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception('Image file is required.');
        }

        // Validate all required fields are present and not empty
        if (empty($_POST['ItemName']) || 
            !isset($_POST['ItemPrice']) || 
            empty($_POST['ItemType'])) {
            throw new Exception('All fields are required.');
        }

        // Sanitize and validate input
        $itemName = trim(strip_tags($_POST['ItemName']));
        $itemPrice = filter_var($_POST['ItemPrice'], FILTER_VALIDATE_FLOAT);
        $itemType = trim(strip_tags($_POST['ItemType']));

        // Additional validation
        if ($itemPrice === false || $itemPrice < 0) {
            throw new Exception('Invalid price value.');
        }

        $file = $_FILES['ImagePath'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Image upload failed.');
        }

        // Read the file contents into binary data
        $imageData = file_get_contents($file['tmp_name']);

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
                throw new Exception('A product with this name already exists.');
            }

            // Prepare INSERT statement
            $query = "INSERT INTO menu (ItemName, ItemPrice, ItemType, ImagePath) 
                     VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($query);
            
            // Execute with parameters
            if (!$stmt->execute([
                $itemName,
                $itemPrice,
                $itemType,
                $imageData // Store image data directly in the database
            ])) {
                throw new Exception('Failed to insert product.');
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
        throw new Exception('Invalid request method.');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Error in adding in database: " . $e->getMessage());
}

// Ensure proper JSON response
echo json_encode($response);
exit;
?>