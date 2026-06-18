$(document).ready(function() {
    $('#storeCountry').select2({
        placeholder: translator.selectCountry,
        allowClear: true,
        width: '100%'
    });

    function searchCountries() {
        $.ajax({
            url: 'https://restcountries.com/v3.1/all?fields=name,currencies,cca2',
            type: 'GET',
            success: function(response) {
                response.sort((a, b) => a.name.common.localeCompare(b.name.common));
                var select = document.getElementById('storeCountry');
                response.forEach(country => {
                    let countryName = null;
                    if (country.name.nativeName && Object.keys(country.name.nativeName).length > 0) {
                        countryName = country.name.nativeName[Object.keys(country.name.nativeName)[0]].common;
                    } else {
                        countryName = country.name.common;
                    }
                    let currencies = country.currencies ? Object.values(country.currencies) : [];
                    currencies.forEach(currency => {
                        var option = document.createElement('option');
                        if (currency.symbol === $('#storeCurrency').val() && countryName === $('#storeCountry').attr('data-default')) {
                            option.selected = true;
                        }
                        option.text = countryName + ' (' + currency.symbol + ')';
                        option.value = countryName;
                        option.setAttribute('data-currency', currency.symbol);
                        select.appendChild(option);
                    });
                });
                $('#storeCountry').select2({
                    placeholder: translator.selectCountry,
                    allowClear: true,
                    width: '100%'
                });
            },
            error: function() {
                console.log('An error occurred');
            }
        });
    }

    initImageDropArea('#store-photo-drop-area', '#store-photo-input', '#store-photo-drop-text');
    initImageDropArea('#store-banner-drop-area', '#store-banner-input', '#store-banner-drop-text');
    searchCountries();

    $(document).on('submit', '#settings-store-form', function(event) {
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                $('#toastTitle').text($('#settings-store-form').attr('data-success'));
                $('#toastBody').text(response.message);
                $('#myToast').removeClass('bg-danger').addClass('bg-success');
                var myToast = new bootstrap.Toast(document.getElementById('myToast'));
                myToast.show();
            } else {
                $('#toastTitle').text($('#settings-store-form').attr('data-error'));
                $('#toastBody').text(response.message || 'Erro ao salvar as alterações.');
                $('#myToast').removeClass('bg-success').addClass('bg-danger');
                var myToast = new bootstrap.Toast(document.getElementById('myToast'));
                myToast.show();
            }
        }).fail(function() {
            $('#toastTitle').text($('#settings-store-form').attr('data-error'));
            $('#toastBody').text('Erro ao salvar as alterações.');
            $('#myToast').removeClass('bg-success').addClass('bg-danger');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        });
    });
});

$('#storeCountry').on('change', function() {
    var currency = $(this).find('option:selected').data('currency');
    $('#storeCurrency').val(currency);
});