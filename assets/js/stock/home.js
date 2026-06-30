document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dictionary.chart.labels,
            datasets: [{
                label: dictionary.chart.datasetLabel,
                data: [goodStock, lowStock, outOfStock, deadStock],
                backgroundColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(255,193,7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108,117,125, 1)',  
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(255,193,7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108,117,125, 1)',   
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }]
            },
            responsive: true,
            legend: {
                display: false,
            },
            title: {
                display: false,
            },
            onClick: function(evt, elements) {
                let url = '/stock?filter%5Bstock_status%5D=';
                if (elements.length > 0) {
                    switch (elements[0]._index) {
                        case 0:
                            location.href = url + 'good';
                            break;
                        case 1:
                            location.href = url + 'low';
                            break;
                        case 2:
                            location.href = url + 'out';
                            break;
                        case 3:
                            location.href = url + 'dead';
                            break;
                    }
                }
            },
            onHover: function(event, elements) {
                const target = event.native ? event.native.target : event.target;
                if (elements && elements.length) {
                    target.style.cursor = 'pointer';
                } else {
                    target.style.cursor = 'default';
                }
            }
        }
    });
});

$(document).ready(function() {
    $('#new-category').select2({
        tags: true,
        placeholder: dictionary.select2.new.category.placeholder,
        allowClear: true,
        dropdownParent: $('#newModal'),
        width: '100%',
        language: {
            noResults: function() {
                return dictionary.select2.new.category.language;
            }
        }
    });
    $('#new-keywords').select2({
        tags: true,
        placeholder: dictionary.select2.new.keywords.placeholder,
        allowClear: true,
        dropdownParent: $('#newModal'),
        width: '100%',
        language: {
            noResults: function() {
                return dictionary.select2.new.keywords.language;
            }
        }
    });

    $('#filter-category').select2({
        placeholder: dictionary.select2.filter.category.placeholder,
        allowClear: true,
        dropdownParent: $('#filterModal'),
        width: '100%',
        language: {
            noResults: function() {
                return dictionary.select2.filter.category.language;
            }
        }
    });

    $('#newModal').on('shown.bs.modal', function () {
        $('#new-category').select2({
            tags: true,
            placeholder: dictionary.select2.new.category.placeholder,
            allowClear: true,
            dropdownParent: $('#newModal .modal-body'),
            width: '100%',
            language: {
                noResults: function() {
                    return dictionary.select2.new.category.language;
                }
            }
        });
        $('#new-keywords').select2({
            tags: true,
            placeholder: dictionary.select2.new.keywords.placeholder,
            allowClear: true,
            dropdownParent: $('#newModal .modal-body'),
            width: '100%',
            language: {
                noResults: function() {
                    return dictionary.select2.new.keywords.language;
                }
            }
        });
        $('#filter-category').select2({
            placeholder: dictionary.select2.filter.category.placeholder,
            allowClear: true,
            dropdownParent: $('#filterModal'),
            width: '100%',
            language: {
                noResults: function() {
                    return dictionary.select2.filter.category.language;
                }
            }
        });
    });

    $('.moedaReal').inputmask('decimal', {
        radixPoint:",",
        groupSeparator: ".",
        autoGroup: true,
        digits: 2,
        digitsOptional: false,
        placeholder: '0',
        rightAlign: false,
        onBeforeMask: function (value, opts) {
            return value;
        }
    });

    function calcularLucro() {
        let preco = $('#new-price').val();
        let desconto = $('#new-discount').val();
        let custo = $('#new-cost').val();

        preco = preco ? parseFloat(preco.replace(/\./g, '').replace(',', '.')) : 0;
        desconto = desconto ? parseFloat(desconto.replace(/\./g, '').replace(',', '.')) : 0;
        custo = custo ? parseFloat(custo.replace(/\./g, '').replace(',', '.')) : 0;

        let lucro = preco - (custo + desconto);

        let lucroFormatado = lucro.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        $('#new-profit').val(lucroFormatado);
    }

    $('#new-price, #new-discount, #new-cost').on('input keyup change', calcularLucro);
    $('#newModal').on('shown.bs.modal', calcularLucro);

    $('#new-name').on('input keyup change', function() {
        $('#new-nameCount').text($(this).val().length);
        if ($(this).val().length > 50) {
            $(this).addClass('is-invalid');
            $('#new-nameHelp').addClass('text-danger');
        } else {
            $(this).removeClass('is-invalid');
            $('#new-nameHelp').removeClass('text-danger');
        }
    });

    $('#new-insideId').on('input keyup change', function() {
        $('#new-insideIdCount').text($(this).val().length);
        if ($(this).val().length > 50) {
            $(this).addClass('is-invalid');
            $('#new-insideIdHelp').addClass('text-danger');
        } else {
            $(this).removeClass('is-invalid');
            $('#new-insideIdHelp').removeClass('text-danger');
        }
    });
});

$(function() {
    const $area = $('#image-drop-area');
    const $input = $('#new-image');
    const $text = $('#image-drop-text');

    $area.on('click', function(e) {
        e.stopPropagation();
        $input.trigger('click');
    });

    $area.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $area.addClass('dragover');
    });

    $area.on('dragleave dragend', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $area.removeClass('dragover');
    });

    $area.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $area.removeClass('dragover');
        if (e.originalEvent.dataTransfer.files.length) {
            $input[0].files = e.originalEvent.dataTransfer.files;
            showPreview($input[0].files[0]);
        }
    });

    $input.on('change', function() {
        if (this.files && this.files[0]) {
            showPreview(this.files[0]);
        }
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            //limitar a imagem a 5MB
            const fileSize = file.size / 1024 / 1024; // tamanho em MB
            if (fileSize > 5) {
                alert(dictionary.imageSizeError);
                $input.val(''); // Limpa o input
                $area.find('img').remove();
                $text.show();
                return;
            }
            $area.find('img').remove();
            $area.append('<img src="' + e.target.result + '" alt="Preview">');
            $text.hide();
        };
        reader.readAsDataURL(file);
    }
});

$(document).on('click', '#create-product-btn', function() {

    var form = $('#form-new-product')[0];
    var formData = new FormData(form);

    if (!formData.get('name')) {
        alert(dictionary.validation.nameRequired);
        return;
    }

    $.ajax({
        url: '/stock/newProduct',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            location.href = $('#form-new-product').attr('action');
        } else {
            console.error(dictionary.validation.error, response.message);
        }
    }).fail(function (error) {
        console.error('An error occurred:', error);
    });
});

$('#importFile').on('change', function() {
    // Limitar extensões e tamanho do arquivo
    const file = this.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024;
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (fileSize > 2) {
            alert(dictionary.validation.fileSizeError);
            this.value = '';
        } else if (fileExtension !== 'csv') {
            alert(dictionary.validation.fileTypeError);
            this.value = '';
        }
    }
});


$(document).on('click', '#import-form-btn', function(e) {

    e.preventDefault();

    var form = $('#import-form')[0];
    var formData = new FormData(form);

    $.ajax({
        url: '/stock/importProducts',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            alert(response.message);
            location.href = $('#import-form').attr('action');
        } else {
            $('#importFileError').text(response.message);
            $('#importFileError').removeClass('d-none');
            // alert(response.message);
        }
    }).fail(function (error) {
        console.error('An error occurred:', error);
    });
});
    
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})