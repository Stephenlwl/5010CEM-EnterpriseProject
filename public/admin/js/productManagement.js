let productNameToRemove = '';

function setProductName(productName) {
    productNameToRemove = productName;
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
            'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
        },
        body: JSON.stringify({ 
            productID: productIDToRemove,
            csrf_token: document.querySelector('input[name="csrf_token"]').value
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

function editProduct(itemID, productName, productPrice, productImagePath) {
    document.getElementById('itemID').value = itemID;
    document.getElementById('currentProductName').value = productName;
    document.getElementById('productName').textContent = productName;
    document.getElementById('currentPrice').value = productPrice;
    document.getElementById('currentImagePath').value = productImagePath;
}

function updateProduct(event) {
    event.preventDefault(); 

    const itemID = document.getElementById('itemID').value;
    const newProductName = document.getElementById('newProductName').value;
    const newProductPrice = document.getElementById('newProductPrice').value;
    const newImagePath = document.getElementById('currentImagePath').value;

    // prepare data object with only modified fields
    const data = { itemID: itemID };
    if (newProductName) data.newProductName = newProductName;
    if (newProductPrice) data.newProductPrice = newProductPrice;
    if (newImagePath) data.newImagePath = newImagePath;

    // Send the update request
    fetch('../auth/api/update_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
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

        // Basic form validation
        const formData = new FormData(form);
        const productData = {
            ItemName: formData.get('ItemName').trim(),
            ItemPrice: parseFloat(formData.get('ItemPrice')),
            ItemType: formData.get('ItemType').trim(),
            ImagePath: formData.get('ImagePath').trim(),
            csrf_token: formData.get('csrf_token')
        };

        // Validate data
        if (!productData.ItemName || !productData.ItemType || !productData.ImagePath) {
            showAlert('Please fill in all required fields.', 'warning');
            return;
        }

        if (productData.ItemPrice < 0) {
            showAlert('Price cannot be negative.', 'warning');
            return;
        }

        if (productData.ItemQuantity < 0) {
            showAlert('Quantity cannot be negative.', 'warning');
            return;
        }

        fetch('../auth/api/add_product.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-Token': productData.csrf_token
            },
            body: JSON.stringify(productData)
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