function addToCartWithCustomization() {
    // Collect item details and customization options
    var itemName = document.getElementById("modalItemName").innerText;
    var itemPrice = parseFloat(document.getElementById("modalItemPrice").innerText.replace('$', ''));
    var itemID = document.getElementById("itemModal").getAttribute("data-item-id");

    var temperature = document.getElementById("temperature").value;
    var milk = document.getElementById("milk").value;
    var size = document.getElementById("size").value;
    var syrup = document.getElementById("syrup").value;

    // Send data to the add_to_cart.php API using AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("Item added to cart successfully!");
            } else {
                alert("Error: " + response.message);
            }
        }
    };

    var params = "itemID=" + itemID + 
                 "&itemName=" + encodeURIComponent(itemName) + 
                 "&itemPrice=" + itemPrice +
                 "&quantity=1" +  // You can modify this to get dynamic quantity
                 "&temperature=" + temperature +
                 "&milk=" + milk +
                 "&size=" + size +
                 "&syrup=" + syrup;

    xhr.send(params);

    closeModal();  // Close modal after adding to cart
}
