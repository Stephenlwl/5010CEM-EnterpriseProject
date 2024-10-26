document.addEventListener("DOMContentLoaded", function () {
    const reportDate = document.getElementById('reportDate');
    
    if (reportDate) {
        reportDate.addEventListener('change', function () {
            const selectedDate = reportDate.value;
            window.location.href = `?date=${selectedDate}`;
        });
    }
});

function printSalesReport() {
    const dateSelected = document.getElementById('reportDate').value;
    if (dateSelected) {
        window.open(`printSalesReport.php?reportDate=${dateSelected}`,'_blank');
    }
}

function printInventoryReport() {
    const inventoryReportWindow = window.open('printInventoryStockReport.php', '_blank');
    inventoryReportWindow.onload = function() {
        inventoryReportWindow.print();
    };
}