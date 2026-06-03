$('.moedaReal').inputmask('decimal', {
    alias: 'numeric',
    groupSeparator: '.',
    radixPoint: ',',
    autoGroup: true,
    digits: 2,
    digitsOptional: false,
    placeholder: '0',
    rightAlign: false,
    removeMaskOnSubmit: true // remove a máscara ao submeter o form
});

// $('#eventAdvantagesSelect').select2({
//     placeholder: "Selecione as vantagens do evento",
//     allowClear: true,
//     // dropdownParent: $('#newModal .modal-body'),
//     width: '100%',
// });

$('#eventCountry').select2({
    placeholder: translator.selectCountry,
    allowClear: true,
    width: '100%'
});

// DATAS

$('#addDateCard').on('click', function() {
    $('#newDateForm')[0].reset();
    $('#newDateModal').modal('show');
});

function criarCardData(day, time, endTime, observation) {

    if (!day) return;

    //Pegar a data e formatar para dd/mm/yyyy
    //Pegar também qual o dia da semana é essa data
    //E formatar as horas para hh:mm
    var dateObj = new Date(day);
    dateObj.setDate(dateObj.getDate() + 1);
    var options = { weekday: 'long', year: 'numeric', month: '2-digit', day: '2-digit' };
    var formattedDay = dateObj.toLocaleDateString(translator.language, options);
    var weekday = formattedDay.charAt(0).toUpperCase() + formattedDay.slice(1).split(',')[0];
    formattedDay = formattedDay.split(',')[1].trim();
    var formattedTime = time.slice(0, 5);
    var formattedEndTime = endTime.slice(0, 5);

    var existingCard = $(`#daysRow .date-card[data-date="${day}"]`);
    if (existingCard.length > 0) existingCard.remove();

    $('#daysRow').append(`
        <div class="col-xxl-4 col-xl-6 col-12 mb-3 date-card" data-date="${day}">
            <div class="card h-100 flex-column position-relative">
                <div class="p-3 pb-2">
                    <h6 class="mb-0">${weekday}</h6>
                    <button type="button" class="btn-close btn-close-card p-3" style="position: absolute; top: 0.1rem; right: 0;" aria-label="Close"></button>
                </div>
                <div class="card-body pt-0">
                    <h6 class="card-title">${formattedDay}</h6 >
                    <span class="card-subtitle mb-2 text-muted">${formattedTime} - ${formattedEndTime}</span>
                    <span class="btn btn-stellar-blue d-flex align-items-center mt-3 edit-date" data-date="${day}" >
                        <i class="fa-solid fa-pen-to-square ms-2 me-3" style="text-align: center;"></i> ${translator.edit}
                    </span>
                </div>
            </div>
            <input type="hidden" name="dates[${day}][day]" value="${day}">
            <input type="hidden" name="dates[${day}][time]" value="${time}">
            <input type="hidden" name="dates[${day}][endTime]" value="${endTime}">
            <input type="hidden" name="dates[${day}][observation]" value="${observation}">
        </div>
    `);

    var dates = $('#daysRow .date-card').get();
    dates.sort(function(a, b) {
        var dateA = new Date($(a).data('date'));
        var dateB = new Date($(b).data('date'));
        return dateA - dateB;
    });
    $.each(dates, function(idx, itm) { $('#daysRow').append(itm); });

}


$('#newDateForm').on('submit', function(e) {
    e.preventDefault();
    var day = $('#dateDay').val();
    var time = $('#dateTime').val();
    var endTime = $('#dateEndTime').val();
    var observation = $('#dateObservation').val();

    criarCardData(day, time, endTime, observation);
    
    $('#newDateModal').modal('hide');
    $('#newDateForm')[0].reset();
});

function createDateCardsFromExisting() {
    // Get GET parameters
    var urlParams = new URLSearchParams(window.location.search);
    var start = urlParams.get('start');
    //Adicionar um dia em start
    var end = urlParams.get('end');

    if (start) {
        var startDate = new Date(start);
        var endDate = end ? new Date(end) : startDate;
        for (var d = startDate; d <= endDate; d.setDate(d.getDate() + 1)) {
            var day = d.toISOString().split('T')[0];
            criarCardData(day, '00:00', '23:59', '');
        }
    }
}

createDateCardsFromExisting();

$(document).on('click', '.edit-date', function(e) {
    e.preventDefault();
    var dateId = $(this).data('date');
    var day = $('input[name="dates[' + dateId + '][day]"]').val();
    var time = $('input[name="dates[' + dateId + '][time]"]').val();
    var endTime = $('input[name="dates[' + dateId + '][endTime]"]').val();
    var observation = $('input[name="dates[' + dateId + '][observation]"]').val();
    $('#dateDay').val(day);
    $('#dateTime').val(time);
    $('#dateEndTime').val(endTime);
    $('#dateObservation').val(observation);
    $('#newDateModal').modal('show');
});

// TAXAS E CUSTOS

$('#addPriceCard').on('click', function() {
    $('#newPriceForm')[0].reset();
    $('#priceAmount').val('0,00');
    $('#priceOrder').val('');
    $('#newPriceModal').modal('show');
});

