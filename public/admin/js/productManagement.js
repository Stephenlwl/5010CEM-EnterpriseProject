let productNameToRemove = '';
let productIDToRemove = 0;

function setProductName(productName, productID) {
    productNameToRemove = productName;
    productIDToRemove = productID;
    document.getElementById('productNameToRemove').innerText = productName;
}

function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.querySelector('.container-fluid').insertAdjacentElement('afterbegin', alertDiv);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => alertDiv.remove(), 5000);
}

function removeProduct() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('removeProductModal'));
    
    fetch('../auth/api/remove_product.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            productID: productIDToRemove
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        modal.hide();
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        modal.hide();
        showAlert('An unexpected error occurred: ' + error.message, 'danger');
    });
}

function editProduct(itemID, productName, productPrice, currentImagePath) {
    document.getElementById('itemID').value = itemID;
    document.getElementById('currentProductName').value = productName;
    document.getElementById('productName').textContent = productName;
    document.getElementById('currentPrice').value = productPrice;
    document.getElementById('currentImagePath').value = currentImagePath;
}

function updateProduct(event) {
    event.preventDefault(); 

    const itemID = document.getElementById('itemID').value;
    const newProductName = document.getElementById('newProductName').value;
    const newProductPrice = document.getElementById('newProductPrice').value;
    const newImagePath = document.getElementById('currentImagePath').files[0];

    // prepare data object with only modified fields
    const formData = new FormData();
    formData.append('itemID', itemID);
    if (newProductName) formData.append('newProductName', newProductName);
    if (newProductPrice) formData.append('newProductPrice', newProductPrice);
    if (newImagePath) formData.append('newImagePath', newImagePath);

    // Send the update request
    fetch('../auth/api/update_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('An unexpected error occurred: ' + error.message);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProductForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Create a FormData object
        const formData = new FormData();

        // Append fields one by one
        formData.append('ItemName', form['ItemName'].value.trim());
        formData.append('ItemPrice', form['ItemPrice'].value);
        formData.append('ItemType', form['ItemType'].value.trim());
        formData.append('ImagePath', form['ImagePath'].files[0]); // Append the file directly
        // formData.append('csrf_token', form['csrf_token'].value);

        // Basic form validation
        if (!formData.get('ItemName') || !formData.get('ItemType') || !formData.get('ImagePath')) {
            showAlert('Please fill in all required fields.', 'warning');
            return;
        }

        if (parseFloat(formData.get('ItemPrice')) < 0) {
            showAlert('Price cannot be negative.', 'warning');
            return;
        }

        // Use fetch to submit the form data including the file
        fetch('../auth/api/add_product.php', {
            method: 'POST',
            body: formData // Send FormData directly
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                alert('Product added successfully!');
                form.reset();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('An unexpected error occurred: ' + error.message, 'danger');
        });
    });
});
