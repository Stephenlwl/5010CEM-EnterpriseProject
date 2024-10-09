document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('contentFrame');
    const page = new URLSearchParams(window.location.search).get('page'); // get 'page' from query params

    // set the iframe source based on the page parameter
    if (page === 'editProfile') {
        iframe.src = 'userProfileSettings/editProfile.php';
    } else if (page === 'orderTracking') {
        iframe.src = 'userProfileSettings/orderTracking.php';
    } else if (page === 'orderHistory') {
        iframe.src = 'userProfileSettings/orderHistory.php';
    } else if (page === 'favouriteList') {
        iframe.src = 'userProfileSettings/favouriteList.php';
    } else if (page === 'deliveryAddress') {
        iframe.src = 'userProfileSettings/deliveryAddress.php';
    } else {
        // default/ when no valid page is found
        iframe.src = 'userProfileSettings/editProfile.php';
    }
});