// Formulário de nova taxa cria uma nova carta de taxa
$('#newPriceForm').on('submit', function(e) {
    e.preventDefault();
    var name = $('#priceName').val();
    var amount = $('#priceAmount').val();
    var observation = $('#priceObservation').val();
    var currency = $('#eventCurrency').val();
    if ($('#priceOrder').val()) {
        var order = $('#priceOrder').val();
        $('input[name="prices[' + order + '][name]"]').val(name);
        $('.card-title[data-price="' + order + '"]').text(name);
        $('input[name="prices[' + order + '][amount]"]').val(amount);
        $('.card-subtitle[data-price="' + order + '"]').text(`<span class="priceCurrency">${currency}</span> ${amount}`);
        $('input[name="prices[' + order + '][observation]"]').val(observation);
    } else {
        var order = $('#pricesRow .card-price-item').length;
        while ($('div.card-price-item[data-price="' + order + '"]').length) order++;
        var newPriceCard = `
            <div class="col-xxl-4 col-xl-6 col-12 mb-3 card-price-item" data-price="${order}">
                <div class="card h-100 flex-column position-relative">
                    <div class="d-flex justify-content-between align-items-center p-3 pb-2">
                        <i class="fa-solid fa-arrows-up-down-left-right color-cloud-gray"></i>
                        <button type="button" class="btn-close btn-close-card" aria-label="Close"></button>
                    </div>
                    <div class="card-body d-flex flex-column pt-0">
                        <div class="flex-grow-1">
                            <h5 class="card-title" data-price="${order}">${name}</h5>
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted color-klikit-2" data-price="${order}"><span class="priceCurrency">${currency}</span> ${amount}</h6>
                        <span class="btn btn-stellar-blue d-flex align-items-center mt-3 edit-price" data-price="${order}" >
                            <i class="fa-solid fa-pen-to-square ms-2 me-3" style="text-align: center;"></i> ${translator.edit}
                        </span>
                    </div>
                </div>
                <input type="hidden" name="prices[${order}][name]" value="${name}">
                <input type="hidden" name="prices[${order}][amount]" value="${amount}">
                <input type="hidden" name="prices[${order}][observation]" value="${observation}">
                <input type="hidden" name="prices[${order}][order]" value="${order}">
            </div>
        `;
        $('#pricesRow').append(newPriceCard);
    }
    $('#newPriceModal').modal('hide');
    $('#newPriceForm')[0].reset();
    $('#priceAmount').val('0,00');
    $('#priceOrder').val('');
});

$(document).on('click', '.btn-close-card', function(e) {
    e.preventDefault();
    //.card-price-item OU .date-card
    $(this).closest('.card-price-item, .date-card').fadeOut(300, function() {
        $(this).remove();
    });
});

$(document).on('click', '.edit-price', function(e) {
    e.preventDefault();
    var priceId = $(this).data('price');
    var name = $('input[name="prices[' + priceId + '][name]"]').val();
    var amount = $('input[name="prices[' + priceId + '][amount]"]').val();
    var observation = $('input[name="prices[' + priceId + '][observation]"]').val();
    $('#priceOrder').val(priceId);
    $('#priceName').val(name);
    $('#priceAmount').val(amount);
    $('#priceObservation').val(observation);
    $('#newPriceModal').modal('show');
});

new Sortable(document.getElementById('pricesRow'), {
    animation: 150,
    ghostClass: 'ghost',
    chosenClass: 'chosen',
    filter: '.unsortable',
    onMove: (evt) => {
        evt.target.classList.add('grabbing');
        return !(evt.related && evt.related.classList.contains('unsortable') && !evt.willInsertAfter);
    },
    onChoose: function(e) {
        e.target.classList.add('grabbing');
    },
    onUnchoose: function(e) {
        e.target.classList.remove('grabbing');
    },
    onStart: function(e) {
        e.target.classList.add('grabbing');
    },
    onEnd: function(e) {
        e.target.classList.remove('grabbing');
        var items = document.querySelectorAll('#pricesRow .card-price-item');
        items.forEach((item, index) => {
            var priceId = item.getAttribute('data-price');
            var orderInput = item.querySelector('input[name="prices[' + priceId + '][order]"]');
            orderInput.value = index;
        });
    }
});

$('#eventCountry').on('change', function() {
    var currency = $(this).find('option:selected').data('currency');
    $('#eventCurrency').val(currency);
    $('.priceCurrency').text(currency);
});

function searchCountries() {
        $.ajax({
        url: `https://restcountries.com/v3.1/all?fields=name,currencies,cca2`,
        type: 'GET',
        success: async function(response) {
            response.sort((a, b) => a.name.common.localeCompare(b.name.common));
            var select = document.getElementById('eventCountry');
            response.forEach(country => {
                var option = document.createElement('option');
                let countryName = null;
                if (country.name.nativeName && Object.keys(country.name.nativeName).length > 0) {
                    countryName = country.name.nativeName[Object.keys(country.name.nativeName)[0]].common;
                } else {                     
                    countryName = country.name.common;
                }
                option.text = countryName;
                option.value = countryName;
                // option.value = country.cca2;
                if (country.currencies && Object.keys(country.currencies).length > 0) {
                    var currencyCode = country.currencies[Object.keys(country.currencies)[0]].symbol;
                    option.setAttribute('data-currency', currencyCode);
                } else {
                    option.setAttribute('data-currency', '');
                }
                select.appendChild(option);
                //Selecionar o país do usuário com base em dados do navegador
            });
            $('#eventCountry').select2({
                placeholder: translator.selectCountry,
                allowClear: true,
                width: '100%'
            });
        },
        error: async function(error) {
            console.log('An error occurred');
        }
    });
}

searchCountries();


$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});


$('#eventForm').on('submit', function(e) {
    e.preventDefault();
    if (!$('#eventTitle').val()) {
        alert('O título do evento é obrigatório.');
        return;
    };
    var form = $('#eventForm')[0];
    var formData = new FormData(form);
    //Pegar os dados do form
        $.ajax({
        url: '/events/create',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            window.location.href = `/events/id/${response.data.eventId}`;
        }
    }).fail(function (error) {
        // atualizarToast('myToast', 'Erro ao registrar venda', 'Ocorreu um erro ao tentar registrar a venda. Por favor, tente novamente.', false);
        // Update toast content for error
    });
});