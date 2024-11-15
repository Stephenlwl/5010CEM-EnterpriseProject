let currentSlide = 0;

function showNext() {
    const featureBox = document.getElementById('featureBox');
    const featureBox2 = document.getElementById('featureBox2');

    if (currentSlide === 0) {
        featureBox.style.opacity = 0; // Start fading out

        // Wait for the fade-out effect to finish before changing boxes
        setTimeout(() => {
            featureBox.classList.add('hidden'); // Ensure it's hidden
            featureBox2.classList.remove('hidden'); // Show the next set
            featureBox2.style.opacity = 1; // Fade in the next set
            currentSlide = 1; // Update slide index
        }, 500); // Match this time with your CSS transition duration
    }
}

function showPrev() {
    const featureBox = document.getElementById('featureBox');
    const featureBox2 = document.getElementById('featureBox2');

    if (currentSlide === 1) {
        featureBox2.style.opacity = 0; // Start fading out

        // Wait for the fade-out effect to finish before changing boxes
        setTimeout(() => {
            featureBox2.classList.add('hidden'); // Ensure it's hidden
            featureBox.classList.remove('hidden'); // Show the previous set
            featureBox.style.opacity = 1; // Fade in the previous set
            currentSlide = 0; // Update slide index
        }, 500); // Match this time with your CSS transition duration
    }
}

function updateSalesSection() {
    let totalCoffeeQuantity = 0;

    // fetch sales data from the get sales api folder
    fetch('auth/api/get_sales.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // loop through the daily sales data and update add up the coffee sold total
                data.data.dailySales.forEach(sale => {
                    totalCoffeeQuantity += parseInt(sale.CoffeeSold);
                });

                // update total coffee items sold so far
                document.getElementById('totalCoffeeSold').textContent = totalCoffeeQuantity + " Cups";
            } else {
                console.error('Error fetching sales data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching sales data:', error);
        });
}

function updateTopThreeCoffee() {
    fetch('auth/api/get_top_products.php') 
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // get the top 3 coffee drinks data
            const topProducts = data.data.topCoffeeDrinks;

            document.getElementById('coffee1').innerHTML = `
                <h3>${topProducts[0].ItemName}</h3>
                <img src="auth/api/get_image_from_menu.php?ItemID=${topProducts[0].ItemID}" 
                    onerror="this.onerror=null; this.src='img/coffee-placeholder.jpg';"
                    alt="${topProducts[0].ItemName}" 
                    style="width: 90%; border-radius: 10px;" />
                <h4 class="mt-3 text-danger">Total Sold: ${topProducts[0].TotalCoffeeDrinkSold}</h4>
            `;
            document.getElementById('coffee2').innerHTML = `
                <h3>${topProducts[1].ItemName}</h3>
                <img src="auth/api/get_image_from_menu.php?ItemID=${topProducts[1].ItemID}" 
                    onerror="this.onerror=null; this.src='img/coffee-placeholder.jpg';"
                    alt="${topProducts[1].ItemName}" 
                    style="width: 90%; border-radius: 10px;" />
                <h4 class="mt-3 text-danger">Total Sold: ${topProducts[1].TotalCoffeeDrinkSold}</h4>
            `;
            document.getElementById('coffee3').innerHTML = `
                <h3>${topProducts[2].ItemName}</h3>
                <img src="auth/api/get_image_from_menu.php?ItemID=${topProducts[2].ItemID}" 
                    onerror="this.onerror=null; this.src='img/coffee-placeholder.jpg';"
                    alt="${topProducts[2].ItemName}" 
                    style="width: 90%; border-radius: 10px;" />
                <h4 class="mt-3 text-danger">Total Sold: ${topProducts[2].TotalCoffeeDrinkSold}</h4>
            `;
            document.getElementById('coffee4').innerHTML = `
                <h3>${topProducts[3].ItemName}</h3>
                <img src="auth/api/get_image_from_menu.php?ItemID=${topProducts[3].ItemID}" 
                    onerror="this.onerror=null; this.src='img/coffee-placeholder.jpg';"
                    alt="${topProducts[3].ItemName}" 
                    style="width: 90%; border-radius: 10px;" />
                <h4 class="mt-3 text-danger">Total Sold: ${topProducts[3].TotalCoffeeDrinkSold}</h4>
            `;
            document.getElementById('coffee5').innerHTML = `
                <h3>${topProducts[4].ItemName}</h3>
                <img src="auth/api/get_image_from_menu.php?ItemID=${topProducts[4].ItemID}" 
                    onerror="this.onerror=null; this.src='img/coffee-placeholder.jpg';"
                    alt="${topProducts[4].ItemName}" 
                    style="width: 90%; border-radius: 10px;" />
                <h4 class="mt-3 text-danger">Total Sold: ${topProducts[4].TotalCoffeeDrinkSold}</h4>
            `;
            document.getElementById('coffee6').innerHTML = `
                <h3>${topProducts[5].ItemName}</h3>
                <img src="auth/api/get_image_from_menu.php?ItemID=${topProducts[5].ItemID}" 
                    onerror="this.onerror=null; this.src='img/coffee-placeholder.jpg';"
                    alt="${topProducts[5].ItemName}" 
                    style="width: 90%; border-radius: 10px;" />
                <h4 class="mt-3 text-danger">Total Sold: ${topProducts[5].TotalCoffeeDrinkSold}</h4>
            `;

        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
}
setInterval(updateSalesSection, 1000);  // refresh every 1 second
setInterval(updateTopThreeCoffee, 1000);  

// initial fetch when the page loads
updateSalesSection();
updateTopThreeCoffee();
