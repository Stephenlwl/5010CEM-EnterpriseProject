document.addEventListener("DOMContentLoaded", function() {
    const orderStatusFilter = document.getElementById('orderStatusFilter');
    const receiveMethodFilter = document.getElementById('receiveMethodFilter');

    orderStatusFilter.addEventListener('change', applyFilters);
    receiveMethodFilter.addEventListener('change', applyFilters);

    function applyFilters() {
        const selectedOrderStatus = orderStatusFilter.value;
        const selectedReceiveMethod = receiveMethodFilter.value;

        const orders = document.querySelectorAll('.order'); 

        orders.forEach(order => {
            const orderStatus = order.getAttribute('orderStatus');
            const receiveMethod = order.getAttribute('receiveMethod');

            // Check if order matches the selected filters
            const matchesStatus = (selectedOrderStatus === 'All' || orderStatus === selectedOrderStatus);
            const matchesMethod = (selectedReceiveMethod === 'All' || receiveMethod === selectedReceiveMethod);

            if (matchesStatus && matchesMethod) {
                order.style.display = 'block'; // show order
            } else {
                order.style.display = 'none'; // hide order
            }
        });
    }
});



document.addEventListener("DOMContentLoaded", function() {
    // Attach event listeners to each "Update Status" button
    document.querySelectorAll('.update-status').forEach(function(updateButton) {
        updateButton.addEventListener('click', function() {
            // Retrieve the associated order ID from the button's data attribute
            const orderId = this.getAttribute('data-order-id');
            
            // Find the dropdown related to this order and get the selected status
            const statusDropdown = document.querySelector(`.orderStatus[data-order-id="${orderId}"]`);
            const newStatus = statusDropdown.value;

            // Perform an AJAX request to update the status in the database
            fetch('../auth/api/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    orderId: orderId,
                    newStatus: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order status updated successfully!');
                    sendNotificationEmail(orderId, newStatus);
                } else {
                    alert('Failed to update order status.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the order status.');
            });
        });
    });

    function sendNotificationEmail(orderId, status) {
        const updateButton = document.querySelector(`.update-status[data-order-id="${orderId}"]`);

        // retrieve user details from the button's data attributes
        const username = updateButton.getAttribute('data-user-name');
        const email = updateButton.getAttribute('data-user-email');
        const spinner = document.createElement('span');

        // setting up spinner
        spinner.className = 'spinner-border spinner-border-sm me-2';
        spinner.setAttribute('role', 'status');
        spinner.setAttribute('aria-hidden', 'true');
        spinner.style.display = 'none'; 
        updateButton.prepend(spinner);
        
        let emailEndpoint = ''; // determine the email endpoint based on the status

        if (status === 'Out for Delivery') {
            spinner.style.display = 'inline-block';
            updateButton.innerHTML = 'Sending...';
            updateButton.disabled = true;
            updateButton.prepend(spinner);
            emailEndpoint = '../auth/mail_handler/sendDeliveryNotification.php';
        } else if (status === 'Ready to Pickup') {
            spinner.style.display = 'inline-block';
            updateButton.innerHTML = 'Sending...';
            updateButton.disabled = true;
            updateButton.prepend(spinner);
            emailEndpoint = '../auth/mail_handler/sendReadyPickupNotification.php';
        } else {
            window.location.reload();
            return; 
        }

        // send the email notification
        fetch(emailEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username: username, email: email })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.status == 'success') {
                alert(`Email notification (${status}) sent successfully.`);
                window.location.reload();
            } else {
                alert('Failed to send email: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error sending email:', error);
            alert('An error occurred while sending the email notification.');
        }).finally(() => {
            spinner.style.display = 'none';
            updateButton.innerHTML = 'Update Status';
            updateButton.disabled = false;
        });
    }
});