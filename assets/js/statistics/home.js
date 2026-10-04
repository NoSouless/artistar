const ctx = document.getElementById('myChart').getContext('2d');
const formatMoney = v => currency + ' ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
var myChart
function atualizarGrafico(data, real = false) {
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

    var data = {
        labels: labels,
        datasets: [{
            data: data,
            fill: true,
            borderColor: '#8B84D7',
            backgroundColor: 'rgba(139, 132, 215, 0.2)',
        }]
    };
    var config = {
        type: 'line',
        data: data,
        options: {
            legend: {
                display: false
            },
        }
    };
    if (real) {
        config.options.tooltips = {
            callbacks: {
                label: function(tooltipItem, data) {
                    return formatMoney(tooltipItem.yLabel);
                }
            }
        };
        config.options.scales = {
            yAxes: [{
                ticks: {
                    callback: function(value) {
                        return formatMoney(value);
                    }
                }
            }]
        };
    } else {
        // ticks 1
        config.options.scales = {
            yAxes: [{
                ticks: {
                    stepSize: 1
                }
            }]
        };
    }
    return new Chart(ctx, config);
}

document.addEventListener("DOMContentLoaded", function() {
    myChart = atualizarGrafico(totalVendas, true);
});

$('#graphByValue').on('click', function() {
    myChart.destroy();
    myChart = atualizarGrafico(totalVendas, true);
});

$('#graphByQuantity').on('click', function() {
    myChart.destroy();
    myChart = atualizarGrafico(totalTransacoes, false);
});

const slider = document.getElementById("slider");
const buttons = document.querySelectorAll("#toggle>.toggle-btn");

buttons.forEach(btn => {
    btn.addEventListener("click", () => {
        var side = btn.getAttribute("data-side");

        buttons.forEach(b => b.classList.remove("active", "text-white", "text-dark"));

        if (side === "left") {
            slider.classList.remove("right");
            slider.classList.add("left");
            btn.classList.add("active", "text-white");
            buttons[1].classList.add("text-dark");
        } else {
            slider.classList.remove("left");
            slider.classList.add("right");
            btn.classList.add("active", "text-white");
            buttons[0].classList.add("text-dark");
        }
    });
});

const sliderContainer = document.getElementById("slider-product-order");
const buttonsProduct = document.querySelectorAll("#toggle-product-order>.toggle-btn");

buttonsProduct.forEach(btn => {
    btn.addEventListener("click", () => {
        var side = btn.getAttribute("data-side");

        buttonsProduct.forEach(b => b.classList.remove("active", "text-white", "text-dark"));

        if (side === "left") {
            sliderContainer.classList.remove("right");
            sliderContainer.classList.add("left");
            btn.classList.add("active", "text-white");
            buttonsProduct[1].classList.add("text-dark");
        } else {
            sliderContainer.classList.remove("left");
            sliderContainer.classList.add("right");
            btn.classList.add("active", "text-white");
            buttonsProduct[0].classList.add("text-dark");
        }
    });
});

$('#productsByRevenue').on('click', function() {
    //Reorder the table based on revenue
    //The Revenue is a data-revenue attribute
    const rows = Array.from(document.querySelectorAll('#productsTable tbody tr'));
    rows.sort((a, b) => {
        const aRevenue = parseFloat(a.getAttribute('data-revenue'));
        const bRevenue = parseFloat(b.getAttribute('data-revenue'));
        return bRevenue - aRevenue;
    });

    rows.forEach((row, index) => {
        let id = row.getAttribute('data-product');
        $('#position-' + id).html('<span class="color-stellar-blue">#' + (index + 1) + '</span>');
        document.querySelector('#productsTable tbody').appendChild(row);
    });
});

$('#productsByQuantity').on('click', function() {
    //Reorder the table based on quantity
    const rows = Array.from(document.querySelectorAll('#productsTable tbody tr'));
    rows.sort((a, b) => {
        const aQuantity = parseInt(a.getAttribute('data-quantity'));
        const bQuantity = parseInt(b.getAttribute('data-quantity'));
        return bQuantity - aQuantity;
    });
    rows.forEach((row, index) => {
        let id = row.getAttribute('data-product');
        $('#position-' + id).html('<span class="color-stellar-blue">#' + (index + 1) + '</span>');
        document.querySelector('#productsTable tbody').appendChild(row);
    });
});

