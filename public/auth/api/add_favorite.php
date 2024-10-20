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

        // Check if the item is already marked as favorite
        $checkQuery = "SELECT * FROM personal_item WHERE UserID = :userID AND ItemID = :itemID";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':userID', $userID);
        $checkStmt->bindParam(':itemID', $itemID);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            // Item exists, toggle the favorite status
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if ($row['Favourite'] == 1) {
                // If currently a favorite, remove it (set Favourite to 0 or delete)
                $deleteQuery = "DELETE FROM personal_item WHERE UserID = :userID AND ItemID = :itemID";
                $deleteStmt = $db->prepare($deleteQuery);
                $deleteStmt->bindParam(':userID', $userID);
                $deleteStmt->bindParam(':itemID', $itemID);

                if ($deleteStmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Item removed from favorites';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to remove favorite item';
                }
            } else {
                // If not marked as favorite, update it to favorite
                $updateQuery = "UPDATE personal_item 
                                SET Temperature = :temperature, Sweetness = :sweetness, AddShot = :addShot, 
                                    MilkType = :milkType, CoffeeBeanType = :coffeeBean, Favourite = 1
                                WHERE UserID = :userID AND ItemID = :itemID";

                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':temperature', $temperature);
                $updateStmt->bindParam(':sweetness', $sweetness);
                $updateStmt->bindParam(':addShot', $addShot);
                $updateStmt->bindParam(':milkType', $milkType);
                $updateStmt->bindParam(':coffeeBean', $coffeeBean);
                $updateStmt->bindParam(':userID', $userID);
                $updateStmt->bindParam(':itemID', $itemID);

                if ($updateStmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Item added to favorite';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to update favorite item';
                }
            }
        } else {
            // Item does not exist, insert as a new favorite
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
