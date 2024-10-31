<?php
session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database_Auth();
        $db = $database->getConnection();

        $itemID = $_POST['itemID'];
        $newProductName = $_POST['newProductName'] ?? null;
        $newProductPrice = $_POST['newProductPrice'] ?? null;
       
        // handle the file upload as binary data
        $newImagePath = null;
        if (isset($_FILES['newImagePath']) && $_FILES['newImagePath']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['newImagePath'];
            $newImagePath = file_get_contents($file['tmp_name']); // Read the file as binary data
        }

        // Check if itemID is provided
        if ($itemID) {
            // Retrieve existing product data
            $query = "SELECT ItemName, ItemPrice, ImagePath FROM menu WHERE ItemID = :ItemID";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':ItemID', $itemID);
            $stmt->execute();
            $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            // If the product exists, update only the provided fields
            if ($existingProduct) {
                $updatedProductName = $newProductName ?: $existingProduct['ItemName'];
                $updatedProductPrice = $newProductPrice ?: $existingProduct['ItemPrice'];
                $updatedImagePath = $newImagePath ?: $existingProduct['ImagePath'];

                // Update the product with the new or existing values
                $query = "UPDATE menu SET ItemName = :ItemName, ItemPrice = :ItemPrice, ImagePath = :ImagePath, UpdatedAt = NOW() WHERE ItemID = :ItemID";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':ItemName', $updatedProductName);
                $stmt->bindParam(':ItemPrice', $updatedProductPrice);
                $stmt->bindParam(':ImagePath', $updatedImagePath, PDO::PARAM_LOB);
                $stmt->bindParam(':ItemID', $itemID);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Product updated successfully!';
                } else {
                    $response['message'] = 'Failed to update product.';
                }
            } else {
                $response['message'] = 'Product not found.';
            }
        } else {
            $response['message'] = 'Invalid input. Please provide a valid Item ID.';
        }
    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
