<?php
session_start();
require_once '../auth/config/database.php';
require_once '../auth/objects/pagination.php';

$database = new Database_Auth();
$db = $database->getConnection();

$admin_id = $_SESSION['AdminID'];

if (!$admin_id){
    header("Location: adminLogin.php");
    exit();
}

$promoCodePerPage = 5;  
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $promoCodePerPage;

$countQuery = "SELECT COUNT(*) as totalPromoCode
               FROM `promotions` p";

$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalPromoCodes = $countStmt->fetch(PDO::FETCH_ASSOC)['totalPromoCode'];

$totalPages = ceil($totalPromoCodes / $promoCodePerPage);


$query = "SELECT * FROM promotions
          ORDER BY PromotionCreatedAt DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
$stmt->bindParam(':limit', $promoCodePerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT); 
$stmt->execute();
$promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- <link rel="stylesheet" href="css/promotionSettings.css"> -->
    <link rel="stylesheet" href="css/adminPublicDefault.css">
    <script src="js/promotionSettings.js"></script>
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
                    <h1 class="text-center">Promotion Setting Dashboard</h1>
                    <hr>

                    <!-- add new promotion code section -->
                    <div class="row mb-4">
                        <div class="col-md-11 container shadow p-2 rounded">
                            <h4 class="mb-3">Existing Promotions</h4>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Promo Code</th>
                                        <th>Promotion Price</th>
                                        <th>Created At</th>
                                        <th>Promo End Date</th>
                                        <th>Terms and Conditions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($promotions as $promotion): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($promotion['PromoCode']) ?></td>
                                        <td><?= $promotion['DiscountType'] === 'percentage' ? '' : 'RM' ?>
                                            <?= $promotion['DiscountType'] === 'percentage' ? $promotion['DiscountValue'] . '%' : number_format($promotion['DiscountValue'], 2) ?>
                                        </td>
                                        <td><?= date('d M Y - H:i', strtotime($promotion['PromotionCreatedAt'])) ?></td>
                                        <td><?= date('d M Y', strtotime($promotion['PromotionEndDate'])) ?></td>
                                        <td><?= htmlspecialchars($promotion['TermsAndConditions']) ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removePromoModal" onclick="setPromoCode('<?= htmlspecialchars($promotion['PromoCode']) ?>')" >Delete</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php renderPagination($currentPage, $totalPages); ?>
                        </div>
                    </div>
                    <div class="row mb-4 mt-4"> 
                        <div class="col-md-6 mt-4 container shadow p-5 rounded">
                            <h4>Add New Promotion</h4>
                            <form id="addPromoForm">
                                <div class="mb-3">
                                    <label for="PromoCode" class="form-label">Promo Code</label>
                                    <input type="text" class="form-control" id="PromoCode" name="PromoCode" required>
                                </div>
                                <div class="mb-3">
                                    <label for="DiscountType" class="form-label">Discount Type</label>
                                    <select id="DiscountType" name="DiscountType" class="form-control" required>
                                        <option value="fixed">Fixed Amount (e.g., RM5)</option>
                                        <option value="percentage">Percentage (e.g., 5%)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="DiscountValue" class="form-label">Discount Value</label>
                                    <input type="number" class="form-control" id="DiscountValue" name="DiscountValue" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label for="promoEndDate" class="form-label">Promotion End Date</label>
                                    <input type="date" class="form-control" id="promoEndDate" name="promoEndDate" required>
                                </div>
                                <div class="mb-3">
                                    <label for="TermsAndConditions" class="form-label">Terms and Conditions</label>
                                    <textarea class="form-control" id="TermsAndConditions" name="TermsAndConditions" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Promotion</button>
                            </form>
                        </div>
                    </div>

                    <!-- Remove Promo Code Modal -->
                    <div class="modal fade" id="removePromoModal" tabindex="-1" aria-labelledby="removePromoModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="removePromoModalLabel">Remove Promotion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to remove the promotion code <span class="text-danger" id="promoCodeToRemove"></span>? This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" onclick="removePromotion()">Yes, Remove Promotion</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
