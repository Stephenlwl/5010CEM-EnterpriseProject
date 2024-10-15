<?php
require_once 'C:/xampp/htdocs/RimberioCafeWebsite/5010CEM-EnterpriseProject/vendor/autoload.php';

// init configuration
$clientID = '642151010969-jgl01vneit0jgc6m78bu5gp2cq3m201k.apps.googleusercontent.com'; 
$clientSecret = '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'; 
$redirectUri = 'http://localhost/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/google_login.php'; 

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
$client->setAccessType('offline');  // Important for obtaining a refresh token
$client->setPrompt('consent');      // Ensures we get the refresh token on every request

// Check if authorization code is in the URL
if (isset($_GET['code'])) {
    // Fetch access token with the authorization code
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Check if there was an error during token fetching
    if (isset($token['error'])) {
        error_log("Error fetching the access token: " . $token['error_description']);
        echo "Error fetching the access token: " . htmlspecialchars($token['error']);
        exit;
    }

    // Set the access token for the client
    $client->setAccessToken($token['access_token']);

    // DEBUG: Check the token info
    var_dump($token);

    // Save the refresh token (if available) to a session or database
    session_start();
    if (isset($token['refresh_token'])) {
        $_SESSION['refresh_token'] = $token['refresh_token'];
    }

    // Current time and token creation time for debugging
    $timeNow = time();
    $tokenCreationTime = $token['created'];
    echo "Current time: $timeNow, Token created at: $tokenCreationTime, Expires in: " . ($tokenCreationTime + $token['expires_in'] - $timeNow) . " seconds.";

    // Check if the access token is expired
    if ($client->isAccessTokenExpired()) {
        // Try to refresh the token using the saved refresh token
        if (isset($_SESSION['refresh_token'])) {
            $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
            // Save the new access token to the session or database
            $_SESSION['access_token'] = $client->getAccessToken();
        } else {
            echo "Access token expired and no refresh token available.";
            exit;
        }
    }

    // If token is valid, get profile info from Google
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    // Redirect to signup process or login
    header('Location: /RimberioCafeWebsite/5010CEM-EnterpriseProject/public/profile.php?google_name=' . urlencode($name) . '&google_email=' . urlencode($email));
    exit;

} else {
    // If no authorization code, create the Google Auth URL for login
    echo "<a href='" . htmlspecialchars($client->createAuthUrl()) . "'>Google Login</a>";
}
?>
