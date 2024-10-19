function editStock(itemID, currentStock, stockThreshold, itemName, autoRestockQuantity) {
    document.getElementById('itemID').value = itemID;                     
    document.getElementById('currentQuantity').value = currentStock;      
    document.getElementById('newQuantity').value = '';                  
    document.getElementById('stockThreshold').value = stockThreshold;   
    document.getElementById('productName').innerText = itemName;   
    document.getElementById('autoRestockQuantity').value = autoRestockQuantity;    
}

document.addEventListener('DOMContentLoaded', function() {
    var editStockForm = document.getElementById('editStockForm'); 

    if (editStockForm) {
        editStockForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            var itemID = document.getElementById('itemID').value;
            var currentStock = document.getElementById('currentQuantity').value;
            var newQuantity = document.getElementById('newQuantity').value;
            var stockThreshold = document.getElementById('stockThreshold').value;
            var autoRestockQuantity = document.getElementById('autoRestockQuantity').value;

            if (newQuantity <= 0) {
                alert('New quantity must be greater than 0');
                return;
            }

            fetch('../auth/api/add_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    itemID: itemID,
                    currentStock: currentStock,
                    newQuantity: newQuantity,
                    stockThreshold: stockThreshold,
                    autoRestockQuantity: autoRestockQuantity
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error updating stock');
                    console.error('Error updating stock:', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
