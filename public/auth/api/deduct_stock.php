<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php'; 

$response = array('status' => 'error', 'message' => '');

try {
    $database = new Database_Auth();
    $db = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $itemID = $item['ItemID'];
                $currentQuantity = $item['currentStock'];
                $orderedQuantity = $item['orderedQuantity'];

                // calculate the new stock product quantity
                $newQuantity = $currentQuantity - $orderedQuantity;

                if ($itemID) {
                    try {
                        // update quantity in the menu table
                        $query = "UPDATE menu SET ItemQuantity = :newQuantity WHERE ItemID = :ItemID";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':newQuantity', $newQuantity);
                        $stmt->bindParam(':ItemID', $itemID);

                        // Execute the statement
                        if ($stmt->execute()) {
                            $response['status'] = 'success';
                        } else {
                            $response['message'] = 'Database error: Unable to update stock quantity for ItemID ' . $itemID;
                            break; // exit loop on error
                        }
                    } catch (Exception $e) {
                        $response['message'] = 'An error occurred: ' . $e->getMessage();
                        break; // exit loop on error
                    }
                } else {
                    $response['message'] = 'Invalid item data provided for ItemID ' . $itemID;
                    break; // exit loop on error
                }
            }
        } else {
            $response['message'] = 'No items provided or invalid format';
        }
    } else {
        $response['message'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>
