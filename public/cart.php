<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
<?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
        include($IPATH."nav.php"); 
    ?>
    <div class="container mt-5">
        <h2>Your Cart</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Customizations</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                            <td>$<?php echo number_format($item['itemPrice'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>
                                Temperature: <?php echo htmlspecialchars($item['customization']['temperature']); ?><br>
                                Milk: <?php echo htmlspecialchars($item['customization']['milk']); ?><br>
                                Size: <?php echo htmlspecialchars($item['customization']['size']); ?><br>
                                Syrup: <?php echo htmlspecialchars($item['customization']['syrup']); ?>
                            </td>
                            <td>$<?php echo number_format($item['itemPrice'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php $total += $item['itemPrice'] * $item['quantity']; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                    <td>
                        <form action="remove_from_cart.php" method="post">
                            <input type="hidden" name="itemID" value="<?php echo $item['itemID']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>

                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include($IPATH."footer.html"); ?>

</body>

</html>