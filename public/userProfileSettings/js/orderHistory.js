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
});

function fetchOrderDetails(orderId) {
    fetch(`../auth/api/viewOrderDetails.php`, {
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
}
