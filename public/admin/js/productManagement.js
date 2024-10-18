let productIDToRemove = '';

function setProductID(productID) {
    productIDToRemove = productID;
    document.getElementById('productIDToRemove').innerText = productID;
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProductForm');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Basic form validation
        const formData = new FormData(form);
        const productData = {
            ItemName: formData.get('ItemName').trim(),
            ItemPrice: parseFloat(formData.get('ItemPrice')),
            ItemQuantity: parseInt(formData.get('ItemQuantity')),
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