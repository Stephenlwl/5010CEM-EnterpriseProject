<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$response = array('success' => false, 'message' => '');

$database = new Database_Auth();
$db = $database->getConnection();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['UserID'];  
    $promoCode = $data['PromoCode']; 

    if (isset($userId) && isset($promoCode)) {
        // check if the promo code has already been used by the user
        $query = "SELECT * FROM used_promotion WHERE UserID = :userID AND UsedPromoCode = :promoCode";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':userID', $userId);
        $stmt->bindParam(':promoCode', $promoCode);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // promo code already used by the user
            $response['success'] = false;
            $response['message'] = 'Promo code already used';
        } else {
            // insert the used promo code into the used promotion table
            $usedAt = date('Y-m-d H:i:s');
            $query = "INSERT INTO used_promotion (UserID, UsedPromoCode, UsedAt) VALUES (:userID, :promoCode, :usedAt)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':userID', $userId);
            $stmt->bindParam(':promoCode', $promoCode);
            $stmt->bindParam(':usedAt', $usedAt);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Promo code applied successfully';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error applying promo code';
            }
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Missing data';
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>
