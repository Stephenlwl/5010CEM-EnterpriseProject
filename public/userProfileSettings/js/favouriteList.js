function removeFavorite(personalItemID) {
    if (confirm('Are you sure you want to remove this item from your favorites?')) {
        fetch('../auth/api/remove_favourite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ PersonalItemID: personalItemID })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.querySelector(`[data-personalitemid="${personalItemID}"]`).remove();
                if (document.querySelectorAll('.favorite-item').length === 0) {
                    document.querySelector('.favorite-items').innerHTML = '<p class="no-favorites">You haven\'t added any favorites yet.</p>';
                }
            } else {
                alert('Failed to remove item from favorites. Please try again.');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}