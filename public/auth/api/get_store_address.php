<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

$response = array('success' => false, 'data' => [], 'message' => '');

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

    //  fetch all the store address
    $query = "SELECT * FROM address WHERE AdminID IS NOT NULL";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($items) {
        $response['success'] = true;
        $response['data'] = $items;
    } else {
        $response['message'] = 'No address found.';
    }

// Return JSON response
echo json_encode($response);
?>
