<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemID = isset($_POST['itemID']) ? $_POST['itemID'] : null;

    if ($itemID && isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['itemID'] == $itemID) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);  // Reindex the array
                break;
            }
        }
    }
}

header('Location: cart.php');
exit();