var colors = [
    {
        //#D00000 in rgba
        color: 'rgba(255, 0, 0, 1)',
        backgroundColor: 'rgba(255, 0, 0, 0.2)'
    },
    {
        //3185FC
        color: 'rgba(49, 133, 252, 1)',
        backgroundColor: 'rgba(49, 133, 252, 0.2)'
    },
    {
        //FFBA08
        color: 'rgba(255, 186, 8, 1)',
        backgroundColor: 'rgba(255, 186, 8, 0.2)'
    },
    {
        //CBFF8C
        color: 'rgba(203, 255, 140, 1)',
        backgroundColor: 'rgba(203, 255, 140, 0.2)'
    },
    {
        //FF7B9C
        color: 'rgba(255, 123, 156, 1)',
        backgroundColor: 'rgba(255, 123, 156, 0.2)'
    },
    {
       //46237A
       color: 'rgba(70, 35, 122, 1)',
       backgroundColor: 'rgba(70, 35, 122, 0.2)'
    },
    {
        //FF9B85
        color: 'rgba(255, 155, 133, 1)',
        backgroundColor: 'rgba(255, 155, 133, 0.2)'
    },
    {
        //1B998B
        color: 'rgba(27, 153, 139, 1)',
        backgroundColor: 'rgba(27, 153, 139, 0.2)'
    },
    {
       //5D2E8C
       color: 'rgba(93, 46, 140, 1)',
       backgroundColor: 'rgba(93, 46, 140, 0.2)'
    },
    {
        //8FE388
        color: 'rgba(143, 227, 136, 1)',
        backgroundColor: 'rgba(143, 227, 136, 0.2)'
    },






];

