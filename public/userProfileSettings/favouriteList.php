<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Items</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
</head>
<body>
    <h1>Your Favorite Items</h1>
    <div id="favoriteList"></div>

    <script>
        const userID = 123; // Replace with actual user ID or get from session

        axios.get(`get_favorite_list.php?UserID=${userID}`)
            .then(response => {
                const favoriteList = document.getElementById('favoriteList');
                if (response.data.success) {
                    const items = response.data.data;
                    let html = '<ul>';
                    items.forEach(item => {
                        html += `
                            <li>
                                <h3>${item.ItemName}</h3>
                                <p>${item.Description}</p>
                                <p>Price: $${item.Price}</p>
                                <p>Temperature: ${item.Temperature || 'N/A'}</p>
                                <p>Sweetness: ${item.Sweetness || 'N/A'}</p>
                                <p>Add Shot: ${item.AddShot || 'N/A'}</p>
                                <p>Milk Type: ${item.MilkType || 'N/A'}</p>
                                <p>Coffee Bean: ${item.CoffeeBeanType || 'N/A'}</p>
                            </li>
                        `;
                    });
                    html += '</ul>';
                    favoriteList.innerHTML = html;
                } else {
                    favoriteList.innerHTML = `<p>${response.data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('favoriteList').innerHTML = '<p>An error occurred while fetching your favorite items.</p>';
            });
    </script>
</body>
</html>