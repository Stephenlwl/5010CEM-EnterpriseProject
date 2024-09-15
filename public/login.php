<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/publicDefault.css">
</head>
<body>

    <?php $IPATH = $_SERVER["DOCUMENT_ROOT"]."/public/inc/";
        include($IPATH."nav.html"); 
    ?>

    <main class="d-flex">
        <section class="left-section d-none d-lg-flex flex-column justify-content-center align-items-center text-center bg-dark text-white p-5">
            <div class="hero-text">
                <h1>Welcome to Our Platform!</h1>
                <p>Log in to access your account and explore.</p>
            </div>
        </section>
        
        <section class="right-section d-flex flex-column justify-content-center align-items-center p-5 bg-white">
            <div class="login-form w-100" style="max-width: 400px;">
                <p class="text-right">Don't have an account? <a href="signup.html">Sign up</a></p>
                <h2 class="text-center mb-4">Login</h2>
                <button class="btn btn-outline-dark btn-block mb-3">Continue with Google</button>
                <button class="btn btn-primary btn-block mb-3">Continue with Twitter</button>
                <p class="separator text-center"><span>OR</span></p>

                <form action="#">
                    <div class="form-group">
                        <label for="email">User name</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hide">
                            <label class="form-check-label" for="hide">Hide</label>
                        </div>
                        <a href="#">Forgot your password?</a>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Sign in</button>
                </form>

                <p class="text-center mt-4">Don't have an account? <a href="#">Sign up</a></p>
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