for (const [key, graph] of Object.entries(customGraphs)) {
    let datasets = [];
    const graphLabel = document.getElementById(`graph-label-${graph.id}`);
    if (graphLabel) graphLabel.textContent = graph.name;
    switch (graph.type) {
        case 'pie':
            new Chart(document.getElementById(`grafico-0${graph.id}`).getContext('2d'), {
                type: 'pie',
                data: {
                    labels: graph.data.map(item => item.nome),
                    datasets: [{
                        label: graph.data.map(item => item.nome),
                        data: graph.data.map(item => item.total),
                        borderColor: graph.data.map((item, index) => colors[index % colors.length].color),
                        backgroundColor: graph.data.map((item, index) => colors[index % colors.length].backgroundColor)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom',
                        display: true
                    },
                    tooltips: {
                       callbacks: {
                            label: function(tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var valor = dataset.data[tooltipItem.index];
                                valor = parseFloat(valor);

                                var total = data.datasets[tooltipItem.datasetIndex].data;

                                var total = data.datasets[tooltipItem.datasetIndex].data.reduce(function(prev, curr) {
                                    return parseFloat(prev) + parseFloat(curr);
                                });

                                var percentual = ((valor / total) * 100).toFixed(2) + '%';
                                if (graph.real) {

                                    var valorFormatado = formatMoney(valor);
                                } else {
                                    var valorFormatado = valor.toString();
                                }

                                return data.labels[tooltipItem.index] + ': ' + valorFormatado + ' (' + percentual + ')';
                            }
                        }
                    },
                }
            });
            break;
        case 'bar':
            graph.data.forEach((item, index) => {
                datasets.push({
                    label: item.nome,
                    data: [item.total],
                    backgroundColor: colors[index % colors.length].backgroundColor,
                    borderColor: colors[index % colors.length].color,
                    borderWidth: 1
                });
            });
            new Chart(document.getElementById(`grafico-0${graph.id}`).getContext('2d'), {
                type: 'bar',
                data: {
                    labels: graph.data.map(item => item.nome),
                    datasets: [{
                        label: 'Total',
                        data: graph.data.map(item => item.total),
                        borderColor: graph.data.map((item, index) => colors[index % colors.length].color),
                        backgroundColor: graph.data.map((item, index) => colors[index % colors.length].backgroundColor)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        display: false
                    },
                    tooltips: {
                       callbacks: {
                            label: function(tooltipItem, data) {
                                if (graph.real) {
                                    return formatMoney(tooltipItem.yLabel);
                                } else {
                                    return tooltipItem.yLabel;
                                }
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                callback: function(value) {
                                    if (graph.real) {
                                        return formatMoney(value);
                                    } else {
                                        return value;
                                    }
                                },
                                precision: (!graph.real) ? 0 : undefined,
                                beginAtZero: true
                            },
                        }]
                    }
                }
            });
            break;
        case 'line':
            graph.data.forEach((item, index) => {
                datasets.push({
                    label: item.label,
                    data: item.data,
                    backgroundColor: colors[index % colors.length].backgroundColor,
                    borderColor: colors[index % colors.length].color,
                    borderWidth: 1
                });
            });
            new Chart(document.getElementById(`grafico-0${graph.id}`).getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        display: true
                    },
                    tooltips: {
                       callbacks: {
                            label: function(tooltipItem, data) {
                                if (graph.real) {
                                    return formatMoney(tooltipItem.yLabel);
                                } else {
                                    return tooltipItem.yLabel;
                                }
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                callback: function(value) {
                                    if (graph.real) {
                                        return formatMoney(value);
                                    } else {
                                        return value;
                                    }
                                },
                                precision: (!graph.real) ? 0 : undefined,
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
            break;
        default:
            console.log(`Unknown chart type for ${graph.name}`);
    }
}



$('[data-modal-label]').on('click', function() {
    var modalLabel = $(this).data('modal-label') + ' - '+customGraphs[$(this).data('graph-id')].name;
    $('#editGraphModalLabel').text(modalLabel);
    $('#graph-id').val($(this).data('graph-id'));
    var graphBase = customGraphs[$(this).data('graph-id')].base;
    $('#graph-type').val(graphBase.grafico_tipo).trigger('change');
    $('#graph-counter').val(graphBase.grafico_contador).trigger('change');
    $('#graph-target').val(graphBase.grafico_alvo).trigger('change');
    $('#graph-filter').val(graphBase.grafico_filtro).trigger('change');

    if (graphBase.grafico_alvo === 'category') {
        $('#graph-category').val(graphBase.grafico_lista.split(',')).trigger('change');
    } else if (graphBase.grafico_alvo === 'product') {
        $('#graph-products').val(graphBase.grafico_lista.split(',')).trigger('change');
    }

});

$(document).ready(function() {
    $('#graph-category').select2({
        placeholder: $("#graph-category").data('placeholder'),
        allowClear: true,
        dropdownParent: $('#editGraphModal'),
        width: '100%',
        language: {
            noResults: function() {
                return $("#graph-category").data('noResults');
            }
        }
    });

    $('#graph-products').select2({
        placeholder: $("#graph-products").data('placeholder'),
        allowClear: true,
        dropdownParent: $('#editGraphModal'),
        width: '100%',
        language: {
            noResults: function() {
                return $("#graph-products").data('noResults');
            }
        }
    });

    $('#editGraphModal').on('shown.bs.modal', function () {
        $('#graph-category').select2({
            placeholder: $("#graph-category").data('placeholder'),
            allowClear: true,
            dropdownParent: $('#editGraphModal'),
            width: '100%',
            language: {
                noResults: function() {
                    return $("#graph-category").data('noResults');
                }
            }
        });

        $('#graph-products').select2({
            placeholder: $("#graph-products").data('placeholder'),
            allowClear: true,
            dropdownParent: $('#editGraphModal'),
            width: '100%',
            language: {
                noResults: function() {
                    return $("#graph-products").data('noResults');
                }
            }
        });
    });

});

function atualizaListaPersonalizada() {
    $('#graph-category-container').hide();
    $('#graph-products-container').hide();
    if ($('#graph-filter').val() === 'custom') {
        if ($('#graph-target').val() === 'category') {
            $('#graph-category-container').show();
        } else if ($('#graph-target').val() === 'product') {
            $('#graph-products-container').show();
        }
    }
}

//If graph-target is "category", stop showing the "all_option" and "top_10_option" and select the "custom_option"
$('#graph-target').on('change', function() {
    switch($(this).val()) {
        case 'product':
            $('#all_option').show();
            $('#top_10_option').show();
            $('#custom_option').show();
            break;
        case 'category':
            $('#all_option').hide();
            $('#top_10_option').hide();
            $('#custom_option').show();
            $('#custom_option').prop('selected', true);
            break;
        case 'payment_method':
            $('#all_option').show();
            $('#top_10_option').hide();
            $('#custom_option').hide();
            $('#all_option').prop('selected', true);
            break;
        default:
            alert("Opção inválida");

    }
    atualizaListaPersonalizada();
});


$('#graph-filter').on('change', function() {
    atualizaListaPersonalizada();
});

$(document).on('click', '#edit-graph-btn', function() {

    var form = $('#form-edit-graph')[0];
    var formData = new FormData(form);

    $.ajax({
        url: '/statistics/edit-graph',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            location.href = $('#form-edit-graph').attr('action'); // Redireciona para a ação do formulário
        } else {
            console.error(dictionary.error, response.message);
        }
    }).fail(function (error) {
        console.error('An error occurred:', error);
    });
});
