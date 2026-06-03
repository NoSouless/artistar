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
    var formattedDay = dateObj.toLocaleDateString('pt-BR', options);
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

function criarCardPreco(order, name, amount, observation, currency = '') {
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

// Formulário de nova taxa cria uma nova carta de taxa
$('#newPriceForm').on('submit', function(e) {
    e.preventDefault();
    var name = $('#priceName').val();
    var amount = $('#priceAmount').val();
    var observation = $('#priceObservation').val();
    if ($('#priceOrder').val()) {
        var order = $('#priceOrder').val();
        $('input[name="prices[' + order + '][name]"]').val(name);
        $('.card-title[data-price="' + order + '"]').text(name);
        $('input[name="prices[' + order + '][amount]"]').val(amount);
        $('.card-subtitle[data-price="' + order + '"]').text($('#eventCurrency').val() + ' ' + amount);
        $('input[name="prices[' + order + '][observation]"]').val(observation);
    } else {
        var order = $('#pricesRow .card-price-item').length;
        while ($('div.card-price-item[data-price="' + order + '"]').length) order++;
        criarCardPreco(order, name, amount, observation, $('#eventCurrency').val());
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

// async function searchCEP() {
//     var cep = document.getElementById('eventCep').value.replace(/\D/g, '');
//     $.ajax({
//         url: `https://viacep.com.br/ws/${cep}/json/`,
//         type: 'GET',
//         success: async function(response) {
//             console.log(response);
//             if (!response.erro) {
//                 document.getElementById('eventAddress').value = response.logradouro;
//                 document.getElementById('eventNeighborhood').value = response.bairro;
//                 document.getElementById('eventState').value = response.uf;
//                 await searchCities(response.uf, response.localidade);
//             }
//         },
//         error: async function(error) {
//             console.log('An error occurred');
//         }
//     });
// }

// document.getElementById('eventCep').addEventListener('blur', async function() {
//     await searchCEP();
// });

// async function searchStates($defaultState = '') {
//     var select = document.getElementById('eventState');
//     select.innerHTML = '<option value="">Carregando...</option>';
//     $.ajax({
//         url: '/apis/states',
//         type: 'POST',
//         success: async function(response) {
//             response = JSON.parse(response);
//             if (response.code == 200) {
//                 select.innerHTML = '';
//                 states = response.data.states;
//                 select.innerHTML = '<option value="">Selecione um estado</option>';
//                 states.forEach(state => {          
//                     var option = document.createElement('option');
//                     option.value = state.sigla;
//                     option.text = state.nome;
//                     if (state.sigla === $defaultState) {
//                         option.selected = true;
//                     }
//                     select.appendChild(option);
//                 });
//             }
//         },
//         error: async function(error) {
//             console.log('An error occurred');
//         }
//     });
// }

// searchStates($('#eventState').attr('data-default'));

// async function searchCities(uf, defaultCity = '') {
//     var select = document.getElementById('eventCity');
//     select.innerHTML = '<option value="">Carregando...</option>';
//     $.ajax({
//         url: '/apis/cities',
//         type: 'POST',
//         data: {uf: uf},
//         success: async function(response) {
//             response = JSON.parse(response);
//             if (response.code == 200) {
//                 select.innerHTML = '';
//                 cities = response.data.cities;
//                 select.innerHTML = '<option value="">Selecione uma cidade</option>';
//                 cities.forEach(city => {          
//                     var option = document.createElement('option');
//                     if (city.nome == defaultCity) option.selected = true;
//                     option.value = city.nome;
//                     option.text = city.nome;
//                     select.appendChild(option);
//                 });
//             }
//         },
//         error: async function(error) {
//             console.log('An error occurred');
//         }
//     });
// }

// document.getElementById('eventState').addEventListener('change', async function() {
//     var uf = this.value;
//     searchCities(uf); 
// });

// searchCities($('#eventState').attr('data-default'), $('#eventCity').attr('data-default'));

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
                if (countryName === $('#eventCountry').attr('data-default')) {
                    option.selected = true;
                }
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
        url: '/events/update',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            atualizarToast('myToast', translator.toastSuccessTitle, translator.toastSuccessMessage, true);
        }
    }).fail(function (error) {
        atualizarToast('myToast', translator.toastErrorTitle, translator.toastErrorMessage, false);
    });
});

function criarCardsDataEmLote(datas) {
    datas.forEach(data => {
        let day = data.evento_data_dia;
        let time = data.evento_data_hora_inicial;
        let endTime = data.evento_data_hora_final;
        let observation = data.evento_data_observacao;
        criarCardData(day, time, endTime, observation);
    });
}

function criarCardsPrecosEmLote(precos) {
    precos.forEach(preco => {
        let order = preco.evento_taxa_ordem;
        let name = preco.evento_taxa_titulo;
        let amount = preco.evento_taxa_valor;
        //Formatar o valor para moeda brasileira
        amount = parseFloat(amount).toFixed(2).replace('.', ',');
        let observation = preco.evento_taxa_observacao;
        criarCardPreco(order, name, amount, observation, $('#eventCurrency').val());
    });
}