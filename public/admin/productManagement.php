<?php
session_start();

require_once '../auth/config/database.php';
require_once '../auth/objects/pagination.php';

// Generate CSRF token if not exists
// if (empty($_SESSION['csrf_token'])) {
//     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// }

$database = new Database_Auth();
$db = $database->getConnection();

$admin_id = isset($_SESSION['AdminID']) ? (int)$_SESSION['AdminID'] : 0;

if (!$admin_id) {
    header("Location: adminLogin.php");
    exit();
}

try {
    $totalProductPerPage = 5;  
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($currentPage - 1) * $totalProductPerPage;

    // Query to count the total number of products
    $countQuery = "SELECT COUNT(*) as totalProduct FROM `menu`";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute();
    $totalProduct = $countStmt->fetch(PDO::FETCH_ASSOC)['totalProduct'];

    $totalPages = ceil($totalProduct / $totalProductPerPage);

    // Query to fetch products for the current page
    $menuQuery = "SELECT ItemID, ItemName, ItemPrice, ItemQuantity, ItemType, ImagePath 
                  FROM `menu`
                  ORDER BY ItemID DESC
                  LIMIT :offset, :limit";
    $menuStmt = $db->prepare($menuQuery);
    $menuStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $menuStmt->bindParam(':limit', $totalProductPerPage, PDO::PARAM_INT);
    $menuStmt->execute();
    $menu = $menuStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "A database error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/productManagement.css">

</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Navigation -->
            <div class="col-md-2">
                <?php include_once "inc/nav.php"; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="main-content">
                <h1 class="mb-3">Product Setting Dashboard</h1>
                <hr>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- Products Table -->
                    <div class="row mb-4">
                        <div class="col-12 shadow p-3 rounded">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Products</th>
                                        <th>Price</th>
                                        <th>Type</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($menu)): ?>
                                        <?php foreach ($menu as $product): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($product['ItemID']) ?></td>
                                                <td><?= htmlspecialchars($product['ItemName']) ?></td>
                                                <td>RM<?= number_format($product['ItemPrice'], 2) ?></td>
                                                <td><?= htmlspecialchars($product['ItemType']) ?></td>
                                                <td>
                                                <img src="../auth/api/get_image_from_menu.php?ItemID=<?= htmlspecialchars($product['ItemID'] ?? '') ?>" 
                                                    onerror="this.onerror=null; this.src='../img/coffee-placeholder.jpg';"
                                                    alt="<?= htmlspecialchars($product['ItemName']) ?>"
                                                    class="img-thumbnail" 
                                                    style="max-width: 100px;">
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editProductModal" 
                                                            onclick="editProduct('<?= $product['ItemID'] ?>', '<?= htmlspecialchars($product['ItemName'], ENT_QUOTES) ?>', '<?= $product['ItemPrice'] ?>', '<?= htmlspecialchars($product['ImagePath'], ENT_QUOTES) ?>')">
                                                            <i class="bi bi-pen"></i> Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#removeProductModal" 
                                                            onclick="setProductName('<?= $product['ItemName'] ?>', '<?= $product['ItemID'] ?> ')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center">No products found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <?php renderPagination($currentPage, $totalPages); ?>
                        </div>
                    </div>


                    <!-- Add Product Form -->
                    <div class="row mb-4">
                        <div class="col-md-6 shadow p-4 rounded">
                            <h4 class="mb-3">Add New Product</h4>
                            <form id="addProductForm">
                                <div class="mb-3">
                                    <label for="ItemName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="ItemName" name="ItemName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="ItemPrice" class="form-label">Product Price (RM)</label>
                                    <input type="number" class="form-control" id="ItemPrice" name="ItemPrice" step="0.01" min="1" max="299" required>
                                </div>
                                <div class="mb-3">
                                    <label for="ItemType" class="form-label">Product Type</label>
                                    <select class="form-control" id="ItemType" name="ItemType" required>
                                        <option value="">Select Type</option>
                                        <option value="food">Food</option>
                                        <option value="coffee">Coffee</option>
                                        <option value="item">Merchandise </option>
                                        <option value="coffeeBean">Coffee Bean</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="ImagePath" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="ImagePath" name="ImagePath" accept="image/*" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add Product
                                </button>
                            </form>
                        </div>
                    </div>

                     <!-- Edit Product Modal -->
                    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header-edit">
                                    <button type="button" class="btn-close m-1" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <h5 class="modal-title m-2" id="editProductModalLabel">Edit 
                                        <span id="productName" name="productName"></span>
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <form id="editProductForm" onsubmit="updateProduct(event)">
                                        <input type="hidden" id="itemID" name="itemID">
                                        <div class="mb-3">
                                            <label for="currentProductName" class="form-label">Current Product Name</label>
                                            <input type="text" class="form-control" id="currentProductName" name="currentProductName" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="newProductName" class="form-label">Change Product Name</label>
                                            <input type="text" class="form-control" id="newProductName" name="newProductName">
                                        </div>
                                        <div class="mb-3">
                                            <label for="currentPrice" class="form-label">Current Price (RM)</label>
                                            <input type="number" class="form-control" id="currentPrice" name="currentPrice" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="newProductPrice" class="form-label">New Product Price (RM)</label>
                                            <input type="number" class="form-control" id="newProductPrice" name="newProductPrice">
                                        </div>
                                        <div class="mb-3">
                                            <label for="currentImagePath" class="form-label">Product Image URL</label>
                                            <input type="file" class="form-control" id="currentImagePath" name="currentImagePath" accept="image/*">
                                        </div>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Product Modal -->
    <div class="modal fade" id="removeProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-remove">
                    <button type="button" class="btn-close m-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title m-2">Remove Product</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove product *<span class="text-danger" id="productNameToRemove"></span>?
                    This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="removeProduct()">
                        <i class="bi bi-trash"></i> Remove Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/productManagement.js"></script>
</body>
</html>