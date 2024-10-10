<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Start output buffering
ob_start();

header('Content-Type: application/json');

// Collect POST data
$data = json_decode(file_get_contents('php://input'), true);
$response = [];

if (isset($data['send'], $data['email'], $data['username'], $data['password'])) {
    $email = $data['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $otp_code = rand(1000, 9999);
        $subject = "Email Verification Code OTP - Rimberio Cafe";
        $emailbody = "Your One-Time Password (OTP) for email verification is: <strong><big>$otp_code</big></strong>";
        $_SESSION['otp_code'] = $otp_code; // Store OTP code

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0;  // Disable verbose debug output for production
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = "stephenlwlhotmailcom@gmail.com";  // Gmail account
            $mail->Password = 'xyjkgynvtyncnths'; // Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587;  // TCP port to connect to

            // Recipients
            $mail->setFrom('stephenlwlhotmailcom@gmail.com', 'Rimberio Cafe'); //The Clinic admin Gmail Account
            $mail->addAddress($email);  // the user email account

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $emailbody;

            $mail->send();
            //parse the needed data to the response 
            $response = ['status' => 'success', 'message' => 'OTP sent successfully.', 'otp_code' => $otp_code];
        } catch (Exception $e) {
            $response = ['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid email address.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request.'];
}

// Clean the output buffer and turn off output buffering
ob_end_clean();

echo json_encode($response);
?>
