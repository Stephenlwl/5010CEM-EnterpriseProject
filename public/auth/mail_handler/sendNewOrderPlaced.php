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

if ($data['username']) {
    $email = 'stephenlwlhotmailcom@gmail.com';
    $username = $data['username'];

    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subject = "New Order Alert !";

        // Prepare email body content
        $emailbody = "
        <html>
            <head>
                <style>
                    .email-header { font-family: Arial, sans-serif; font-size: 16px; color: #333; margin-bottom: 20px; }
                    .email-body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
                    .email-footer { font-family: Arial, sans-serif; font-size: 12px; color: #888; margin-top: 30px; }
                    .email-button { background-color: #f5f5f5; color: white; padding: 10px 20px; text-decoration: none; font-size: 16px; margin-top: 20px; display: inline-block; border-radius: 5px; }
                </style>
            </head>
            <body>
                <div class='email-header'>
                    Dear Rimberio Cafe Admin,
                </div>
                <div class='email-body'>
                    New order has been placed by customer - <strong> $username</strong>. Please check the order dashboard for more details.
                <br><br>
                    Below is the link to view the order.
                <br><br>
                    <a href='http://localhost/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/admin/orderDashboard.php' class='email-button'>View Order</a>
                </div>
                <div class='email-footer'>
                    Best regards, <br>
                    $username
                </div>
            </body>
        </html>";

        // Set up PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0;  // Disable verbose debug output for production
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = "stephenlwlhotmailcom@gmail.com";  // Gmail account
            $mail->Password = 'xyjkgynvtyncnths'; // Gmail app password (use env variables for security)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587;  // TCP port to connect to

            // Recipients
            $mail->setFrom('stephenlwlhotmailcom@gmail.com', 'Rimberio Cafe');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $emailbody;

            $mail->send();
            $response = ['status' => 'success', 'message' => 'New Order email sent successfully.'];
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
