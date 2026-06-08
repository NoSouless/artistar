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

    function initImageDropArea(areaSelector, inputSelector, textSelector) {
        const $area = $(areaSelector);
        const $input = $(inputSelector);
        const $text = $(textSelector);

        if (!$area.length || !$input.length) {
            return;
        }

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
            const fileSize = file.size / 1024 / 1024;
            if (fileSize > 5) {
                alert($input.attr('data-size-error'));
                $input.val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                $area.find('img').remove();
                $area.append('<img src="' + event.target.result + '" alt="Preview">');
                if ($text.length) {
                    $text.hide();
                }
            };
            reader.readAsDataURL(file);
        }
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