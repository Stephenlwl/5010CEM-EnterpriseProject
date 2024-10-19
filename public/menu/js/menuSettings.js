let selectedItemId;

function showDetails(itemName, itemPrice, itemID) {
    document.getElementById('modalItemName').innerText = itemName;
    document.getElementById('modalItemPrice').innerText = itemPrice.toFixed(2);
    document.getElementById('modalItemID').value = itemID;
}


function addToCartWithCustomization() {

    const userID = document.getElementById('userId').value;

    if (userID === "" || userID === null || userID === "guest") {
        alert('Please login to add product to your cart!');
        return;
    }

    // Get values from the modal
    const itemID = document.getElementById('modalItemID').value;
    const itemName = document.getElementById('modalItemName').innerText;
    const itemPrice = document.getElementById('modalItemPrice').innerText;
    const temperature = document.getElementById('Temperature').value;
    const sweetness = document.getElementById('Sweetness').value;
    const addShot = document.getElementById('AddShot').value;
    const milkType = document.getElementById('MilkType').value;
    const coffeeBean = document.getElementById('CoffeeBean').value;
    const quantity = document.getElementById('Quantity').value;
    const currentItemStockQuantity = document.getElementById('item-stock-quantity-' + itemID).value;
    let currentItemQuantityInCart = document.getElementById('item-quantity-in-cart-' + itemID)?.value || 0;

    let totalQuantity = parseInt(quantity) +parseInt(currentItemQuantityInCart);

    if (quantity <= 0) {
        alert('Please enter a valid quantity!');
        return;
    }

    if (totalQuantity > currentItemStockQuantity) {
        alert('No more stock available for this item!');
        return;
    }

    // Create data object to send
    const cartData = {
        itemID: itemID,
        itemName: itemName,
        itemPrice: parseFloat(itemPrice),
        temperature: temperature,
        sweetness: sweetness,
        addShot: addShot,
        milkType: milkType,
        coffeeBean: coffeeBean,
        quantity: parseInt(quantity),
        userID: userID,
        personalItemID: null, 
        customization: true
    };

    // Send the POST request to your add-to-cart API
    fetch('../auth/api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(cartData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Item added to cart successfully!');
            location.reload();    
        } else {
            alert('Failed to add item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the item to the cart.');
    });
}

// Close the modal when clicking outside of it
window.onclick = function (event) {
    var modal = document.getElementById('itemModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function addToCart(itemID) {
    
    const userID = document.getElementById('userId').value;

    if (userID === "" || userID === null || userID === "guest") {
        alert('Please login to add product to your cart!');
        return;
    }

    // Create data object to send
    const cartData = {
        itemID: itemID,
        userID: userID,
        personalItemID: null,
        customization: false
    };

    fetch('../auth/api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(cartData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Item added to cart successfully!');
            location.reload(); 
        } else {
            alert('Failed to add item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the item to the cart.');
    });
}