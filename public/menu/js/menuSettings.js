let selectedItemId;

// Function to show item details in a modal
function showDetails(name, price, itemId) {
    document.getElementById('modalItemName').textContent = name;
    document.getElementById('modalItemPrice').textContent = "Price: $" + price.toFixed(2);

    selectedItemId = itemId; // Store the item ID for use in customization

    // Display the modal
    document.getElementById('itemModal').style.display = 'block';
}

// Function to close the modal
function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}

// Function to add an item with customizations to the cart
function addToCartWithCustomization() {
    const temperature = document.getElementById('temperature').value;
    const milk = document.getElementById('milk').value;
    const size = document.getElementById('size').value;
    const syrup = document.getElementById('syrup').value;

    alert("Item " + selectedItemId + " added to cart with customization:\n" +
        "Temperature: " + temperature + "\n" +
        "Milk: " + milk + "\n" +
        "Size: " + size + "\n" +
        "Syrup: " + syrup);

    // You can integrate actual cart functionality here
    closeModal();
}

// Close the modal when clicking outside of it
window.onclick = function (event) {
    var modal = document.getElementById('itemModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function addToCart(itemId) {
    alert("Item " + itemId + " added to cart.");
    // You can integrate actual cart functionality here
}