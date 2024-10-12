<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/adminLogin.css">
    <script src="js/adminLogin.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/RimberioCafeWebsite/5010CEM-EnterpriseProject/public/admin/inc/";?>

    <main class="d-flex">
        <!-- Left section with background image and gradient -->
        <section class="left-section d-none d-lg-flex flex-column justify-content-center align-items-center text-center p-5 w-50">
            <div class="hero-text">
                <h1>Welcome to Admin Dashboard!</h1>
            </div>
        </section>

        <!-- Right section for the login form -->
        <section class="right-section d-flex flex-column justify-content-center align-items-center p-5 bg-white w-50">
            <div class="login-form w-100" style="max-width: 400px;">
                <h2 class="text-center mb-4">Admin Login</h2>

                <form id="login-form" onsubmit="adminLogin(event)">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" id="flexSwitchCheckChecked" type="checkbox" onclick="showAdminPassword()">
                        <small class="form-check-label">Show Password</small>
                    </div>
                    <br>
                    <div class="g-recaptcha" data-sitekey="6LeO394pAAAAAIfJUVp5Z0c_cOdR-xVFsEn_mDYD"></div>
                    <br>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <div id="error-message" class="mt-2 text-danger"></div>
                </form>

            </div>
        </section>
    </main>
    <!-- footer -->
    <?php include($IPATH."footer.html"); ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
