const selecionados = new Set(); // <- Aqui guardamos os IDs dos produtos selecionados

$(document).ready(function () {

    function atualizarValorTotal() {
        let total = 0;
        let descontoTotal = 0;
        $('#selected .card-product').each(function () {
            let id = $(this).data('id');
            let valor = parseFloat($(this).find('#total-price-' + id).val().replace(/\./g, '').replace(',', '.'));
            let desconto = parseFloat($(this).find('#discount-' + id).val().replace(/\./g, '').replace(',', '.'));
            if (!isNaN(valor)) {
                total += valor;
            }
            if (!isNaN(desconto)) {
                descontoTotal += desconto;
            }
        });
        $('#total-price').text(total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#total-input').val(total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#total-discount-input').val(descontoTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#total-price-modal').text(total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    //O que está errado aqui?
    
    function somarPrecos() {
        let somaPrecos = 0;
        $('#selected .card-product').each(function () {
            let id = $(this).data('id');
            let precoUnitario = parseFloat($(this).find('#base-price-' + id).val().replace(/\./g, '').replace(',', '.'));
            let quantidade = parseInt($(this).find('#qtd-items-' + id).val());
            somaPrecos += precoUnitario * quantidade;
        });
        return somaPrecos;
    }

    function repartirTotalEntreProdutos(total) {
        let somaPrecos = somarPrecos();
        let totalCalculado = 0;
        let ultimoId = null;
        let descontoTotal = 0;

        $('#selected .card-product').each(function () {
            let id = $(this).data('id');
            ultimoId = id;
            let precoUnitario = parseFloat($(this).find('#base-price-' + id).val().replace(/\./g, '').replace(',', '.'));
            let quantidade = parseInt($(this).find('#qtd-items-' + id).val());
            let proporcao = (precoUnitario * quantidade) / somaPrecos;
            let novoTotal = Math.round((total * proporcao) * 100) / 100;
            totalCalculado += novoTotal;
            let desconto = ((precoUnitario * quantidade) - novoTotal);
            descontoTotal += desconto;
            desconto = desconto.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            $(this).find('#discount-' + id).val(desconto);
            $(this).find('#total-price-' + id).val(novoTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        });

        if (ultimoId !== null) {
            let diferenca = total - totalCalculado;
            let valorAtual = parseFloat($(`#total-price-${ultimoId}`).val().replace(/\./g, '').replace(',', '.'));
            let novoValor = valorAtual + diferenca;
            let precoUnitario = parseFloat($(`#base-price-${ultimoId}`).val().replace(/\./g, '').replace(',', '.'));
            let quantidade = parseInt($(`#qtd-items-${ultimoId}`).val());
            let desconto = ((precoUnitario * quantidade) - novoValor);
            let descontoAtual = parseFloat($(`#discount-${ultimoId}`).val().replace(/\./g, '').replace(',', '.'));
            descontoTotal -= descontoAtual;
            descontoTotal += desconto;
            desconto = desconto.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            $(`#discount-${ultimoId}`).val(desconto);
            $(`#total-price-${ultimoId}`).val(novoValor.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
        return descontoTotal;
    }

    $('#total-input').on('input keyup', function () {
        let total = $(this).val();
        $('#total-price-modal').text(total);
        total = total ? parseFloat(total.replace(/\./g, '').replace(',', '.')) : 0;
        desconto = repartirTotalEntreProdutos(total);
        desconto = desconto.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}); 
       $('#total-discount-input').val(desconto);
    });

    $('#total-discount-input').on('input keyup', function () {
        let descontoTotal = $(this).val();
        descontoTotal = descontoTotal ? parseFloat(descontoTotal.replace(/\./g, '').replace(',', '.')) : 0;
        let somaPrecos = somarPrecos();
        let novoTotal = somaPrecos - descontoTotal;
        repartirTotalEntreProdutos(novoTotal);
        novoTotal = novoTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}); 
        $('#total-price-modal').text(novoTotal);
        $('#total-input').val(novoTotal);  
    });

    $('#search').on('input keyup', atualizarSugestoes);

    function atualizarSugestoes() {
        const termo = $('#search').val().toLowerCase();
        $('#suggestions').empty();

        if (termo.length === 0) return;

        const resultados = produtos.filter(p =>
            (
                p.nome.toLowerCase().includes(termo) || 
                p.subtitulo.toLowerCase().includes(termo) ||
                p.palavra_chave.toLowerCase().includes(termo) ||
                p.descricao.toLowerCase().includes(termo)
            ) && !selecionados.has(p.id)
        );

        // Se resultados for vazio, o botão é de "criar produto"
        if (resultados.length > 0) {
            resultados.forEach(prod => {
                const item = $(`
                <div class="row suggestion-item">
                    <div class="col-3">
                        <img src="${prod.imagem}" alt="${prod.nome}">
                    </div>
                    <div class="col-6 card-body py-3">
                        <span class="mb-1">${prod.nome}</span>
                        <p class="card-text mb-1"><small class="text-muted">${prod.subtitulo}</small></p>
                    </div>
                    <div class="col-3 text-end">
                        <span class="card-text fw-bold text-success">${currency} ${prod.total}</span>
                        <p class="card-text mb-1"><small class="text-muted">${prod.estoque} uni</small></p>
                    </div>
                </div>
                `);

                item.on('click', function () {
                    adicionarProduto(prod);
                    atualizarSugestoes();
                });

                $('#suggestions').append(item);
            });
        }           
        const noResult = $(`
            <div class="row suggestion-item">
                <div class="col-12 text-center">${dictionary.noResults[0]}<br>${dictionary.noResults[1]}</div>
            </div>
        `);

        $('#suggestions').append(noResult);

        noResult.on('click', function () {
            $('#product-name').val(termo);
            $('#new-price').val('0,00');
            $('#new-discount').val('0,00');
            $('#saleInsertNewProduct').modal('show');
        });

    }

    $('#insert-new-product').on('click', function () {
        $('#saleInsertNewProduct').modal('hide');
        let nome = $('#product-name').val();
        let preco = $('#new-price').val();
        let desconto = $('#new-discount').val();
        let precoNum = preco ? parseFloat(preco.replace(/\./g, '').replace(',', '.')) : 0;
        let descontoNum = desconto ? parseFloat(desconto.replace(/\./g, '').replace(',', '.')) : 0;
        let total = precoNum - descontoNum;
        adicionarProduto({
            id: `new-${Date.now()}`, // ID temporário único
            imagem: '/assets/image/200x300.png',
            nome: nome,
            subtitulo: 'Produto novo adicionado rapidamente',
            nome: nome,
            preco: preco,
            desconto: desconto,
            total: total,
            estoque: 0,
        }, true);
    });

    function adicionarProduto(prod, newProduct = false) {
        if (selecionados.has(prod.id)) return;

        selecionados.add(prod.id);

        const card = $(`
            <div class="col-12 mb-3">
                <div class="card card-product shadow-sm p-0" data-id="${prod.id}">
                    <input type="hidden" name="items[${prod.id}][id]" value="${prod.id}">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-2 col-12">
                            <img src="${prod.imagem}" class="product-img m-3" alt="${prod.nome}">
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="card-body py-3">
                                <input type="hidden" name="items[${prod.id}][name]" value="${prod.nome}">
                                <input type="hidden" name="items[${prod.id}][subtitle]" value="${prod.subtitulo}">
                                <h5 class="card-title mb-1">${prod.nome}</h5>
                                <p class="card-text mb-1"><small class="text-muted">${prod.subtitulo}</small></p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="card-body py-3">
                                <span class="card-text fw-bold text-success">${currency} ${prod.preco}</span>
                                <p class="card-text mb-1"><small class="text-muted">${prod.estoque} ${dictionary.uni}</small></p>
                            </div>
                        </div>
                        <div class="col-md-1 col-12 text-end">
                            <button type="button" class="btn-close btn-remove" aria-label="${dictionary.remove}"></button>
                        </div>
                    </div>
                    <div class="row g-0 align-items-center mb-3">
                        <div class="col-md-4 col-12 align-items-center px-3">
                            <label for="qtd-items-${prod.id}" class="">${dictionary.quantity}</label>
                            <div class="d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-danger qtd" data-id="${prod.id}" data-action="decrement">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input id="stock-${prod.id}" type="hidden" value="${prod.estoque}">
                                <input id="qtd-items-${prod.id}" name="items[${prod.id}][qtd]" type="text" class="form-control mx-2" value="1" min="1" readonly>
                                <button type="button" class="btn btn-sm btn-success qtd" data-id="${prod.id}" data-action="increment">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 my-md-0 my-3 align-items-center px-3">
                            <label for="discount-${prod.id}" class="">${dictionary.discount} (${currency})</label>
                            <div class="text-end">
                                <input id="discount-${prod.id}" name="items[${prod.id}][discount]" type="text" class="form-control moedaReal input-stellar-blue" value="${prod.desconto}">
                            </div>
                        </div>
                        <div class="col-md-4 col-12 align-items-center px-3">
                            <label for="total-price-${prod.id}" class="">${dictionary.value} (${currency})</label>
                            <div class="text-end">
                                <input id="base-price-${prod.id}" name="items[${prod.id}][base_price]" type="hidden" value="${prod.preco}">
                                <input id="total-price-${prod.id}" name="items[${prod.id}][total_price]" type="text" class="form-control moedaReal input-stellar-blue" value="${prod.total}">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="items[${prod.id}][new_product]" value="${newProduct ? '1' : '0'}">
            </div>
        `);

        card.find('.btn-remove').on('click', function () {
            selecionados.delete(prod.id);
            card.remove();
            atualizarSugestoes();
            atualizarValorTotal();
        });

        card.find('.qtd').on('click', function () {
            const action = $(this).data('action');
            const id = $(this).data('id');
            const input = $(`#qtd-items-${id}`);
            let currentValue = parseInt(input.val());
            let previousValue = currentValue;
            if (action === 'increment') {
                currentValue += 1;
            } else if (action === 'decrement' && currentValue > 1) {
                currentValue -= 1;
            }
            input.val(currentValue);
            verificarEstoque(id);
            atualizarDesconto(id, previousValue, currentValue);
            atualizarTotal(id);
            atualizarValorTotal();
        });

        function verificarEstoque(id) {
            const estoque = parseInt($(`#stock-${id}`).val());
            const quantidade = parseInt($(`#qtd-items-${id}`).val());
            if (quantidade > estoque) {
                $(`#qtd-items-${id}`).addClass('is-invalid');
            } else {
                $(`#qtd-items-${id}`).removeClass('is-invalid');
            }
        }

        function atualizarDesconto(id, previousValue, currentValue) {

            let desconto = $(`#discount-${id}`).val();
            desconto = desconto ? parseFloat(desconto.replace(/\./g, '').replace(',', '.')) : 0;

            let descontoTotal = (desconto / previousValue) * currentValue;
            $(`#discount-${id}`).val(descontoTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }


        function atualizarTotal(id) {
            const quantidade = parseInt($(`#qtd-items-${id}`).val());

            let precoUnitario = $(`#base-price-${id}`).val();
            precoUnitario = precoUnitario ? parseFloat(precoUnitario.replace(/\./g, '').replace(',', '.')) : 0;

            let desconto = $(`#discount-${id}`).val();
            desconto = desconto ? parseFloat(desconto.replace(/\./g, '').replace(',', '.')) : 0;

            const total = ((precoUnitario * quantidade) - desconto).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            $(`#total-price-${id}`).val(total);
        }

        // Keep adding continuously on mousedown
        card.find('.qtd').on('mousedown', function () {
            const id = $(this).data('id');
            const input = $(`#qtd-items-${id}`);
            let previousValue = parseInt(input.val());
            let currentValue = parseInt(input.val());
            const action = $(this).data('action');

            const interval = setInterval(function () {
                previousValue = currentValue;
                if (action === 'increment') {
                    currentValue += 1;
                } else if (action === 'decrement' && currentValue > 1) {
                    currentValue -= 1;
                }
                input.val(currentValue);
                verificarEstoque(id);
                atualizarDesconto(id, previousValue, currentValue);
                atualizarTotal(id);
                atualizarValorTotal();
            }, 100);

            $(document).on('mouseup', function () {
                clearInterval(interval);
            });
        });

        card.find('#discount-' + prod.id).on('input keyup', function () {
            const id = prod.id;
            let desconto = $(this).val();
            desconto = desconto ? parseFloat(desconto.replace(/\./g, '').replace(',', '.')) : 0; 
            atualizarTotal(id);
            atualizarValorTotal();
        });

        card.find('#total-price-' + prod.id).on('input keyup', function () {
            const id = prod.id;
            let total = $(this).val();
            total = total ? parseFloat(total.replace(/\./g, '').replace(',', '.')) : 0;
            const precoUnitario = parseFloat($(`#base-price-${id}`).val().replace(/\./g, '').replace(',', '.'));
            const quantidade = parseInt($(`#qtd-items-${id}`).val());
            const desconto = (precoUnitario * quantidade) - total;
            $(`#discount-${id}`).val(desconto.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            atualizarValorTotal();
        });

        card.find('.moedaReal').inputmask({
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

        $('#selected').append(card);
        verificarEstoque(prod.id);
        atualizarValorTotal();
    }

});

$(document).on('click', '#finalizar-venda', function () {
    if ($('#selected .card-product').length === 0) {
        alert(dictionary.alert);
        return;
    }
    // #sale-datetime -> atualiza-se a data e hora da venda (pegar do dispositivo do usuário)
    var horaAtual = new Date();
    var ano = horaAtual.getFullYear();
    var mes = String(horaAtual.getMonth() + 1).padStart(2, '0');
    var dia = String(horaAtual.getDate()).padStart(2, '0');
    var horas = String(horaAtual.getHours()).padStart(2, '0');
    var minutos = String(horaAtual.getMinutes()).padStart(2, '0');
    var dataHoraFormatada = `${ano}-${mes}-${dia}T${horas}:${minutos}`;
    $('#sale-datetime').val(dataHoraFormatada); 
    $('#insertModal').modal('show');
});

$('#new-sale-form').on('submit', function (e) {
    e.preventDefault();
});

$(document).on('click', '#accept-insert', function () {

    const formData = new FormData($('#new-sale-form')[0]);

    $.ajax({
        url: '/sales/insert',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            $('#sale-id').text(response.data.numero);
            $('#insertModal').modal('hide');
            $('#saleInsertedModal').modal('show');
        } else {
            console.error(dictionary.error.title + ':', response.message);
        }
    }).fail(function (error) {
        atualizarToast('myToast', dictionary.error.title, dictionary.error.message, false);
        // Update toast content for error

    });
});

$('.moedaReal').inputmask({
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