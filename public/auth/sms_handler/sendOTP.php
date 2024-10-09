<?php
session_start();

// Authorisation details.
$username = "p22013985@student.newinti.edu.my";
$hash = "ff326cfdea8922dcd71623bc3a56fcf99eae61c92ed5782c12ccc03f1177c7b4";

$otp_code = rand(1000, 9999);
$_SESSION['otp_code'] = $otp_code;

// Config variables
$test = "0";

// Get the phone number from the request body
$data = json_decode(file_get_contents('php://input'), true);
// $numbers = isset($data['phone_no']) ? $data['phone_no'] : null;
$numbers = "601120977422";

if (!$numbers) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number is required.']);
    exit();
}

// Data for text message
$sender = "Rimberio Cafe"; // Sender name
$message = "Your One-Time Password (OTP) for phone number verification is: $otp_code. If it is not you, please ignore this message.";
$message = urlencode($message);

$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
$ch = curl_init('https://api.txtlocal.com/send/?');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch); // This is the result from the API
curl_close($ch);

// Check the response from the API (assuming you have it in JSON format)
$response = json_decode($result, true);

if (isset($response['status']) && $response['status'] === 'success') {
    echo json_encode(['status' => 'success', 'otp_code' => $otp_code]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP.']);
}
?>
