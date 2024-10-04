<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/orderHistory.css">
    <link rel="stylesheet" href="../css/publicDefault.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <div class="container mt-5 mb-4">
        <h1 class="text-center">Favourite List</h1>
        <h2 class="text-center mb-4">My Favourites</h2>
        <hr>
        <div class="container-borderframe p-3 mb-3">
            <div class="row">
                <!-- Add d-flex to align the image and content side by side -->
                <div class="col-4 d-flex align-items-center">
                    <img src="../img/coffee.jpg" alt="#" class="img-fluid" style="max-height: 200px; width: auto;">
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="order_type" class="form-label">Order Type:</label>
                            <p class="mb-1">Self-Pickup</p>
                        </div>
                        <div class="col-sm-6">
                            <label for="pickup-at" class="form-label">Pickup At:</label>
                            <p class="mb-1">Sunway Velocity</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6 p-3">
                            <a href="#">View Order Details</a>
                        </div>
                        <div class="col-sm-6 text-end">
                            <label for="order_total" class="form-label">Total:</label>
                            <h4 class="mb-0">RM 33.50</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>


</body>

</html>