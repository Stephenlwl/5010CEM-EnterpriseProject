<?php

session_start();

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../models/user.php';

$response = array('success' => false, 'message' => '');

ob_start();
// Check if form is submitted
error_log(print_r($_POST, true));
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Ensure UserID is in session
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            throw new Exception('User not logged in');
        }

        // Fetch current user data
        $query = "SELECT Username, Password FROM users WHERE UserID = :UserID";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':UserID', $userId);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            throw new Exception('User not found');
        }

        // instantiate User object
        $user = new User($db);

        $newName = $_POST['username'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        // initialize variables to track if name and password were updated
        $nameUpdated = false;
        $passwordUpdated = false;

        // Check if new name is different from current name
        if (!empty($newName) && $newName !== $userData['Username']) {
            if ($user->updateName($userId, $newName)) {
                $nameUpdated = true;
            } else {
                throw new Exception('Failed to update name');
            }
        }

        //  if both current and new passwords are provided
        if (!empty($currentPassword) && !empty($newPassword)) {
            // Verify current password
            
            if (password_verify($currentPassword, $userData['Password'])) {
                // hash the new password
                $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($user->updatePassword($userId, $newPasswordHashed)) {
                    $passwordUpdated = true;
                } else {
                    throw new Exception('Failed to update password');
                }
            } else {
                throw new Exception('Current password is incorrect');
            }
        } else if (!empty($currentPassword) || !empty($newPassword)) {
            throw new Exception('Both current and new passwords are required');
        }

        //  check if name and password were updated
        if ($nameUpdated && $passwordUpdated) {
            $response['success'] = true;
            $response['message'] = 'Profile name and password updated successfully';
        } else if ($nameUpdated) {
            $response['success'] = true;
            $response['message'] = 'Name updated successfully';
        } else if ($passwordUpdated) {
            $response['success'] = true;
            $response['message'] = 'Password updated successfully';
        } else {
            throw new Exception('No changes submitted');
        }

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

// return the response as JSON
echo json_encode($response);

?>
