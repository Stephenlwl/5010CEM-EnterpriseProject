function updateQuantity(action, itemID, userId, cartID) {
    // get the current quantity from the input field
    let quantityInput = document.getElementById('quantity_' + itemID);
    let currentQuantity = parseInt(quantityInput.value);

    // adjust quantity based on the action (plus or minus)
    if (action === 'plus') {
        currentQuantity++; 
        if (currentQuantity > 10) {
            alert('You cannot order more than 10 items at once!');
            return;
        }
    } else if (action === 'minus' && currentQuantity > 1) {
        currentQuantity--;  // ensure and prevent quantity go below 1
        if (currentQuantity < 1) {
            alert('You cannot order less than 1 item!');
            return;
        }
    }

    // update the input value
    quantityInput.value = currentQuantity;

    let data = {
        itemID: itemID,
        quantity: currentQuantity,
        userID: userId,
        cartID: cartID
    };

    fetch('auth/api/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json()) 
    .then(result => {
        if (result.status === 'success') {
            console.log('Cart updated successfully:', result.message);
            window.location.reload();
        } else {
            console.log('Error updating cart:', result.message);
            alert('Failed to update cart. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the cart.');
    });

}

function removeItem(itemID, userId, cartID) {
    let data = {
        itemID: itemID,
        userID: userId,
        cartID: cartID
    };

    fetch('auth/api/remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data) 
    })
    .then(response => response.json()) 
    .then(result => {
        if (result.status === 'success') {
            alert('Item removed successfully!');
            console.log('Item removed successfully:', result.message);
            window.location.reload();
        } else {
            console.log('Error removing item:', result.message);
            alert('Failed to remove item. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the item.');
    });
}