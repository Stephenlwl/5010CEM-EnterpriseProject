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

        // Set the favourite flag to 1
        $favourite = 1;

        // insert into the personal_item table
        $query = "INSERT INTO personal_item (ItemID, UserID, Temperature, Sweetness, AddShot, MilkType, CoffeeBeanType, Favourite)
                  VALUES (:itemID, :userID, :temperature, :sweetness, :addShot, :milkType, :coffeeBean, :favourite)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':itemID', $itemID);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':temperature', $temperature);
        $stmt->bindParam(':sweetness', $sweetness);
        $stmt->bindParam(':addShot', $addShot);
        $stmt->bindParam(':milkType', $milkType);
        $stmt->bindParam(':coffeeBean', $coffeeBean);
        $stmt->bindParam(':favourite', $favourite);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add item']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}
echo json_encode($response);

?>
