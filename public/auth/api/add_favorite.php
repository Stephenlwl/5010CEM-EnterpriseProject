<?php
session_start();

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get database connection
    $database = new Database_Auth();
    $db = $database->getConnection();

    // Get the data from the POST request
    $userID = $_POST['userID'];
    $itemID = $_POST['ItemID'];
    $temperature = $_POST['Temperature'];
    $sweetness = $_POST['Sweetness'];
    $addShot = $_POST['AddShot'];
    $milkType = $_POST['MilkType'];
    $coffeeBean = $_POST['CoffeeBean'];

    // Set the favourite flag to 1
    $favourite = 1;

    // Get the current date and time
    $createdAt = date('Y-m-d H:i:s');

    // Insert into the personal_item table
    $query = "INSERT INTO personal_item (ItemID, UserID, Temperature, Sweetness, AddShot, MilkType, CoffeeBeanType, Favourite, CreatedAt)
              VALUES (:itemID, :userID, :temperature, :sweetness, :addShot, :milkType, :coffeeBean, :favourite, :createdAt)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':itemID', $itemID, PDO::PARAM_INT);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->bindParam(':temperature', $temperature, PDO::PARAM_STR);
    $stmt->bindParam(':sweetness', $sweetness, PDO::PARAM_STR);
    $stmt->bindParam(':addShot', $addShot, PDO::PARAM_STR);
    $stmt->bindParam(':milkType', $milkType, PDO::PARAM_STR);
    $stmt->bindParam(':coffeeBean', $coffeeBean, PDO::PARAM_STR);
    $stmt->bindParam(':favourite', $favourite, PDO::PARAM_INT);
    $stmt->bindParam(':createdAt', $createdAt);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Failed";
    }
}
?>
