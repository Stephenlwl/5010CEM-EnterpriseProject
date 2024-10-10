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

            // Update the modal's content.
            var modalBody = editAddressModal.querySelector('.modal-body');
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

    //delete address
    window.deleteAddress = function (addressId) {
        if (confirm('Are you sure you want to delete this address?')) {
            fetch('../auth/api/remove_address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' // Ensures the server knows you're sending JSON
                },
                body: JSON.stringify({ address_id: addressId }) // Send the address ID as JSON
            })
            .then(response => {
                // Check if the response is ok and parse JSON
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
    };

    
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

