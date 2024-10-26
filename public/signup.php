<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/publicDefault.css">
    <script src="js/signup.js"></script>
</head>
<body>

<?php 
// require_once 'C:/xampp/htdocs/RimberioCafeWebsite/5010CEM-EnterpriseProject/vendor/autoload.php';

$IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/inc/";
include($IPATH."nav.php"); 
?>


    <main class="d-flex">
        <section class="left-section d-none d-lg-flex flex-column justify-content-center align-items-center text-center bg-dark text-white p-5">
            <div class="hero-text">
                <h1>Welcome to Our Platform!</h1>
                <p>Log in to access your account and explore.</p>
            </div>
        </section>
        
        <section class="right-section d-flex flex-column justify-content-center align-items-center bg-white">
            <div class="login-form mt-5">
                <p class="text-right">Already have an account? <a href="login.php">Login</a></p>
                <h2 class="text-center mb-4">Signup</h2>
                <form id="signup_form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input id="username" name="username" type="text" class="form-control" placeholder="Enter username" required>
                        <div class="form-text">*Username must be at least 8 characters long and no special character.</div>
                        <div id="username-error" class="mt-2"></div>                            
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" placeholder="...@gmail.com" required>
                        <div id="email-error" class="mt-2"></div> 
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Enter password" required>
                        <div class="form-check form-switch">                                
                            <input class="form-check-input" id="flexSwitchCheckChecked" type="checkbox" onclick="showPassword()"><small>Show Password</small>
                        </div>
                        <div class="form-text">*Password must be at least 8 characters long with at least 1 number and 1 special character.</div>
                        <div id="password-error" class="mt-2"></div>  
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input id="confirm_password" name="confirm_password" type="password" class="form-control" placeholder="Enter confirm password" required>
                        <div id="confirm-password-error" class="mt-2"></div>
                        <div class="form-check form-switch">    
                            <input class="form-check-input" id="flexSwitchCheckChecked" type="checkbox" onclick="showConfirmPassword()"><small>Show Confirm Password</small>
                        </div>
                    </div>
                    <button type="submit" name="send" class="btn btn-success">Verify Email</button>           
                </form>
            </div>
        </section>
    </main>
    <!-- footer -->
    <?php include($IPATH."footer.html"); ?>

    <!-- Email Verification Modal -->
    <div class="modal fade" id="emailVerificationModal" tabindex="-1" role="dialog" aria-labelledby="emailVerificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailVerificationModalLabel">Email Verification</h5>
                </div>
                <div class="modal-body">
                    <p>An OTP has been sent to your email. Please enter the OTP below to verify your email address.</p>
                    <div class="d-flex justify-content-center">
                        <!-- Four input boxes for OTP -->
                        <input type="text" id="otp1" class="form-control otp-input" maxlength="1" autocomplete="off" required>
                        <input type="text" id="otp2" class="form-control otp-input" maxlength="1" autocomplete="off" required>
                        <input type="text" id="otp3" class="form-control otp-input" maxlength="1" autocomplete="off" required>
                        <input type="text" id="otp4" class="form-control otp-input" maxlength="1" autocomplete="off" required>
                    </div>
                    <div id="otp-timer" style="font-weight: bold; color: red;"></div>
                    <div id="otp-error" class="mt-2 text-danger"></div>
                    <div id="validation-error" class="text-start text-danger error-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="verifyOTP()">Verify OTP</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
