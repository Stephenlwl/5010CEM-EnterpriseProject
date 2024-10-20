<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $personalItemID = $data['PersonalItemID'] ?? null;
    $userID = $_SESSION['user_id'] ?? null;

    if (!$personalItemID || !$userID) {
        $response['message'] = 'Invalid input or user not logged in';
    } else {
        try {
            $database = new Database_Auth();
            $db = $database->getConnection();

            $query = "UPDATE personal_item SET Favourite = 0 WHERE PersonalItemID = :personalItemID AND UserID = :userID";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':personalItemID', $personalItemID);
            $stmt->bindParam(':userID', $userID);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Item removed from favorites';
            } else {
                $response['message'] = 'Failed to remove item from favorites';
            }
        } catch (Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>