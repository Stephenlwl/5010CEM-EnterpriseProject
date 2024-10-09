<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

$response = array('success' => false, 'message' => '');

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'User not logged in.';
            echo json_encode($response);
            exit();
        }

        // Read the raw POST data
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input fields
        if (!isset($input['AddressID'], $input['TotalPrice'], $input['PaymentType'], $input['ReceiveMethod'])) {
            $response['message'] = 'Missing required fields.';
            echo json_encode($response);
            exit();
        }

        // Get UserID from session
        $UserID = $_SESSION['user_id'];
        $AddressID = $input['AddressID'];
        $TotalPrice = $input['TotalPrice'];
        $PaymentType = $input['PaymentType']; 
        $ReceiveMethod = $input['ReceiveMethod']; 
        $ReferenceNo = $input['ReferenceNo'] ?? null; // Set as null if not provided

        // Fetch cart items for the user
        $cartResponse = fetchCartItems($UserID, $db);
        if (!$cartResponse['success']) {
            echo json_encode($cartResponse);
            exit();
        }

        $items = $cartResponse['data'];
        if (empty($items)) {
            $response['message'] = 'Cart is empty.';
            echo json_encode($response);
            exit();
        }

        // Current date and time for ReceiptCreatedAt
        $ReceiptCreatedAt = date('Y-m-d H:i:s');

        // Insert into the 'receipt' table
        $query_receipt = "INSERT INTO receipt (UserID, AddressID, TotalPrice, ReceiptCreatedAt, PaymentType, ReceiveMethod, ReferenceNo)
                          VALUES (:UserID, :AddressID, :TotalPrice, :ReceiptCreatedAt, :PaymentType, :ReceiveMethod, :ReferenceNo)";
        $stmt = $db->prepare($query_receipt);
        $stmt->bindParam(':UserID', $UserID);
        $stmt->bindParam(':AddressID', $AddressID);
        $stmt->bindParam(':TotalPrice', $TotalPrice);
        $stmt->bindParam(':ReceiptCreatedAt', $ReceiptCreatedAt);
        $stmt->bindParam(':PaymentType', $PaymentType);
        $stmt->bindParam(':ReceiveMethod', $ReceiveMethod);
        $stmt->bindParam(':ReferenceNo', $ReferenceNo);

        if ($stmt->execute()) {
            // Get the last inserted ReceiptID
            $ReceiptID = $db->lastInsertId();

            // Insert into the receipt_details table
            foreach ($items as $item) {
                $ItemID = $item['ItemID'];
                $PersonalItemID = null; // Set as default for not customized items
                $ItemQuantity = $item['Quantity'];
                $ItemPrice = $item['ItemPrice'];
                $TotalItemPrice = $ItemPrice * $ItemQuantity;

                $query_details = "INSERT INTO receipt_details (ReceiptID, ItemID, PersonalItemID, ItemQuantity, ItemPrice, TotalPrice)
                                  VALUES (:ReceiptID, :ItemID, :PersonalItemID, :ItemQuantity, :ItemPrice, :TotalPrice)";
                $stmt_details = $db->prepare($query_details);
                $stmt_details->bindParam(':ReceiptID', $ReceiptID);
                $stmt_details->bindParam(':ItemID', $ItemID);
                $stmt_details->bindParam(':PersonalItemID', $PersonalItemID);
                $stmt_details->bindParam(':ItemQuantity', $ItemQuantity);
                $stmt_details->bindParam(':ItemPrice', $ItemPrice);
                $stmt_details->bindParam(':TotalPrice', $TotalItemPrice);
                $stmt_details->execute();
            }

            $response['success'] = true;
            $response['message'] = 'Receipt and details created successfully!';
            $response['ReceiptID'] = $ReceiptID;
        } else {
            $response['message'] = 'Failed to create receipt.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Exception: ' . $e->getMessage();
    }
}

// Function to fetch cart items
function fetchCartItems($UserID, $db) {
    $query = "SELECT c.CartID, c.ItemID, c.Quantity, m.ItemName, m.ItemPrice
              FROM cart AS c
              JOIN menu AS m ON c.ItemID = m.ItemID
              WHERE c.UserID = :userID AND c.Status = 'Active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userID', $UserID);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array('success' => true, 'data' => $items);
}

// Return JSON response
echo json_encode($response);
?>