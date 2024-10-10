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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/publicDefault.css">
    <link rel="stylesheet" href="../css/editProfile.css">
    <script src="js/editProfile.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Account Information</h1>
        <h2 class="text-center mb-4">Edit Profile</h2>
        <hr>
        <form id="editProfileForm">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['Username']); ?>" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-5">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($userData['Email']); ?>" class="form-control" readonly>
                </div>
                <div class="col-sm-5 text-muted">    
                    <p class="mt-4 text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Cannot change email</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-6">
                    <label for="phone" class="form-label">Phone Number:</label>
                    <input type="tel" id="phone_no" name="phone" value="<?php echo htmlspecialchars($userData['PhoneNumber']); ?>" class="form-control" readonly>
                </div>
                <div class="col-sm-5 text-muted">    
                    <p class="mt-4 text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Cannot change phone number</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-6">
                    <label for="current_password" class="form-label">Current Password:</label>
                    <input type="text" id="current_password" name="current_password" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-6">
                    <label for="new_password" class="form-label">New Password:</label>
                    <input type="text" id="new_password" name="new_password" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                    <button type="reset" class="btn btn-danger">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
