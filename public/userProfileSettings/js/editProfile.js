document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('editProfileForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value;
        const current_password = document.getElementById("current_password").value;
        const new_password = document.getElementById("new_password").value;

        // Perform AJAX request to submit form data
        fetch('../auth/objects/profile.php', {
            method: "POST",
            body: username,current_password,new_password
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Handle success message or redirection
                alert(data.message);
                location.reload();
            } else {
                // Handle error message
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request. Please try again.');
        });
    });
});