document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const captchaResponse = grecaptcha.getResponse();

        if (!captchaResponse.length > 0) {
            alert("The Captcha not complete, Please complete it before proceed!");
            throw new Error("Captcha not complete, Please complete it before proceed!");
            return;
        }

        const fd = new FormData(e.target);
        const params = new URLSearchParams(fd);

        fetch('http://httpbin.org/post', {
            method: "POST",
            body: params,
        })
                .then(res => res.json())
                .then(data => console.log(data))
                .catch(err => console.error(err))
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

function showPasswordAdmin() {
    var adminPasswd = document.getElementById("admin_password");

    if (adminPasswd.type === "password") {
        adminPasswd.type = "text";
    } else {
        adminPasswd.type = "password";
    }
}

function userLogin(event) {
    event.preventDefault(); 

    // Get the email and password from the form inputs
    const email = document.getElementById("email").value; 
    const password = document.getElementById("password").value;
    const captchaResponse = grecaptcha.getResponse();

    document.getElementById("error-message").style.color = "red";

    // Check for empty email or password
    if (email === "" || email == null) {
        document.getElementById("error-message").innerText = "Please don't leave the email empty.";
        return;
    }

    if (password === "" || password == null) {
        document.getElementById("error-message").innerText = "Please don't leave the password empty.";
        return;
    }

    if (captchaResponse.length === 0) {
        document.getElementById("error-message").innerText = "Please complete the Captcha!";
        return;
    }

    // Prepare the data to send
    const loginData = {email: email, password: password};

    fetch('auth/objects/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(loginData)
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    }).then(data => {
        if (data.success) {
            alert("Login Successfully! Welcome back " + data.username + " !");
            window.location.href = "profile.php";
        } else {
            document.getElementById("error-message").innerText = data.message;
        }
    }).catch(error => {
        console.error('Error:', error);
        document.getElementById("error-message").innerText = "An error occurred. Please try again.";
    });
}

function adminLogin(event) {
    event.preventDefault();  // Prevent the form from submitting the traditional way

    // Get the username and password from the form inputs
    const admin_username = document.getElementById("admin_username").value;
    const admin_password = document.getElementById("admin_password").value;
    const captchaResponse = grecaptcha.getResponse();

    document.getElementById("error-message").style.color = "red";

    // Check for empty username or password
    if (admin_username === "" || admin_username == null) {
        document.getElementById("error-message").innerText = "Please don't leave the username empty.";
        return;
    }

    if (admin_password === "" || admin_password == null) {
        document.getElementById("error-message").innerText = "Please don't leave the password empty.";
        return;
    }

    if (captchaResponse.length === 0) {
        document.getElementById("error-message").innerText = "Please complete the Captcha!";
        return;
    }

    // Prepare the data to send
    const adminLoginData = {admin_username: admin_username, admin_password: admin_password};

    fetch('auth/objects/admin_login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(adminLoginData)
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    }).then(data => {
        if (data.success) {
            alert("Login Successfully! Welcome back " + admin_username + "!");
            window.location.href = "sap_management.php";
        } else {
            document.getElementById("error-message").innerText = data.message;
        }
    }).catch(error => {
        console.error('Error:', error);
        document.getElementById("error-message").innerText = "An error occurred. Please try again.";
    });
}
