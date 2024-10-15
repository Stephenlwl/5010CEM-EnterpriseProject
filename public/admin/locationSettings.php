<?php
session_start();
require_once '../auth/config/database.php';
require_once '../auth/objects/pagination.php';

$database = new Database_Auth();
$db = $database->getConnection();

$admin_id = $_SESSION['AdminID'];

$totalAddressPerPage = 5;  
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $totalAddressPerPage;

$countQuery = "SELECT COUNT(*) as totalAddress FROM `address` WHERE AdminID IS NOT NULL";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalAddress = $countStmt->fetch(PDO::FETCH_ASSOC)['totalAddress'];

$totalPages = ceil($totalAddress / $totalAddressPerPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion Setting Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <script src="js/locationSettings.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <?php 
                $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/admin/inc/";
                include($IPATH."nav.php"); 
                ?>
            </div>
            <div class="col-md-10">
                <div class="main-content">
                    <h1 class="text-center my-4">Store Location Settings Dashboard</h1>
                    <hr class="mb-4">

                    <?php
                        $query = "SELECT * FROM address WHERE AdminID IS NOT NULL";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        // Start output buffering
                        ob_start();
                        
                        if ($stmt->rowCount() > 0) {
                            // Loop through each address and display it
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <div class="container-borderframe p-3 mb-4 border rounded shadow">
                                    <div class="row mb-3 p-2 ">
                                        <div class="col-sm-7">
                                            <h5 class="form-label mb-4"><?= htmlspecialchars($row['AddressName']) ?></h5>
                                            <p><strong>Address 1: </strong><?= htmlspecialchars($row['Address1']) ?></p>
                                            <p><strong>Address 2: </strong><?= htmlspecialchars($row['Address2']) ?></p>
                                            <p><strong>State: </strong><?= htmlspecialchars($row['PostalCode']) ?>, <?= htmlspecialchars($row['State']) ?></p>
                                        </div>
                                        <div class="col-sm-5 text-end">
                                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editAddressModal" 
                                            data-admin-id="<?= $admin_id ?>"
                                            data-id="<?= $row['AddressID'] ?>" data-name="<?= htmlspecialchars($row['AddressName']) ?>" 
                                            data-address1="<?= htmlspecialchars($row['Address1']) ?>" data-address2="<?= htmlspecialchars($row['Address2']) ?>" 
                                            data-postal="<?= htmlspecialchars($row['PostalCode']) ?>" data-state="<?= htmlspecialchars($row['State']) ?>">Edit</button>
                                            
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeAddressModal" 
                                                onclick="setRemoveAddressId('<?= htmlspecialchars($row['AddressID']) ?>', '<?= htmlspecialchars($row['AddressName']) ?>', '<?= htmlspecialchars($row['AdminID']) ?>')">
                                            Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="alert alert-warning text-center" role="alert">
                                No addresses have been created yet! To ensure smooth order deliveries, please add an address.
                            </div>
                            <?php
                        }
                    ?>

                    <br>
                    <button type="button" class="btn btn-primary me-2 mx-auto d-block" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New Address</button>
                </div>

                <?php renderPagination($currentPage, $totalPages); ?>
                
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
                                    <input type="hidden" id="editAdminID" name="admin_id">
                                    <input type="hidden" id="editAddressID" name="address_id">
                                    <div class="mb-3">
                                        <label for="editAddressName" class="form-label">Address Name</label>
                                        <input type="text" class="form-control" id="editAddressName" name="address_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editAddress1" class="form-label">Address Line 1</label>
                                        <input type="text" class="form-control" id="editAddress1" name="address1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editAddress2" class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control" id="editAddress2" name="address2">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editPostalCode" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="editPostalCode" name="postal_code" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editState" class="form-label">State</label>
                                        <input type="text" class="form-control" id="editState" name="state" required>
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
                                <input type="hidden" class="form-control" id="admin_id" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>">
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
                                    <button type="submit" class="btn btn-success">Add Address</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remove Promo Code Modal -->
                <div class="modal fade" id="removeAddressModal" tabindex="-1" aria-labelledby="removeAddressModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="removeAddressModalLabel">Remove Address</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to remove this <span class="text-danger" id="addressToRemove"></span> address? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" onclick="removeAddress()">Yes, Remove Address</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
