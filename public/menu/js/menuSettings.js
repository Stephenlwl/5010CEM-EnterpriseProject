let selectedItemId;

function showDetails(itemName, itemPrice, itemID) {
    document.getElementById('modalItemName').innerText = itemName;
    document.getElementById('modalItemPrice').innerText = 'RM' + itemPrice.toFixed(2);
    document.getElementById('modalItemID').value = itemID;

    // Show the modal
    document.getElementById('itemModal').style.display = 'block';
}


function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}


function addToCartWithCustomization() {
    // Get values from the modal
    const itemID = document.getElementById('modalItemID').value;
    const itemName = document.getElementById('modalItemName').innerText;
    const itemPrice = document.getElementById('modalItemPrice').innerText.replace('RM', '');
    const temperature = document.getElementById('Temperature').value;
    const sweetness = document.getElementById('Sweetness').value;
    const addShot = document.getElementById('AddShot').value;
    const milkType = document.getElementById('MilkType').value;
    const coffeeBean = document.getElementById('CoffeeBean').value;
    const quantity = document.getElementById('Quantity').value;

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
        userID: 14, // Example userID, replace with session or dynamic value
        personalItemID: null // If you have personal items, populate here
    };

    // Send the POST request to your add-to-cart API
    fetch('../auth/add-to-cart.php', {
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
    // Send a simplified request with default values for customization
    const cartData = {
        itemID: itemID,
        quantity: 1, // Default quantity
        temperature: "Hot", // Default customization
        sweetness: "Regular",
        addShot: "False",
        milkType: "Diary",
        coffeeBean: "Boss",
        userID: 14, // Example userID, replace with session or dynamic value
        personalItemID: null
    };

    fetch('../auth/add-to-cart.php', {
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
        } else {
            alert('Failed to add item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the item to the cart.');
    });
}
