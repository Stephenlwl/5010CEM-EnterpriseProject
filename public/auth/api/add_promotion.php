<?php
session_start();

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$database = new Database_Auth();
$db = $database->getConnection();

$response = array('success' => false, 'message' => '');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['promoCode'], $data['discountType'], $data['discountValue'], $data['promoEndDate'], $data['termsAndConditions'])) {
            $promoCode = $data['promoCode'];
            $discountType = $data['discountType']; // fixed or percentage discount
            $discountValue = $data['discountValue'];
            $promoEndDate = $data['promoEndDate']; 
            $termsAndConditions = $data['termsAndConditions'];

            // validate date format
            if (strtotime($promoEndDate) === false) {
                $response['message'] = 'Invalid date format.';
                echo json_encode($response);
                exit();
            }

            // check if promo code already exists
            $query = "SELECT COUNT(*) FROM promotions WHERE PromoCode = :promoCode";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':promoCode', $promoCode);
            $stmt->execute();

            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $response['message'] = 'Promo Code already exists.';
                echo json_encode($response);
                exit();
            }

            $query = "INSERT INTO promotions (PromoCode, DiscountType, DiscountValue, PromotionEndDate, TermsAndConditions) 
                      VALUES (:promoCode, :discountType, :discountValue, :promoEndDate, :termsAndConditions)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':promoCode', $promoCode);
            $stmt->bindParam(':discountType', $discountType);
            $stmt->bindParam(':discountValue', $discountValue);
            $stmt->bindParam(':promoEndDate', $promoEndDate);
            $stmt->bindParam(':termsAndConditions', $termsAndConditions);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Promo Code added successfully!';
            } else {
                $response['message'] = 'Failed to add promo code.';
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

echo json_encode($response);
?>