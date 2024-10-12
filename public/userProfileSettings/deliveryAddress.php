<?php
session_start();

require_once '../auth/config/database.php';
require_once '../auth/models/user.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Fetch user data
$UserID = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE UserID = :UserID";
$stmt = $db->prepare($query);
$stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT); // Bind UserID as an integer
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data exists
if (!$userData) {
    echo "User data not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riberio Cafe Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/publicDefault.css">
    <link rel="stylesheet" href="../css/deliveryAddress.css">
    <script src="js/deliveryAddress.js"></script>
</head>

<body>
    <div class="container mt-5 mb-4">
        <h1 class="text-center">Delivery</h1>
        <h2 class="text-center mb-4">Delivery Address</h2>
        <hr>
        <h4>Saved Addresses</h4>
        
        <?php
            $query = "SELECT * FROM Address WHERE UserID = :UserID";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':UserID', $UserID);
            $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Loop through each address and display it
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <div class="container-borderframe">
                    <div class="row mb-3">
                        <div class="col-sm-8">
                            <label for="address_name" class="form-label">' . htmlspecialchars($row['AddressName']) . '</label>
                            <p>' . htmlspecialchars($row['Address1']) . '</p>
                            <p>' . htmlspecialchars($row['Address2']) . '</p>
                            <p>' . htmlspecialchars($row['PostalCode']) . ', ' . htmlspecialchars($row['State']) . '</p>
                        </div>
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editAddressModal"
                            data-user-id="' . $UserID . '" 
                            data-id="' . $row['AddressID'] . '" data-name="' . htmlspecialchars($row['AddressName']) . '" 
                            data-address1="' . htmlspecialchars($row['Address1']) . '" data-address2="' . htmlspecialchars($row['Address2']) . '" 
                            data-postal="' . htmlspecialchars($row['PostalCode']) . '" data-state="' . htmlspecialchars($row['State']) . '">Edit</button>
                            <button type="button" class="btn btn-danger" onclick="deleteAddress(' . $row['AddressID'] . ', ' . $UserID . ')">Delete</button>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="alert alert-warning text-center" role="alert">
                    No addresses have been created yet! To ensure smooth order deliveries, please add an Address .
                </div>';
        }
        ?>

        <br>
        <button type="button" class="btn btn-primary me-2 mx-auto" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New Address</button>
    </div>

    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAddressForm">
                        <input type="hidden" id="editUserID" name="user_id">
                        <input type="hidden" id="editAddressID" name="address_id" value="<?php isset($_GET['edit_address_id'])?>">
                        <div class="mb-3">
                            <label for="editAddressName" class="form-label">Address Name</label>
                            <input type="text" class="form-control" id="editAddressName" name="address_name" value="<?php if(isset($_GET['edit_address_id']) && $_GET['edit_address_id'] == $row['AddressID']) { echo htmlspecialchars($row['AddressName']); } ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="editAddress1" name="address1" value="<?php if(isset($_GET['edit_address_id']) && $_GET['edit_address_id'] == $row['AddressID']) { echo htmlspecialchars($row['Address1']); } ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="editAddress2" name="address2" value="<?php if(isset($_GET['edit_address_id']) && $_GET['edit_address_id'] == $row['AddressID']) { echo htmlspecialchars($row['Address2']); } ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editPostalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="editPostalCode" name="postal_code" value="<?php if(isset($_GET['edit_address_id']) && $_GET['edit_address_id'] == $row['AddressID']) { echo htmlspecialchars($row['PostalCode']); } ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editState" class="form-label">State</label>
                            <input type="text" class="form-control" id="editState" name="state" value="<?php if(isset($_GET['edit_address_id']) && $_GET['edit_address_id'] == $row['AddressID']) { echo htmlspecialchars($row['State']); } ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAddressForm">
                        <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo htmlspecialchars($UserID); ?>">
                        <div class="mb-3">
                            <label for="addAddressName" class="form-label">Address Name</label>
                            <input type="text" class="form-control" id="addAddressName" name="address_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="addAddress1" name="address1" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="addAddress2" name="address2">
                        </div>
                        <div class="mb-3">
                            <label for="addPostalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="addPostalCode" name="postal_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="addState" class="form-label">State</label>
                            <input type="text" class="form-control" id="addState" name="state" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Address</button>
                    </form>
                </div>
            </div>
        </div>
    </div>   
</body>

</html>