<?php
session_start();

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

// Establish a database connection
$database = new Database_Auth();
$db = $database->getConnection();

$response = array('success' => false, 'message' => '');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Get the input data and decode it
        $data = json_decode(file_get_contents("php://input"), true);
        error_log(print_r($data, true)); // Debugging line to log the received data

        // Check if promoCode is set in the data
        if (isset($data['promoCode'])) {
            $promoCode = $data['promoCode'];

            // Prepare the SQL query to delete the promotion by promo code
            $query = "DELETE FROM promotions WHERE PromoCode = :promoCode";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':promoCode', $promoCode);

            // Execute the query and provide feedback
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Promotion removed successfully!';
            } else {
                $response['message'] = 'Failed to remove promotion.';
            }
        } else {
            $response['message'] = 'Invalid data format.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Send the JSON response
echo json_encode($response);
?>
