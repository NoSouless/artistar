document.addEventListener("DOMContentLoaded", function () {
    const formatMoney = v => homeData.currency + ' ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Inventory chart
    const stockUrl = '/stock?filter%5Bstock_status%5D=';
    const stockStatus = ['good', 'low', 'out', 'dead'];
    const stockColors = ['rgba(25, 135, 84, 1)', 'rgba(255,193,7, 1)', 'rgba(220, 53, 69, 1)', 'rgba(108,117,125, 1)'];
    new Chart(document.getElementById('stockChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: homeData.stock.labels,
            datasets: [{
                label: homeData.stock.datasetLabel,
                data: homeData.stock.values,
                backgroundColor: stockColors,
                borderColor: stockColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] },
            responsive: true,
            legend: { display: false },
            title: { display: false },
            onClick: function (evt, elements) {
                if (elements.length > 0) location.href = stockUrl + stockStatus[elements[0]._index];
            },
            onHover: function (event, elements) {
                const target = event.native ? event.native.target : event.target;
                target.style.cursor = elements && elements.length ? 'pointer' : 'default';
            }
        }
    });

    // Sales chart
    const salesCanvas = document.getElementById('salesChart');
    let salesChart = null;
    function renderSalesChart(values, isMoney) {
        if (salesChart) salesChart.destroy();
        const config = {
            type: 'line',
            data: {
                labels: homeData.sales.labels,
                datasets: [{
                    data: values,
                    fill: true,
                    borderColor: '#8B84D7',
                    backgroundColor: 'rgba(139, 132, 215, 0.2)'
                }]
            },
            options: { legend: { display: false } }
        };
        if (isMoney) {
            config.options.tooltips = { callbacks: { label: item => formatMoney(item.yLabel) } };
            config.options.scales = { yAxes: [{ ticks: { callback: value => formatMoney(value) } }] };
        } else {
            config.options.scales = { yAxes: [{ ticks: { stepSize: 1 } }] };
        }
        salesChart = new Chart(salesCanvas.getContext('2d'), config);
    }
    renderSalesChart(homeData.sales.value, true);

    const slider = document.getElementById('slider');
    const buttons = document.querySelectorAll('#toggle > .toggle-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const left = btn.getAttribute('data-side') === 'left';
            buttons.forEach(b => b.classList.remove('active', 'text-white', 'text-dark'));
            slider.classList.toggle('left', left);
            slider.classList.toggle('right', !left);
            btn.classList.add('active', 'text-white');
            buttons[left ? 1 : 0].classList.add('text-dark');
            renderSalesChart(left ? homeData.sales.value : homeData.sales.quantity, left);
        });
    });

    // Events calendar
    new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: homeData.locale,
        initialView: 'dayGridMonth',
        customButtons: {
            novoEvento: {
                text: homeData.newEventText,
                click: () => { window.location.href = '/events/create'; }
            }
        },
        headerToolbar: { left: 'prev next today', center: 'title', right: 'novoEvento' },
        buttonText: homeData.buttonText,
        events: homeData.events,
        height: 'auto'
    }).render();
});
