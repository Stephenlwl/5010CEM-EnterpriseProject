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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $database = new Database_Auth();
        $db = $database->getConnection();

        // Ensure UserID is in session
        $userId = $_SESSION['UserID'] ?? null;

        if (!$userId) {
            throw new Exception('User not logged in');
        }

        // Fftch current user data
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
                $response['success'] = true;
                $response['message'] = 'Name updated successfully';
                $nameUpdated = true;
            } else {
                throw new Exception('Failed to update name');
            }
        }

        //  if both current and new passwords are provided
        if (!empty($currentPassword) && !empty($newPassword)) {
            // Verify current password
            
            // after finish testing should remove the below if code
            if ($currentPassword === $userData['Password']) {
            // should use the below code after testing 
            // if (password_verify($currentPassword, $userData['Password'])) {
                // hash the new password
                $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($user->updatePassword($userId, $newPasswordHashed)) {
                    $response['success'] = true;
                    $response['message'] = 'Password updated successfully';
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
        }
        // check if no changes were submitted
        if (!$response['success']) {
            throw new Exception('No changes submitted');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

// return the response as JSON
echo json_encode($response);

?>