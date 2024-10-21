document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('contactForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const spinner = document.createElement('span');
        const submitButton = contactForm.querySelector('button[type="submit"]');

        // setting up spinner
        spinner.className = 'spinner-border spinner-border-sm me-2';
        spinner.setAttribute('role', 'status');
        spinner.setAttribute('aria-hidden', 'true');
        spinner.style.display = 'none'; 
        submitButton.prepend(spinner);

        data = {
            firstName:document.getElementById('first-name').value,
            lastName: document.getElementById('last-name').value,
            email: document.getElementById('email').value,
            message: document.getElementById('message').value
        }

        spinner.style.display = 'inline-block';
            submitButton.innerHTML = 'Sending...';
            submitButton.disabled = true;
            submitButton.prepend(spinner);

        fetch('auth/mail_handler/sendContactEmail.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Email API response:', data); 
            if (data.status !== 'success') {
                alert('Failed to send email: ' + data.message);
            } else {
                console.log('Email sent successfully'); 
                alert('Email sent successfully');
                spinner.style.display = 'none';
                submitButton.innerHTML = 'Send Message';
                submitButton.disabled = false;
                document.getElementById('contactForm').reset();
            }
        })
        .catch(error => {
            console.error('Error sending email:', error);
            alert('An error occurred while sending the email notification.');
        });
    });
});