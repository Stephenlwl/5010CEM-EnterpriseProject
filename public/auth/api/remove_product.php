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

        // Validate CSRF token
        if (empty($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        // Validate product ID
        if (empty($data['productID'])) {
            throw new Exception('Product ID is required');
        }

        // Sanitize and validate product ID
        $productID = filter_var($data['productID'], FILTER_VALIDATE_INT);
        if ($productID === false || $productID <= 0) {
            throw new Exception('Invalid product ID');
        }

        // Initialize database connection
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Begin transaction
        $db->beginTransaction();

        try {
            // Prepare DELETE statement
            $query = "DELETE FROM menu WHERE ItemID = ?";
            $stmt = $db->prepare($query);

            // Execute with parameters
            if (!$stmt->execute([$productID])) {
                throw new Exception('Failed to remove product');
            }

            // Check if any row was affected
            if ($stmt->rowCount() === 0) {
                throw new Exception('Product not found');
            }

            // Commit transaction
            $db->commit();

            $response['success'] = true;
            $response['message'] = 'Product removed successfully!';
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return the response as JSON
echo json_encode($response);
