<?php
session_start();

if (isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'];

    // Remove the item from the cart
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $item_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    // Reindex the cart array to prevent gaps
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
