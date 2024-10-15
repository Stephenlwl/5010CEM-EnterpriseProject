document.addEventListener('DOMContentLoaded', function () {
    var editAddressModal = document.getElementById('editAddressModal');
    if (editAddressModal) {
        editAddressModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // the button that triggered the modal
            var addressId = button.getAttribute('data-id'); // extract the info from data-* attributes
            var addressName = button.getAttribute('data-name');
            var address1 = button.getAttribute('data-address1');
            var address2 = button.getAttribute('data-address2');
            var postal = button.getAttribute('data-postal');
            var state = button.getAttribute('data-state');
            var adminId = button.getAttribute('data-admin-id');

            // Update the modal's content.
            var modalBody = editAddressModal.querySelector('.modal-body');
            modalBody.querySelector('#editAdminID').value = adminId;
            modalBody.querySelector('#editAddressID').value = addressId;
            modalBody.querySelector('#editAddressName').value = addressName;
            modalBody.querySelector('#editAddress1').value = address1;
            modalBody.querySelector('#editAddress2').value = address2;
            modalBody.querySelector('#editPostalCode').value = postal;
            modalBody.querySelector('#editState').value = state;
            
        });
    } else {
        console.error('Modal element not found!');
    }

    // Add submit event listener for the edit form
    var editAddressForm = document.getElementById('editAddressForm');
    if (editAddressForm) {
        editAddressForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Retrieve form data
            const addressData = new FormData(this);

            // Send the updated address to the server using fetch API or AJAX
            fetch('../auth/api/update_address.php', {
                method: 'POST',
                body: addressData,
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the server
                if (data.success) {
                    alert('Address updated successfully!');
                    location.reload(); // Refresh the page 
                } else {
                    alert('Error updating address: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    } else {
        console.error('Edit address form not found!');
    }
    
    // Submit event listener for add address form
    var addAddressForm = document.getElementById('addAddressForm');
    if (addAddressForm) {
        addAddressForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Retrieve form data
            const addressData = new FormData(this);

            // Send new address to the server
            fetch('../auth/api/add_address.php', {
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
}); 

function setRemoveAddressId(addressId, AddressName, adminId) {
    removeAddressId = addressId;
    removeAdminId = adminId;
    document.getElementById('addressToRemove').innerHTML = AddressName;
}

//delete address
function removeAddress() {

    var addressId = removeAddressId;
    var admin_id = removeAdminId;

    fetch('../auth/api/remove_address.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ address_id: addressId, admin_id: admin_id }) 
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Server returned an error!');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Address deleted successfully!');
            location.reload();
        } else {
            alert('Error deleting address: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the address.');
    });
}
    

