<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

$response = array('success' => false, 'data' => [], 'message' => '');

try {
    $database = new Database_Auth();
    $db = $database->getConnection();

    // to get daily sales
    $dailyQuery = "SELECT DATE(r.ReceiptCreatedAt) AS SaleDate, SUM(r.TotalPrice - r.DiscountAmount) AS DailySales, SUM(CASE WHEN m.ItemType = 'coffee' THEN rd.ItemQuantity ELSE 0 END) AS CoffeeSales
                   FROM receipt_details rd
                   JOIN `order` o ON rd.ReceiptID = o.ReceiptID
                   INNER JOIN receipt r ON o.ReceiptID = r.ReceiptID
                   INNER JOIN menu m ON rd.ItemID = m.ItemID
                   GROUP BY SaleDate
                   ORDER BY SaleDate ASC";
    
    $dailyResult = $db->query($dailyQuery);
    $dailySales = [];

    if ($dailyResult) {
        while ($row = $dailyResult->fetch(PDO::FETCH_ASSOC)) {
            $dailySales[] = [
                'SaleDate' => $row['SaleDate'],
                'DailySales' => (float) $row['DailySales'],
                'CoffeeSold' => (int) $row['CoffeeSales']
            ];
        }
    } else {
        $response['message'] = "Failed to fetch daily sales.";
    }

    // to get weekly sales
    $weeklyQuery = "SELECT YEAR(r.ReceiptCreatedAt) AS Year, WEEK(r.ReceiptCreatedAt) AS Week, SUM(r.TotalPrice - r.DiscountAmount) AS WeeklySales
                    FROM receipt_details rd
                    JOIN `order` o ON rd.ReceiptID = o.ReceiptID
                    INNER JOIN receipt r ON o.ReceiptID = r.ReceiptID
                    GROUP BY Year, Week
                    ORDER BY Year ASC, Week ASC";

    $weeklyResult = $db->query($weeklyQuery);
    $weeklySales = [];

    if ($weeklyResult) {
        while ($row = $weeklyResult->fetch(PDO::FETCH_ASSOC)) {
            $weeklySales[] = [
                'Year' => $row['Year'],
                'Week' => $row['Week'],
                'WeeklySales' => (float) $row['WeeklySales']
            ];
        }
    } else {
        $response['message'] = "Failed to fetch weekly sales.";
    }

    // to get monthly sales
    $monthlyQuery = "SELECT DATE_FORMAT(r.ReceiptCreatedAt, '%Y-%m') AS Month, SUM(r.TotalPrice - r.DiscountAmount) AS MonthlySales
                     FROM receipt_details rd
                     JOIN `order` o ON rd.ReceiptID = o.ReceiptID
                     INNER JOIN receipt r ON o.ReceiptID = r.ReceiptID
                     GROUP BY Month
                     ORDER BY Month ASC";

    $monthlyResult = $db->query($monthlyQuery);
    $monthlySales = [];

    if ($monthlyResult) {
        while ($row = $monthlyResult->fetch(PDO::FETCH_ASSOC)) {
            $monthlySales[] = [
                'Month' => $row['Month'],
                'MonthlySales' => (float) $row['MonthlySales']
            ];
        }
    } else {
        $response['message'] = "Failed to fetch monthly sales.";
    }

    // assemble final response
    $response['success'] = true;
    $response['data'] = [
        'dailySales' => $dailySales,
        'weeklySales' => $weeklySales,
        'monthlySales' => $monthlySales
    ];
} catch (Exception $e) {
    $response['message'] = "An error occurred: " . $e->getMessage();
}

echo json_encode($response);
?>
