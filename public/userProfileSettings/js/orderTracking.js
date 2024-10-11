document.addEventListener('DOMContentLoaded', () => {
    // get current order status from the hidden input value
    const currentStatus = document.getElementById('currentStatus').value;

    // get all status items
    const statusItems = document.querySelectorAll('.status-item');

    // highlight the corresponding status
    statusItems.forEach(item => {
        if (item.getAttribute('data-status') === currentStatus) {
            item.classList.add('current-status-highlight');
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const viewOrderDetailsLink = document.getElementById('viewOrderDetailsLink');

    viewOrderDetailsLink.addEventListener('click', function () {
        const orderId = this.getAttribute('data-order-id');

        fetch(`../auth/api/view_order_details.php`, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ orderId: orderId }), // Send the orderId in the body
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const orderDetailsBody = document.getElementById('order-details-body');
            orderDetailsBody.innerHTML = ''; // Clear previous details
        
            if (data.orderDetails.length === 0) {
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
                                        ${item.AddShot ? '| Add Shot' : ''}
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
    });
});
