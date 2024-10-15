document.addEventListener('DOMContentLoaded', function () {
    // get the top products 
    fetch('../auth/api/get_top_products.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const itemNames = data.data.topProducts.map(item => item.ItemName);
                const quantities = data.data.topProducts.map(item => item.TotalQuantityOrdered);
                
                // showing top products in table
                const topProductsTableBody = document.getElementById('topProductsTable');
                topProductsTableBody.innerHTML = ''; 
                data.data.topProducts.forEach(item => {
                    const row = `<tr>
                                    <td>${item.ItemName}</td>
                                    <td>${item.TotalQuantityOrdered}</td>
                                </tr>`;
                    topProductsTableBody.innerHTML += row;
                });

                const ctx1 = document.getElementById('topProductsChart').getContext('2d');
                const topProductsChart = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: itemNames,
                        datasets: [{
                            label: 'Total Orders',
                            data: quantities,
                            backgroundColor: 'rgba(255, 223, 77, 0.2)', 
                            borderColor: 'rgba(255, 223, 77, 1)',  
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                console.error("Failed to fetch top products:", data.data.message);
            }
        })
        .catch(error => console.error("Error fetching top products:", error));


    // get sales data
    fetch('../auth/api/get_sales.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // get daily sales details
                const dailySaleDates = data.data.dailySales.map(sale => sale.SaleDate);
                const dailySales = data.data.dailySales.map(sale => sale.DailySales);

                const ctx1 = document.getElementById('salesOverTimeChart').getContext('2d');
                const dailySalesChart = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: dailySaleDates,
                        datasets: [{
                            label: 'Daily Sales',
                            data: dailySales,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // get weekly sales chart info
                const weeklySalesData = data.data.weeklySales.map(sale => `${sale.Year}-W${sale.Week}`);
                const weeklySales = data.data.weeklySales.map(sale => sale.WeeklySales);

                const ctx2 = document.getElementById('weeklySalesChart').getContext('2d');
                const weeklySalesChart = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: weeklySalesData,
                        datasets: [{
                            label: 'Weekly Sales',
                            data: weeklySales,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // get monthly sales chart data
                const monthlySalesData = data.data.monthlySales.map(sale => sale.Month);
                const monthlySales = data.data.monthlySales.map(sale => sale.MonthlySales);

                const ctx3 = document.getElementById('monthlySalesChart').getContext('2d');
                const monthlySalesChart = new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: monthlySalesData,
                        datasets: [{
                            label: 'Monthly Sales',
                            data: monthlySales,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                console.error("Failed to fetch sales data:", data.message);
            }
        })
        .catch(error => console.error("Error fetching sales data:", error));
});
