<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

$response = array('success' => false, 'data' => [], 'message' => '');

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

if (isset($_SESSION['user_id'])) {
    $UserID = $_SESSION['user_id'];

    // Fetch cart items for the logged-in user
    $query = "SELECT * FROM address WHERE UserID = :userID";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $UserID);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($items) {
        $response['success'] = true;
        $response['data'] = $items;
    } else {
        $response['message'] = 'No address found.';
    }
} else {
    $response['message'] = 'User not logged in.';
}

// Return JSON response
echo json_encode($response);
?>
