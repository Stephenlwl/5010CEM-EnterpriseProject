function setPromoCode(promoCode) {
    promoCodeToRemove = promoCode;
    document.getElementById('promoCodeToRemove').innerText = promoCode;
    
}

function removePromotion() {

    const promoCode = promoCodeToRemove;

    fetch('../auth/api/remove_promo_code.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ promoCode: promoCode }) 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Deleted! ' + data.message)
            window.location.reload();
        } else {
            alert('Error! ' + data.message);
        }
    })
    .catch(error => {
        alert('Error! An unexpected error occurred. ' + error);
    });
}
   


document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addPromoForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const promoCode = document.getElementById('PromoCode').value;
        const discountType = document.getElementById('DiscountType').value; // Assumed added to the form
        const discountValue = document.getElementById('DiscountValue').value;
        const promoEndDate = document.getElementById('promoEndDate').value;
        const terms = document.getElementById('TermsAndConditions').value;

        fetch('../auth/api/add_promotion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                'promoCode': promoCode,
                'discountType': discountType,
                'discountValue': discountValue,
                'promoEndDate': promoEndDate,
                'termsAndConditions': terms
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Success ! ' + data.message);
                window.location.reload();
            } else {
                alert('Error ! ' + data.message);
            }
        });
    });
});