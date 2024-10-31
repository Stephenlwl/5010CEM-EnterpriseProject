<?php
require_once '../config/database.php';

if (isset($_GET['ItemID'])) {
    $ItemID = (int)$_GET['ItemID']; //  cast to int 

    $database = new Database_Auth();
    $db = $database->getConnection();

    $query = "SELECT ImagePath FROM menu WHERE ItemID = :ItemID"; 
    $stmt = $db->prepare($query);
    $stmt->bindParam(':ItemID', $ItemID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        header("Content-Type: image/jpeg"); // image type
        echo $row['ImagePath']; // output the binary image data
        exit;
    } else {
        http_response_code(404);
        echo "Image not found.";
        exit;
    }
} else {
    http_response_code(400);
    echo "No image ID provided.";
    exit;
}
