let otp_code;

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("signup_form");
    const verifyButton = form.querySelector('button[type="submit"]');
    const spinner = document.createElement('span');

    // For Setting up the spinner - to prevent the user click multiple times for the submit button when the action is running
    spinner.className = 'spinner-border spinner-border-sm me-2';
    spinner.setAttribute('role', 'status');
    spinner.setAttribute('aria-hidden', 'true');
    spinner.style.display = 'none'; // Hide spinner initially
    verifyButton.prepend(spinner);

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!accountValidation()) {
            return;
        }

        const username = form.querySelector('[name="username"]').value;
        const email = form.querySelector('[name="email"]').value;
        const password = form.querySelector('[name="password"]').value;

        spinner.style.display = 'inline-block';
        verifyButton.innerHTML = 'Sending...';
        verifyButton.disabled = true;
        verifyButton.prepend(spinner);

        fetch('./auth/mail_handler/sendOTP.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({username: username, email: email, password: password, send: true})
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        }).then(data => {
            if (data.status == 'success') {
                alert("OTP sent successfully.");
                console.log("OTP sent successfully.");
                otp_code = data.otp_code;
                $('#emailVerificationModal').modal('show');
            } else {
                alert(data.message);
            }
        }).catch(error =>
            console.error('Error:', error))
                .finally(() => {
                    spinner.style.display = 'none';
                    verifyButton.innerHTML = 'Verify Email';
                    verifyButton.disabled = false;
                });
    });
});

function showPassword() {
    var passwd = document.getElementById("password");

    if (passwd.type === "password") {
        passwd.type = "text";
    } else {
        passwd.type = "password";
    }
}

function showConfirmPassword() {
    var confirmPasswd = document.getElementById("confirm_password");

    if (confirmPasswd.type === "password") {
        confirmPasswd.type = "text";
    } else {
        confirmPasswd.type = "password";
    }
}

function clearErrors() {
// Clear all error messages at the Initial
    document.getElementById("username-error").innerText = "";
    document.getElementById("password-error").innerText = "";
    document.getElementById("confirm-password-error").innerText = "";
}

function setError(elementId, message) {
// Set error message and style for a specific element
    const element = document.getElementById(elementId);
    element.innerText = message;
    element.style.color = "red";
}

function validateUsername(username) {
    const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/;
    if (username.length < 8) {
        return "Username must be at least 8 characters long.";
    } else if (username.length > 20) {
        return "Username is too long. It must be less than or equal to 20 characters.";
    } else if (hasSymbol.test(username)) {
        return "Username can't contain any special character.";
    }
    return "";
}

function validatePassword(password) {
    let errors = [];
    const hasNumber = /\d/;
    const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/;
    if (password.length < 8) {
        errors.push("Password must be at least 8 characters long.");
    } else if (password.length > 20) {
        errors.push("Password is too long. It must be less than or equal to 20 characters.");
    }

    if (!hasNumber.test(password)) {
        errors.push("Password must contain at least one number.");
    }
    if (!hasSymbol.test(password)) {
        errors.push("Password must contain at least one special character.");
    }

    return errors.join("\n");
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let isValidEmail = emailRegex.test(email);

    if (isValidEmail) {
        console.log("Valid email format");
    } else {
        console.log("Invalid email format");
        return "Invalid email format";
    }
}

function accountValidation() {

    // Get input values
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const confirm_password = document.getElementById("confirm_password").value;
    const email = document.getElementById("email").value;

    // Clear previous errors at the initial stage
    clearErrors();
    // Validate username and password
    const usernameError = validateUsername(username);
    const passwordError = validatePassword(password);
    const emailError = validateEmail(email);

    // if got error set and display the error
    if (usernameError) {
        setError("username-error", usernameError);
    }

    if (passwordError) {
        setError("password-error", passwordError);
    }

    if (password !== confirm_password) {
        setError("confirm-password-error", "Password and Confirm Password do not match!");
    }

    if (emailError) {
        setError("email-error", emailError);
    }

    // If no errors return true
    return !usernameError && !passwordError && !emailError && password === confirm_password;
}

function verifyOTP() {

    const otp_inp = document.getElementById("otp").value;

    if (otp_inp == otp_code) {
        const formElement = document.getElementById('signup_form');
        const formData = new FormData(formElement);
        // Convert formData to JSON
        const formObject = {};
        formData.forEach((value, key) => formObject[key] = value);
        const formJSON = JSON.stringify(formObject);

        fetch('auth/objects/signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: formJSON
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        }).then(response => {
            if (response.success) {
                if (response.redirect) {
                    alert("Register Successfully, Your email was verified!")
                    window.location.href = "profile.php";  // Redirect to profile page
                } else {
                    $('#emailVerificationModal').modal('show');
                }
            } else {
                if (response.errors.validation) {
                    $('#validation-error').text(response.errors.validation);
                    alert(response.errors.validation);
                }
                if (response.errors.username) {
                    $('#username-error').text(response.errors.username);
                }
                if (response.errors.email) {
                    $('#email-error').text(response.errors.email);
                }
                if (response.errors.password) {
                    $('#password-error').text(response.errors.password);
                }
                if (response.errors.confirm_password) {
                    $('#confirm-password-error').text(response.errors.confirm_password);
                }
            }
        }).catch(error => {
            alert("Error: " + error);  // Handle AJAX errors
            console.error('Error:', error);
        });
    } else {
        alert("Invalid OTP");
    }
}
