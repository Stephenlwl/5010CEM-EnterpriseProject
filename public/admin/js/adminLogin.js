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


function showAdminPassword() {
    var adminPasswd = document.getElementById("password");

    if (adminPasswd.type === "password") {
        adminPasswd.type = "text";
    } else {
        adminPasswd.type = "password";
    }
}


function adminLogin(event) {
    event.preventDefault();  // Prevent the form from submitting the traditional way

    // Get the email and password from the form inputs
    const admin_email = document.getElementById("email").value;
    const admin_password = document.getElementById("password").value;
    const captchaResponse = grecaptcha.getResponse();

    document.getElementById("error-message").style.color = "red";

    // Check for empty email or password
    if (admin_email === "" || admin_email == null) {
        document.getElementById("error-message").innerText = "Please don't leave the email empty.";
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
    const adminLoginData = {admin_email: admin_email, admin_password: admin_password};

    fetch('../auth/objects/admin_login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(adminLoginData)
    }).then(response => {
        return response.text(); // Change to text() to log raw response
    }).then(data => {
        console.log(data); // Log the raw response to identify the error
        let jsonData = JSON.parse(data); // Parse the response after logging it
        if (jsonData.success) {
            alert("Login Successfully! Welcome back " + jsonData.admin_username + "!");
            window.location.href = "orderDashboard.php";
        } else {
            document.getElementById("error-message").innerText = jsonData.message;
        }
    }).catch(error => {
        console.error('Error:', error);
        document.getElementById("error-message").innerText = "An error occurred. Please try again.";
    });
    
}