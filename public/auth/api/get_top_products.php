<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

$response = array('success' => false, 'data' => [], 'message' => '');

try {
    $database = new Database_Auth();
    $db = $database->getConnection();

    // get the top 5 products
    $query = "SELECT m.ItemName, SUM(rd.ItemQuantity) AS TotalQuantityOrdered
              FROM receipt_details rd
              JOIN menu m ON rd.ItemID = m.ItemID
              GROUP BY m.ItemName
              ORDER BY TotalQuantityOrdered DESC
              LIMIT 5";

    $result = $db->query($query);
    $topProducts = [];

    if ($result) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $topProducts[] = [
                'ItemName' => $row['ItemName'],
                'TotalQuantityOrdered' => (int) $row['TotalQuantityOrdered']
            ];
        }

        // get the top 3 coffee drinks
        $queryCoffeeItems = "SELECT m.ItemName, SUM(rd.ItemQuantity) AS TotalCoffeeDrinkSold, m.ItemID
                             FROM receipt_details rd
                             JOIN menu m ON rd.ItemID = m.ItemID
                             WHERE m.ItemType = 'coffee'
                             GROUP BY m.ItemName
                             ORDER BY TotalCoffeeDrinkSold DESC
                             LIMIT 6";

        $resultCoffee = $db->query($queryCoffeeItems);
        $topCoffeeDrink = [];

        if ($resultCoffee) {
            while ($row = $resultCoffee->fetch(PDO::FETCH_ASSOC)) {
                $topCoffeeDrink[] = [
                    'ItemName' => $row['ItemName'],
                    'TotalCoffeeDrinkSold' => (int) $row['TotalCoffeeDrinkSold'],
                    'ItemID' => $row['ItemID']
                ];
            }
        } else {
            $response['message'] = "Failed to fetch top coffee drinks.";
        }

        $response['success'] = true;
        $response['data'] = [
            'topProducts' => $topProducts,
            'topCoffeeDrinks' => $topCoffeeDrink
        ];

    } else {
        $response['message'] = "Failed to fetch top products.";
    }
} catch (Exception $e) {
    $response['message'] = "An error occurred: " . $e->getMessage();
}

echo json_encode($response);
?>
