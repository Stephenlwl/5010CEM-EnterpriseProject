document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', function (e) {
        // check if the clicked element is an order detail link
        if (e.target && e.target.matches('#viewOrderDetailsLink')) {
            const orderId = e.target.getAttribute('data-order-id');
            console.log("Fetching details for Order ID:", orderId); 
            // get order details
            fetchOrderDetails(orderId);
        }
    });

    // handle order id search form submission
    document.getElementById('searchOrderButton').addEventListener('click', function() {
        const orderIdInput = document.getElementById('orderIdInput');
        const orderIdValue = orderIdInput.value.trim();
    
        const isValid = /^[\d, ]+$/.test(orderIdValue); //check for order id format
    
        if (isValid) {
            const orderIds = orderIdValue.split(',').map(id => id.trim()); // Split and trim the Order IDs
            filterOrderId(orderIds);
        } else {
            alert("Please enter valid Order IDs in the correct format.");
        }
    });

    const receiveMethodFilter = document.getElementById('receiveMethodFilter');

    receiveMethodFilter.addEventListener('change', filterReceiveMethod);

    function filterReceiveMethod() {
        const selectedReceiveMethod = receiveMethodFilter.value;

        const orders = document.querySelectorAll('.order'); 

        orders.forEach(order => {
            const receiveMethod = order.getAttribute('receiveMethod');

            // Check if order matches the selected filters
            const matchesMethod = (selectedReceiveMethod === 'All' || receiveMethod === selectedReceiveMethod);

            if (matchesMethod) {
                order.style.display = 'block'; // show order
            } else {
                order.style.display = 'none'; // hide order
            }
        });
    }

    // Function to filter based on Order ID
    function filterOrderId(orderIds) {
        const orders = document.querySelectorAll('.order');

        orders.forEach(order => {
            const orderId = order.getAttribute('data-order-id'); 
            const matchesOrderId = orderIds.includes(orderId);

            if (matchesOrderId) {
                order.style.display = 'block'; // Show matching orders
            } else {
                order.style.display = 'none'; // Hide non-matching orders
            }
        });
    }

    function fetchOrderDetails(orderId) {
        fetch(`../auth/api/view_order_details.php`, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ orderId: orderId }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const orderDetailsBody = document.getElementById('order-details-body');
            orderDetailsBody.innerHTML = '';

            if (!data.orderDetails || data.orderDetails.length === 0) {
                orderDetailsBody.innerHTML = '<p>No order details found.</p>';
            } else {
                data.orderDetails.forEach(item => {
                    let itemDetails = `
                        <div class="row align-items-center order-item mb-3">
                            <div class="col-8 col-sm-8">
                                <strong>${item.ItemName}</strong>
                            </div>
                            <div class="col-4 col-sm-4 text-start">
                                <strong>${item.ItemQuantity}</strong>
                            </div>
                        </div>
                        <hr>
                    `;

                    if (item.PersonalItemID !== null) {
                        itemDetails = `
                            <div class="row align-items-center order-item mb-3">
                                <div class="col-8 col-sm-8">
                                    <strong>${item.ItemName}</strong>
                                    <br>
                                    <small class="text-muted">
                                        ${item.Temperature || 'Default'} 
                                        ${item.MilkType ? '| ' + item.MilkType : ''} 
                                        ${item.CoffeeBeanType ? '| ' + item.CoffeeBeanType : ''} 
                                        ${item.Sweetness ? '| ' + item.Sweetness : ''} 
                                        ${item.AddShot ? '| ' + item.AddShot : ''}
                                    </small>
                                </div>
                                <div class="col-4 col-sm-4 text-start">
                                    <strong>${item.ItemQuantity}</strong>
                                </div>
                            </div>
                            <hr>
                        `;
                    }
                    orderDetailsBody.innerHTML += itemDetails;
                });
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            const orderDetailsBody = document.getElementById('order-details-body');
            orderDetailsBody.innerHTML = '<p>Error fetching order details. Please try again later.</p>';
        });
    }

});