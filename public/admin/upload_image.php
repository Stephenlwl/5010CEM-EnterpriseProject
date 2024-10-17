<?php
// At the top of your productManagement.php, add these constants
define('UPLOAD_DIR', 'uploads/products/'); // Create this directory and ensure it's writable
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Update your form HTML to accept file upload
?>
<!-- Replace your existing image URL input with this file input -->
<div class="mb-3">
    <label for="ProductImage" class="form-label">Product Image</label>
    <input type="file" class="form-control" id="ProductImage" name="ProductImage" accept="image/*" required>
    <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 5MB</div>
</div>

<!-- Update your table image cell to include a preview modal -->
<td>
    <img src="<?= htmlspecialchars($product['ImagePath']) ?>" 
         alt="<?= htmlspecialchars($product['ItemName']) ?>" 
         class="img-thumbnail product-image" 
         style="max-width: 100px; cursor: pointer;"
         onclick="showImagePreview('<?= htmlspecialchars($product['ImagePath']) ?>', '<?= htmlspecialchars($product['ItemName']) ?>')">
</td>

<!-- Add this modal for image preview -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Create a new file: upload_image.php -->
<?php
session_start();
header('Content-Type: application/json');

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['success' => false, 'message' => 'Invalid security token']));
}

if (!isset($_FILES['image'])) {
    die(json_encode(['success' => false, 'message' => 'No image uploaded']));
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpPath = $file['tmp_name'];
$fileSize = $file['size'];
$fileType = $file['type'];
$fileError = $file['error'];

// Validate file
if ($fileError !== UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'Error uploading file']));
}

if ($fileSize > MAX_FILE_SIZE) {
    die(json_encode(['success' => false, 'message' => 'File too large']));
}

if (!in_array($fileType, ALLOWED_TYPES)) {
    die(json_encode(['success' => false, 'message' => 'Invalid file type']));
}

// Generate unique filename
$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
$newFileName = uniqid() . '.' . $fileExtension;
$uploadPath = UPLOAD_DIR . $newFileName;

// Create upload directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Move uploaded file
if (move_uploaded_file($fileTmpPath, $uploadPath)) {
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'path' => $uploadPath
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving file'
    ]);
}