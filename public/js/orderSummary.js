document.addEventListener('DOMContentLoaded', function () {
    const paymentOptions = document.getElementsByName('paymentOption');
    const paypalContainer = document.getElementById('paypal-button-container');
    const userId = document.getElementById('userId').value;
    let paymentOption = document.querySelector('input[name="paymentOption"]:checked').value;  
    const addressOptions = document.getElementById('addressOptions');
    const addressSection = document.getElementById('addressSection');
    const storeAddressOptions = document.getElementById('storeAddressOptions');
    const storeAddressSection = document.getElementById('storeAddressSection');
    const deliveryHomeRadio = document.getElementById('deliveryHome');
    const deliveryPickupRadio = document.getElementById('deliveryPickup');
    const currency = 'MYR';
    let deliveryMethod = 'Delivery';
    
    const promoButtons = document.querySelectorAll('.promo-button'); 

    promoButtons.forEach(button => {
        button.addEventListener('change', function() {
            const promoCode = this.getAttribute('value');
            const discountValue = parseFloat(this.getAttribute('data-discount'));
            const discountType = this.getAttribute('data-discounttype');

            // update the displayed total based on the selected promo code
            applyPromoCode(promoCode, discountValue, discountType);
        });
    });

    function applyPromoCode(promoCode, discountValue, discountType) {
        let discountedTotal = totalAmount; // set default discounted total to current total
        
        // calculate the discounted total based on promo type
        if (discountType === 'percentage') {
            discountValue = (totalAmount * discountValue / 100)
            discountedTotal = totalAmount - discountValue;
        } else if (discountType === 'fixed') {
            discountedTotal = totalAmount - discountValue;
        }

        // update the final total displayed
        document.getElementById('final-total').innerText = `RM ${discountedTotal.toFixed(2)}`;

        // update the discount amount section
        const discountSection = document.getElementById('discount-section');
        const discountAmount = document.getElementById('discountAmount');

        // show discount amount with the correct format and discount amount
        discountAmount.innerText = `RM ${discountValue.toFixed(2)}`;
        discountSection.style.display = 'block';

        document.getElementById('promoCodeApplied').value = promoCode;
        document.getElementById('discountedTotal').value = discountedTotal.toFixed(2);
        document.getElementById('discountAmount').value = discountValue.toFixed(2);
    }

    // Show address at initial load becasue home delivery is selected by default
    fetchUserAddresses();

    function handleDeliveryMethodChange() {
        if (deliveryHomeRadio.checked) {
            addressSection.style.display = 'block'; // show address
            storeAddressSection.style.display = 'none'; // hide store address
            deliveryMethod = 'Delivery';
            fetchUserAddresses(); 
        } else if (deliveryPickupRadio.checked) {
            addressSection.style.display = 'none'; // hide address
            storeAddressSection.style.display = 'block'; // show store address
            deliveryMethod = 'Pickup';
            fetchStoreAddresses();
        }
    }
    
    // show user address based on radio button listener
    deliveryHomeRadio.addEventListener('change', handleDeliveryMethodChange);
    deliveryPickupRadio.addEventListener('change', handleDeliveryMethodChange);

   // Submit event listener for add address form
   var addAddressForm = document.getElementById('addAddressForm');
   if (addAddressForm) {
       addAddressForm.addEventListener('submit', function (e) {
           e.preventDefault();

           // Retrieve form data
           const addressData = new FormData(this);

           // Send new address to the server
           fetch('auth/api/add_address.php', {
               method: 'POST',
               body: addressData,
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   alert('Address added successfully!');
                   location.reload(); 
               } else {
                   alert('Error adding address: ' + data.message);
               }
           })
           .catch(error => {
               console.error('Error:', error);
           });
       });
   }
    
    // fetch user addresses
    function fetchUserAddresses() {
        fetch('auth/api/get_user_address.php') 
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addressOptions.innerHTML = ''; // clear previous options
                    data.data.forEach(address => {
                        const addressDiv = document.createElement('div');
                        addressDiv.classList.add('form-check');
                        addressDiv.innerHTML = `
                            <input class="form-check-input" type="radio" name="addressId" id="address_${address.AddressID}" value="${address.AddressID}">
                            <label class="form-check-label" for="address_${address.AddressID}">
                                ${address.AddressName}, ${address.Address1}, ${address.State}, ${address.PostalCode}
                            </label>
                        `;
                        addressOptions.appendChild(addressDiv);
                    });
                } else {
                    addressOptions.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching addresses:', error);
            });
    }

    // fetch store addresses
    function fetchStoreAddresses() {
        fetch('auth/api/get_store_address.php') 
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    storeAddressOptions.innerHTML = ''; // clear previous options
                    data.data.forEach(address => {
                        const addressDiv = document.createElement('div');
                        addressDiv.classList.add('form-check');
                        addressDiv.innerHTML = `
                            <input class="form-check-input" type="radio" name="addressId" id="address_${address.AddressID}" value="${address.AddressID}">
                            <label class="form-check-label" for="address_${address.AddressID}">
                                ${address.AddressName}, ${address.Address1}, ${address.State}, ${address.PostalCode}
                            </label>
                        `;
                        storeAddressOptions.appendChild(addressDiv);
                    });
                } else {
                    storeAddressOptions.innerHTML = `<p>${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching addresses:', error);
            });
    }

    function getSelectedAddressId() {
        if (deliveryHomeRadio.checked) {
            let selectedAddressRadio = addressOptions.querySelector('input[name="addressId"]:checked');
            if (selectedAddressRadio) {
                return selectedAddressRadio.value; // get AddressID for delivery
            }
        } else if (deliveryPickupRadio.checked) {
            let selectedAddressRadio = storeAddressOptions.querySelector('input[name="addressId"]:checked');
            if (selectedAddressRadio) {
                return selectedAddressRadio.value; // get AddressID for pickup
            }
        }
        return null; // return null if no address is selected
    }

    function togglePaypalButton() {
        if (document.getElementById('paymentPaypal').checked) {
            paypalContainer.style.display = 'block';
        } else {
            paypalContainer.style.display = 'none';
        }
    }

    paymentOptions.forEach(method => {
        method.addEventListener('change', function() {
            paymentOption = this.value;
            togglePaypalButton();
        });
    });

    paypal.Buttons({
        createOrder: function (data, actions) {
            const addressId = getSelectedAddressId(); 

            if (!addressId || addressId === 'null') {
                // returning a rejected promise to prevent the payment process from starting
                return Promise.reject(new Error('Please select an address before proceeding.'));
            }

            const discountedTotal = document.getElementById('discountedTotal').value;
    
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        currency_code: currency,
                        value: discountedTotal
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                const transactionId = details.id;
                const discountedTotal = document.getElementById('discountedTotal').value;
                const discountAmount = document.getElementById('discountAmount').value;
                const addressId = getSelectedAddressId()
        
                // declare all the receipt data
                const receiptData = {
                    AddressID: addressId,
                    TotalPrice: discountedTotal,
                    PaymentType: paymentOption,
                    ReceiveMethod: deliveryMethod,
                    ReferenceNo: transactionId,
                    DiscountAmount: discountAmount,  // discount value
                    // Get cart items from the cartItems where has been fetched on OrderSummary.php
                    items: cartItems.map(item => ({
                        ItemID: item.ItemID,
                        PersonalItemID: item.PersonalItemID,
                        ItemQuantity: item.Quantity,
                        ItemPrice: item.ItemPrice,
                        TotalPrice: item.ItemPrice * item.Quantity
                    }))
                };
        
                // Send receipt data to backend
                return fetch('auth/api/add_receipt.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(receiptData)
                }).then(response => response.text().then(text => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                    }
                    return JSON.parse(text); 
                }));
            }).then(data => {
                if (data.success) {
                    // open receipt in new tab and redirect to profile page
                    window.open('userProfileSettings/print_receipt.php?receipt_id=' + data.ReceiptID, '_blank');
                    alert('Transaction completed and receipt created with ID: ' + data.ReceiptID);

                    // declare order data
                    const orderData = { UserID: userId, ReceiptID: data.ReceiptID };
                    // add order data to order table
                    return fetch('auth/api/add_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(orderData)
                    }).then(orderResponse => orderResponse.text().then(orderText => {
                        if (!orderResponse.ok) {
                            throw new Error(`HTTP error! status: ${orderResponse.status} - ${orderText}`);
                        }
                        return JSON.parse(orderText);
                    }).then(data => {
                        if (data.success) {
                            let appliedPromoCode = document.getElementById('promoCodeApplied').value;
        
                            // Add the used promo code
                            const promoCodeData = {
                                UserID: userId,
                                PromoCode: appliedPromoCode,
                            };
        
                            return fetch('auth/api/add_used_promo.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(promoCodeData)
                            }).then(promoResponse => promoResponse.text().then(promoText => {
                                console.log('Promo API raw response:', promoText); // Log raw response
                                if (!promoResponse.ok) {
                                    throw new Error(`HTTP error! status: ${promoResponse.status} - ${promoText}`);
                                }
                                return JSON.parse(promoText);
                            }).then(promoData => {
                                if (promoData.success) {
                                    // Handle promo code success logic if necessary
                                    alert('Promo code applied successfully!');
                                } else {
                                    alert('Error applying promo code: ' + promoData.message);
                                }
                            }));
                        } else {
                            alert('Error adding order: ' + data.message);
                        }
                    }));
                } else {
                    // show error message if receipt creation failed
                    alert('Error creating receipt: ' + data.message);
                }
            }).then(() => {
                // remove cart items after the transaction is complete and receipt is created
                return fetch('auth/api/remove_cart_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ userId: userId, status: 'Active'})
                });
            }).then(cartResponse => cartResponse.text().then(cartText => {
                if (!cartResponse.ok) {
                    throw new Error(`HTTP error! status: ${cartResponse.status} - ${cartText}`);
                }
                return JSON.parse(cartText);
            }).then(data => {
                if (data.success) {
                    // navigate to order tracking for user easier to track their order
                    window.location.href = 'profile.php?page=orderTracking';
                } else {
                    alert('Error removing cart items: ' + data.message);
                }
            })).catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request. Please try again later.');
            });
        },
        onCancel: function (data) {
            alert('Payment was cancelled.');
        },
        onError: function (err) {
            console.error('PayPal Checkout error: ', err);
            alert('An error occurred during the payment process: ' + err);
        }
    }).render('#paypal-button-container');
});