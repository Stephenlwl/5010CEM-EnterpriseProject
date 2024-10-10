<?php
header("Content-Type: application/json; charset=UTF-8");

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

// get the user input data
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// Instantiate User object
$user = new User($db);

// Check credentials
$user_id = $user->checkCredentials($username, $password);
if ($user_id) {
    $_SESSION['username'] = $username;
    $_SESSION['user_id'] = $user_id;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password. Please try again.']);
}
?>

