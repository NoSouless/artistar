$(document).ready(function() {
    $('#category').select2({
        tags: true,
        placeholder: $('#category').attr('data-placeholder'),
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return $('#category').attr('data-noResults');
            }
        }
    });
    $('#keywords').select2({
        tags: true,
        placeholder: $('#keywords').attr('data-placeholder'),
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return $('#keywords').attr('data-noResults');
            }
        }
    });
});

$(function() {
    const $area = $('#image-drop-area');
    const $input = $('#image');
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
            const fileSize = file.size / 1024 / 1024;
            if (fileSize > 1) {
                alert($('#image').attr('data-sizeError'));
                $input.val('');
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

     $('.moedaReal').inputmask({
      alias: 'numeric',
      groupSeparator: '.',
      radixPoint: ',',
      autoGroup: true,
      digits: 2,
      digitsOptional: false,
      placeholder: '0',
      rightAlign: false,
      removeMaskOnSubmit: true
    });

    function calcularLucro() {
        let preco = $('#price').val();
        let desconto = $('#discount').val();
        let custo = $('#cost').val();

        // Remove apenas pontos de milhares e troca vírgula por ponto
        preco = preco ? parseFloat(preco.replace(/\./g, '').replace(',', '.')) : 0;
        desconto = desconto ? parseFloat(desconto.replace(/\./g, '').replace(',', '.')) : 0;
        custo = custo ? parseFloat(custo.replace(/\./g, '').replace(',', '.')) : 0;

        let lucro = preco - (custo + desconto);
        let precoAtual = preco - desconto;
        let descontoPercentual = 0;
        if (preco > 0) descontoPercentual = (desconto * 100) / preco;

        let lucroFormatado = lucro.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        let precoAtualFormatado = precoAtual.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        let descontoPercentualFormatado = descontoPercentual.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        $('#real_price').val(precoAtualFormatado);
        $('#discount_percentage').val(descontoPercentualFormatado);
        $('#margin').val(lucroFormatado);
    }

    $('#price, #discount, #cost').on('input keyup change', calcularLucro);

    function calculaCaracteres(input, help, count) {
        $(count).text($(input).val().length);
        if ($(input).val().length > 50) {
            $(input).addClass('is-invalid');
            $(help).addClass('text-danger');
        } else {
            $(input).removeClass('is-invalid');
            $(help).removeClass('text-danger');
        }
    }

    $('#name').on('input keyup change', function() {
        calculaCaracteres(this, '#nameHelp', '#nameCount');
    });

    $('#insideId').on('input keyup change', function() {
        calculaCaracteres(this, '#insideIdHelp', '#insideIdCount');
    });

    calcularLucro();
    calculaCaracteres($('#name'), '#nameHelp', '#nameCount');
    calculaCaracteres($('#insideId'), '#insideIdHelp', '#insideIdCount');
});

$(document).on('click', '#create-product-btn, #create-product-btn-2', function() {

    event.preventDefault();

    var form = $('#product-details-form')[0];
    var formData = new FormData(form);

    if (!formData.get('name')) {
        alert($('#name').attr('data-empty'));
        return;
    }

    formData.append('productId', productId);

    $.ajax({
        url: '/stock/product/alter',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            $('#toastTitle').text($('#product-details-form').attr('data-success'));
            $('#toastBody').text(response.message);
            $('#myToast').removeClass('bg-danger')
            $('#myToast').addClass('bg-success');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        } else {
            console.error('Erro ao salvar as alterações do produto:', response.message);
            $('#toastTitle').text($('#product-details-form').attr('data-error'));
            $('#toastBody').text(dictionary.error + ': ' + response.message);
            $('#myToast').removeClass('bg-success');
            $('#myToast').addClass('bg-danger');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        }
    }).fail(function (error) {
        console.error(dictionary.error + ':', error);
        $('#toastTitle').text($('#product-details-form').attr('data-error'));
        $('#toastBody').text(dictionary.saveProductError);
        $('#myToast').removeClass('bg-success');
        $('#myToast').addClass('bg-danger');
        var myToast = new bootstrap.Toast(document.getElementById('myToast'));
        myToast.show();
    });
});
    
$(document).on('click', '#accept-duplicate', function() {
    $.ajax({
        url: '/stock/product/duplicate',
        type: 'POST',
        data: { productId: productId },
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            window.location.href = '/stock/product/' + response.data.newProductId;
        } else {
            console.error(dictionary.duplicateProductError.title+':', response.message);
            $('#toastTitle').text(dictionary.duplicateProductError.title);
            $('#toastBody').text(dictionary.duplicateProductError.body);
            $('#myToast').removeClass('bg-success');
            $('#myToast').addClass('bg-danger');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        }
    }).fail(function (error) {
        console.error(dictionary.error + ':', error);
        $('#toastTitle').text(dictionary.duplicateProductError.title);
        $('#toastBody').text(dictionary.duplicateProductError.body);
        $('#myToast').removeClass('bg-success');
        $('#myToast').addClass('bg-danger');
        var myToast = new bootstrap.Toast(document.getElementById('myToast'));
        myToast.show();
    });
});

$(document).on('click', '#accept-delete', function() {
    $.ajax({
        url: '/stock/product/delete',
        type: 'POST',
        data: { productId: productId }
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            alert(dictionary.deleteProduct.success);
            window.location.href = '/stock';
        } else {
            console.error('Erro ao excluir o produto:', response.message);
            $('#toastTitle').text(dictionary.deleteProduct.error.title);
            $('#toastBody').text(dictionary.deleteProduct.error.body);
            $('#myToast').removeClass('bg-success');
            $('#myToast').addClass('bg-danger');
        }
        var myToast = new bootstrap.Toast(document.getElementById('myToast'));
        myToast.show();
    }).fail(function (error) {
        console.error('Ocorreu um erro:', error);
        $('#toastTitle').text(dictionary.deleteProduct.error.title);
        $('#toastBody').text(dictionary.deleteProduct.error.body);
        $('#myToast').removeClass('bg-success');
        $('#myToast').addClass('bg-danger');
        var myToast = new bootstrap.Toast(document.getElementById('myToast'));
        myToast.show();
    });
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})