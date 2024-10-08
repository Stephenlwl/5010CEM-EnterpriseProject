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

session_start();

// Include database and user connection file
include_once '../config/database.php';
include_once '../models/user.php';

// Database connection
$database = new Database_Auth();
$db = $database->getConnection();

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);

// Get the user input data
$email = $input['email'] ?? ''; 
$password = $input['password'] ?? '';

// Instantiate User object
$user = new User($db);

// Check credentials
$UserData = $user->checkCredentials($email, $password); 
if ($UserData) {
    $_SESSION['email'] = $email;
    $_SESSION['username'] =  $UserData['Username'];
    $_SESSION['user_id'] = $UserData['UserID'];
    echo json_encode(['success' => true, 'username' => $UserData['Username']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password. Please try again.']); 
}
?>
