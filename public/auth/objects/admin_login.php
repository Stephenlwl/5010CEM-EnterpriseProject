<?php

// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1); 
error_log("error.log");

session_start();

// Include database and user connection file
include_once '../../auth/config/database.php';
include_once '../../auth/models/admin.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);

// get the user input data
$admin_email = $input['admin_email'] ?? '';
$admin_password = $input['admin_password'] ?? '';

// Instantiate User object
$admin = new Admin($db);

// Check credentials
$AdminData = $admin->checkCredentials($admin_email, $admin_password);

if ($AdminData) {
    $_SESSION['admin_email'] = $admin_email;
    $_SESSION['admin_id'] = $AdminData['AdminID'];
    $_SESSION['admin_username'] = $AdminData['AdminName'];
    
    echo json_encode(['success' => true, 'admin_username' => $AdminData['AdminName']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password. Please try again.']);
}

?>

