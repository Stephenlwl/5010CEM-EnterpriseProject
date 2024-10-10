<?php

// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../config/database.php';
include_once '../models/user.php';

$database = new Database_Auth();
$db = $database->getConnection();

// Initialize response array
$response = [];

// Check if data is received via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate incoming JSON data
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->username) && isset($data->email) && isset($data->password)){
        // Retrieve user input
        $username = $data->username;
        $email = $data->email;
        $password = $data->password;
        
        // Validate data
        if (strlen($username) < 8 || strlen($password) < 8) {
            $response['errors']['validation'] = "Username and password must be at least 8 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['errors']['validation'] = "Invalid email format.";
        } else {
            // Check if username or email already exists
            $query = "SELECT UserID FROM users WHERE Email = :email LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response['errors']['validation'] = "Email already existing!";
            } else {
                // Create a new user instance
                $user = new User($db);

                // Set properties
                $user->username = htmlspecialchars(strip_tags($username));
                $user->email = htmlspecialchars(strip_tags($email));
                $user->password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into database
                if ($user->create()) {
                    $response['success'] = true;
                    $response['message'] = "User registered successfully.";
                    $response['redirect'] = true;
                    http_response_code(201); // Created
                } else {
                    $response['errors']['database'] = "Unable to register user.";
                    http_response_code(500); // Server error
                }
            }
        }
    } else {
        $response['errors']['data'] = "All fields are required.";
        http_response_code(400); // Bad request
    }
} else {
    $response['errors']['method'] = "Method not allowed.";
    http_response_code(405); // Method not allowed
}

// Output JSON response
echo json_encode($response);
?>