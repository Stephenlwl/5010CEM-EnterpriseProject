<?php
session_start();

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php'; 

$response = array('status' => 'error', 'message' => '');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $database = new Database_Auth();
        $db = $database->getConnection();

        $data = json_decode(file_get_contents('php://input'), true);

        // validate the received data
        if (!isset($data['UserID']) || !isset($data['ItemID'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            exit;
        }

        $userID = $data['UserID']; 
        $itemID = $data['ItemID'];
        $temperature = isset($data['Temperature']) ? $data['Temperature'] : null;  
        $sweetness = isset($data['Sweetness']) ? $data['Sweetness'] : null; 
        $addShot = isset($data['AddShot']) ? $data['AddShot'] : null;  
        $milkType = isset($data['MilkType']) ? $data['MilkType'] : null;  
        $coffeeBean = isset($data['CoffeeBean']) ? $data['CoffeeBean'] : null;  

        // check for the item is already marked as favorite
        $checkQuery = "SELECT * FROM personal_item 
               WHERE ItemID = :itemID 
               AND UserID = :userID 
               AND Temperature = :temperature 
               AND Sweetness = :sweetness 
               AND AddShot = :addShot 
               AND MilkType = :milkType 
               AND CoffeeBeanType = :coffeeBean";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':itemID', $itemID);
        $checkStmt->bindParam(':userID', $userID);
        $checkStmt->bindParam(':temperature', $temperature);
        $checkStmt->bindParam(':sweetness', $sweetness);
        $checkStmt->bindParam(':addShot', $addShot);
        $checkStmt->bindParam(':milkType', $milkType);
        $checkStmt->bindParam(':coffeeBean', $coffeeBean);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            // Item exists, check if attributes match
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if all attributes match the existing favorite item
            $isSameFavorite = (
                $row['Temperature'] == $temperature &&
                $row['Sweetness'] == $sweetness &&
                $row['AddShot'] == $addShot &&
                $row['MilkType'] == $milkType &&
                $row['CoffeeBeanType'] == $coffeeBean &&
                $row['ItemID'] == $itemID &&
                $row['UserID'] == $userID
            );
            
            if ($isSameFavorite) {
                // toggle the Favourite attribute between 0 and 1
                $personalItemID = $row['PersonalItemID'];
                $currentFavoriteStatus = $row['Favourite'];
                
                if ($currentFavoriteStatus == 1) {
                    $response['status'] = 'success';
                    $response['message'] = 'This Item is already added in your favorite list';
                } else {
                    $newFavoriteStatus = 1; // set as favorite
                    $toggleQuery = "UPDATE personal_item SET Favourite = :newFavoriteStatus WHERE PersonalItemID = :personalItemID";
                
                    $toggleStmt = $db->prepare($toggleQuery);
                    $toggleStmt->bindParam(':newFavoriteStatus', $newFavoriteStatus);
                    $toggleStmt->bindParam(':personalItemID', $personalItemID);
                
                    if ($toggleStmt->execute()) {
                        $response['status'] = 'success';
                        $response['message'] = 'Item added to favorites';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Failed to add item to favorites';
                    }
                }
            } else {
                // attributes not match, add a new favorite entry
                $query = "INSERT INTO personal_item (ItemID, UserID, Temperature, Sweetness, AddShot, MilkType, CoffeeBeanType, Favourite)
                          VALUES (:itemID, :userID, :temperature, :sweetness, :addShot, :milkType, :coffeeBean, 1)";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':itemID', $itemID);
                $stmt->bindParam(':userID', $userID);
                $stmt->bindParam(':temperature', $temperature);
                $stmt->bindParam(':sweetness', $sweetness);
                $stmt->bindParam(':addShot', $addShot);
                $stmt->bindParam(':milkType', $milkType);
                $stmt->bindParam(':coffeeBean', $coffeeBean);
        
                if ($stmt->execute()) {
                    $response['status'] = 'success'; 
                    $response['message'] = 'New item added to favorite';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to add favorite item';
                }
            }
        } else {
            // item not exist, insert as a new favorite
            $query = "INSERT INTO personal_item (ItemID, UserID, Temperature, Sweetness, AddShot, MilkType, CoffeeBeanType, Favourite)
                      VALUES (:itemID, :userID, :temperature, :sweetness, :addShot, :milkType, :coffeeBean, 1)";
        
            $stmt = $db->prepare($query);
            $stmt->bindParam(':itemID', $itemID);
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':temperature', $temperature);
            $stmt->bindParam(':sweetness', $sweetness);
            $stmt->bindParam(':addShot', $addShot);
            $stmt->bindParam(':milkType', $milkType);
            $stmt->bindParam(':coffeeBean', $coffeeBean);
        
            if ($stmt->execute()) {
                $response['status'] = 'success'; 
                $response['message'] = 'Item added to favorite';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to add favorite item';
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}
echo json_encode($response);

?>
