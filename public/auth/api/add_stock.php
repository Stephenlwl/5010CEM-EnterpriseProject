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

    function autoRestock($itemID, $restockAmount) {
        global $db; // use the global database connection
        // fetch current stock for the item
        $query = "SELECT ItemQuantity FROM menu WHERE ItemID = :itemID";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':itemID', $itemID);
        $stmt->execute();
        $currentStock = $stmt->fetchColumn();

        // perform restock
        $newQuantity = $currentStock + $restockAmount;
        
        // update the stock quantity in db
        $updateQuery = "UPDATE menu SET ItemQuantity = :newQuantity WHERE ItemID = :itemID";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':newQuantity', $newQuantity);
        $updateStmt->bindParam(':itemID', $itemID);
        if ($updateStmt->execute()) {
            return true; // restock was successful
        }
        return false; // restock failed
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $itemID = $data['itemID'] ?? null; 
        $currentQuantity = $data['currentStock'] ?? null;
        $restockQuantity = $data['newQuantity'] ?? null;
        $stockThreshold = $data['stockThreshold'] ?? null;
        $autoRestockQuantity = $data['autoRestockQuantity'] ?? null;

        if ($currentQuantity !== null && $restockQuantity !== null) {
            $newQuantity = $currentQuantity + $restockQuantity;

            if ($itemID && $newQuantity !== null && $stockThreshold) {
                try {
                    // update quantity in the menu db
                    $query = "UPDATE menu SET ItemQuantity = :newQuantity, StockThreshold = :stockThreshold, AutoRestockQuantity = :autoRestockQuantity  WHERE ItemID = :ItemID";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':newQuantity', $newQuantity);
                    $stmt->bindParam(':ItemID', $itemID);
                    $stmt->bindParam(':stockThreshold', $stockThreshold);
                    $stmt->bindParam(':autoRestockQuantity', $autoRestockQuantity);

                    if ($stmt->execute()) {
                        // call autoRestock function
                        if ($newQuantity <= $stockThreshold) { 
                            autoRestock($itemID, $autoRestockQuantity);
                            $response['status'] = 'success';
                            $response['message'] = 'Stock quantity updated and system has auto-restocked successfully due to still being below stock threshold';
                        } else {
                            $response['status'] = 'success';
                            $response['message'] = 'Stock quantity updated successfully, no restock needed';
                        }
                    } else {
                        $response['message'] = 'Database error: Unable to update stock quantity';
                    }
                } catch (Exception $e) {
                    $response['message'] = 'An error occurred: ' . $e->getMessage();
                }
            } else {
                $response['message'] = 'Invalid item data provided';
            }
        } else {
            $response['message'] = 'Current stock or restock quantity is missing';
        }
    } else {
        $response['message'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>
