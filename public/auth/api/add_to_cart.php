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

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if customization flag is true
        if (isset($data['customization']) && $data['customization'] == true) {
            $itemID = $data['itemID'] ?? null;
            $itemName = $data['itemName'] ?? null;
            $itemPrice = $data['itemPrice'] ?? null;
            $temperature = $data['temperature'] ?? null;
            $milkType = $data['milkType'] ?? null;
            $sweetness = $data['sweetness'] ?? null;
            $addShot = $data['addShot'] ?? null;
            $coffeeBean = $data['coffeeBean'] ?? null;
            $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
            $userID = $data['userID'] ?? null;

            if ($itemID && $itemName && $itemPrice && $userID) {
                try {
                    // Insert customized coffee data into the personal_item table
                    $query = "INSERT INTO personal_item (ItemID, Temperature, Sweetness, AddShot, MilkType, CoffeeBeanType, Favourite, CreatedAt)
                            VALUES (:ItemID, :Temperature, :Sweetness, :AddShot, :MilkType, :CoffeeBeanType, 0, NOW())"; // Favourite set to 0 by default

                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':ItemID', $itemID);
                    $stmt->bindParam(':Temperature', $temperature);
                    $stmt->bindParam(':Sweetness', $sweetness);
                    $stmt->bindParam(':AddShot', $addShot);
                    $stmt->bindParam(':MilkType', $milkType);
                    $stmt->bindParam(':CoffeeBeanType', $coffeeBean);

                    if ($stmt->execute()) {
                        $personalItemID = $db->lastInsertId();
                        // Insert the customized item into the cart table
                        $query = "INSERT INTO cart (UserID, ItemID, Quantity, PersonalItemID, AddedDate, Status)
                                VALUES (:UserID, :ItemID, :Quantity, :PersonalItemID, NOW(), 'Active')";

                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':UserID', $userID);
                        $stmt->bindParam(':ItemID', $itemID);
                        $stmt->bindParam(':Quantity', $quantity);
                        $stmt->bindParam(':PersonalItemID', $personalItemID);

                        if ($stmt->execute()) {
                            $response['status'] = 'success';
                            $response['message'] = 'Customized item added to cart successfully';
                        } else {
                            $response['message'] = 'Database error: Unable to add customized item to cart';
                        }
                    } else {
                        $response['message'] = 'Database error: Unable to store customization';
                    }
                } catch (Exception $e) {
                    $response['message'] = 'An error occurred: ' . $e->getMessage();
                }
            } else {
                $response['message'] = 'Missing required item data';
            }
        } else if (isset($data['customization']) && $data['customization'] == false) {
            // handle for the non-customized item insertion
            $itemID = $data['itemID'] ?? null; 
            $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
            $userID = $data['userID'] ?? null;
            $personalItemID = $data['personalItemID'] ?? null;
            if ($itemID && $userID) {
                try {
                    // check if the item already exists in the cart for the same user
                    $query = "SELECT * FROM cart WHERE UserID = :UserID AND ItemID = :ItemID  AND Status = 'Active'";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':UserID', $userID);
                    $stmt->bindParam(':ItemID', $itemID);
                    $stmt->execute();
        
                    if ($stmt->rowCount() > 0) {
                        // if item exists in the cart then only update the quantity
                        $existingCartItem = $stmt->fetch(PDO::FETCH_ASSOC);
                        $currentPersonalItemID = $existingCartItem['PersonalItemID'];

                        if (is_null($currentPersonalItemID) || $currentPersonalItemID == $personalItemID) {
                            $newQuantity = $existingCartItem['Quantity'] + $quantity; // increase the quantity of the item

                            $updateQuery = "UPDATE cart SET Quantity = :Quantity WHERE CartID = :CartID";
                            $updateStmt = $db->prepare($updateQuery);
                            $updateStmt->bindParam(':Quantity', $newQuantity);
                            $updateStmt->bindParam(':CartID', $existingCartItem['CartID']);
            
                            if ($updateStmt->execute()) {
                                $response['status'] = 'success';
                                $response['message'] = 'Quantity updated successfully';
                            } else {
                                $response['message'] = 'Database error: Unable to update item quantity';
                            }
                        } else {
                                $response['message'] = 'You have already added a customized item to the cart';
                            }
                        } else {
                            // if item does not exist in the cart then only insert a new record
                            $query = "INSERT INTO cart (UserID, ItemID, Quantity, AddedDate, Status)
                                    VALUES (:UserID, :ItemID, :Quantity, NOW(), 'Active')";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':UserID', $userID);
                            $stmt->bindParam(':ItemID', $itemID);
                            $stmt->bindParam(':Quantity', $quantity);
            
                            if ($stmt->execute()) {
                                $response['status'] = 'success';
                                $response['message'] = 'Item added to cart successfully';
                            } else {
                                $response['message'] = 'Database error: Unable to add item to cart';
                            }
                        }
                } catch (Exception $e) {
                    $response['message'] = 'An error occurred: ' . $e->getMessage();
                }
            } else {
                $response['message'] = 'Invalid item data provided';
            }
        }
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>
