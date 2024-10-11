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
                    window.location.reload();
                    
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
});