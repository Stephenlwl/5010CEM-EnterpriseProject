let otp_code;
let otpTimeoutDuration = 30; // set 30 seconds for OTP expiration
let otpExpired = false;

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
                startOtpCountdown(otp_code); // start the countdown timer
                
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


document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
    input.addEventListener('input', () => {
        // Move to the next input if current input is filled
        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        
        // Move to the previous input if the current input is empty and it's not the first input
        if (input.value.length === 0 && index > 0) {
            inputs[index - 1].focus();
        }
    });

    // Optional: Automatically select the input when focused
    input.addEventListener('focus', () => {
        input.select(); // Select the current input's content if needed
    });
});

function startOtpCountdown(otp_code) {
    const otpTimerElement = document.getElementById('otp-timer');
    let remainingTime = otpTimeoutDuration;

    const countdownInterval = setInterval(() => {
        remainingTime--;
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        otpTimerElement.textContent = `Time remaining: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

        if (remainingTime <= 0) {
            clearInterval(countdownInterval);
            otpExpired = true;  // mark the OTP as expired
            otp_code = null;  // invalidate the OTP code
            otpTimerElement.textContent = "OTP expired. Please request a new one.";
            alert("Your OTP has expired. Please verify your email again.");
        } else {
            otpExpired = false;
            otp_code = otp_code;  // keep the OTP code
        }
    }, 1000); // for updating the countdown every second
}

function verifyOTP() {
    let otp_inp = '';

    if (otpExpired) {
        alert("Your OTP has expired. Please request a new OTP.");
        return;  // Stop further execution
    }

    for (let i = 1; i <= 4; i++) {
        otp_inp += document.getElementById('otp' + i).value;
    }

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
                    window.location.href = "login.php";  
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
