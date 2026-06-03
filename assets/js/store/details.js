(function () {
    var productsList = document.getElementById('storeProductsList');
    var searchInput = document.getElementById('storeSearchInput');
    var followButton = document.querySelector('.store-follow-btn[data-store-id]');

    if (!productsList) return;

    var storeId = parseInt(productsList.dataset.storeId || '0', 10);
    var searchTimeout = null;
    var activeRequest = null;

    function parseApiResponse(response) {
        if (response && typeof response === 'object') return response;
        if (typeof response !== 'string') return null;

        var trimmed = response.trim();
        try {
            return JSON.parse(trimmed);
        } catch (e) {
            var firstBrace = trimmed.indexOf('{');
            var lastBrace = trimmed.lastIndexOf('}');
            if (firstBrace === -1 || lastBrace === -1 || lastBrace <= firstBrace) return null;

            try {
                return JSON.parse(trimmed.substring(firstBrace, lastBrace + 1));
            } catch (err) {
                return null;
            }
        }
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCurrencyBRL(value) {
        var number = Number(value);
        if (!isFinite(number)) return 'R$ 0,00';
        return 'R$ ' + number.toFixed(2).replace('.', ',');
    }

    function buildPlaceholderCards(total) {
        var html = '';
        for (var i = 0; i < total; i++) {
            html += '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento">';
            html += '  <div class="card h-100 d-flex flex-column product-card is-placeholder">';
            html += '      <div class="card-img-top position-relative pt-2 px-2">';
            html += '          <div class="store-product-image placeholder-glow thumbnail-product" style="display:block; width:100%;">';
            html += '              <span class="placeholder w-100 h-100 d-block"></span>';
            html += '          </div>';
            html += '      </div>';
            html += '      <div class="card-body d-flex flex-column">';
            html += '          <h5 class="card-title d-flex justify-content-between align-items-center">';
            html += '              <span class="color-stellar-blue nome-produto">Carregando...</span>';
            html += '              <span class="badge bg-lavanda">Ativo</span>';
            html += '          </h5>';
            html += '          <p class="card-text mt-auto">';
            html += '              <span class="badge bg-light text-dark me-1"></span>';
            html += '          </p>';
            html += '          <div class="card-text">';
            html += '              <div class="d-flex justify-content-between align-items-center">';
            html += '                  <div>';
            html += '                      <span>Preco Base:</span><br>';
            html += '                      <span class="badge bg-light text-dark me-1">R$ 0,00</span>';
            html += '                  </div>';
            html += '                  <div>';
            html += '                      <span>Preco Atual:</span> <br>';
            html += '                      <div class="text-end">';
            html += '                          <span class="badge bg-light text-dark me-1">R$ 0,00</span>';
            html += '                      </div>';
            html += '                  </div>';
            html += '              </div>';
            html += '          </div>';
            html += '          </p>';
            html += '      </div>';
            html += '  </div>';
            html += '</div>';
        }
        return html;
    }

    function renderProducts(products) {
        if (!Array.isArray(products) || products.length === 0) {
            productsList.innerHTML = [
                '<div class="col-12">',
                '   <div class="alert alert-light border text-center mb-0">',
                '       Nenhum produto encontrado na loja.',
                '   </div>',
                '</div>'
            ].join('');
            return;
        }

        var html = '';
        products.forEach(function (product) {
            var productId = parseInt(product.id || 0, 10);
            var name = escapeHtml(product.nome || 'Produto sem nome');
            var currentPrice = escapeHtml(product.price || 'R$ 0,00');
            var thumbnail = escapeHtml(product.thumbnail || '/assets/image/200x300.png');
            var productUrl = '/store/product/' + productId;
            var estoque = parseInt(product.estoque || 0, 10);

            var rawBasePrice = product.valor;
            if (typeof rawBasePrice === 'string') {
                rawBasePrice = rawBasePrice.replace(',', '.');
            }
            var basePrice = formatCurrencyBRL(rawBasePrice);

            var hasDiscount = Number(product.valor_desconto || 0) > 0;

            html += '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento">';
            html += '  <a href="' + productUrl + '" class="card h-100 d-flex flex-column product-card">';
            html += '      <div class="card-img-top position-relative pt-2 px-2">';
            html += '          <img src="' + thumbnail + '" alt="' + name + '" class="img-fluid rounded thumbnail-product">';
            if (estoque === 0) {
                html += '          <span class="badge bg-danger position-absolute top-0 end-0 m-3">Sem Estoque</span>';
            }
            html += '      </div>';
            html += '      <div class="card-body d-flex flex-column">';
            html += '          <h5 class="card-title d-flex justify-content-between align-items-center">';
            html += '              <span class="color-stellar-blue nome-produto">' + name + '</span>';
            html += '              <span class="badge bg-lavanda">Ativo</span>';
            html += '          </h5>';
            html += '          <p class="card-text mt-auto">';
            html += '              <span class="badge bg-light text-dark me-1"></span>';
            html += '          </p>';
            html += '          <div class="card-text">';
            html += '              <div class="d-flex justify-content-between align-items-center">';
            html += '                  <div>';
            html += '                      <span>Preco Base:</span><br>';
            if (hasDiscount) {
                html += '                      <span class="badge bg-light text-dark me-1 text-decoration-line-through">' + basePrice + '</span>';
            } else {
                html += '                      <span class="badge bg-light text-dark me-1">' + basePrice + '</span>';
            }
            html += '                  </div>';
            html += '                  <div>';
            html += '                      <span>Preco Atual:</span> <br>';
            html += '                      <div class="text-end">';
            html += '                          <span class="badge bg-light text-dark me-1">' + currentPrice + '</span>';
            html += '                      </div>';
            html += '                  </div>';
            html += '              </div>';
            html += '          </div>';
            html += '          </p>';
            html += '      </div>';
            html += '  </a>';
            html += '</div>';
        });

        productsList.innerHTML = html;
    }

    function loadProducts(searchTerm) {
        if (!storeId) {
            productsList.innerHTML = [
                '<div class="col-12">',
                '   <div class="alert alert-warning text-center mb-0">',
                '       Loja inválida para carregar produtos.',
                '   </div>',
                '</div>'
            ].join('');
            return;
        }

        if (activeRequest && activeRequest.readyState !== 4) {
            activeRequest.abort();
        }

        productsList.innerHTML = buildPlaceholderCards(6);

        activeRequest = $.ajax({
            url: '/apis/store/products',
            type: 'POST',
            data: {
                storeId: storeId,
                search: searchTerm || ''
            },
            success: function (response) {
                response = parseApiResponse(response);
                if (!response) {
                    productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center mb-0">Erro ao processar resposta da API.</div></div>';
                    return;
                }

                if (response.code !== 200 || !response.data) {
                    productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center mb-0">Não foi possível carregar os produtos.</div></div>';
                    return;
                }

                renderProducts(response.data.products || []);
            },
            error: function () {
                if (activeRequest && activeRequest.readyState === 0) {
                    return;
                }
                productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center mb-0">Falha ao buscar produtos da loja.</div></div>';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            var value = this.value;
            searchTimeout = setTimeout(function () {
                loadProducts(value);
            }, 30);
        });
    }

    if (followButton) {
        followButton.addEventListener('click', function () {
            var button = this;
            var buttonStoreId = parseInt(button.dataset.storeId || '0', 10);
            var loginRedirect = button.dataset.loginRedirect || '';

            if (!buttonStoreId || button.disabled) return;

            button.disabled = true;

            $.ajax({
                url: '/apis/store/follow',
                type: 'POST',
                data: {
                    storeId: buttonStoreId,
                    returnUrl: loginRedirect
                },
                success: function (response) {
                    response = parseApiResponse(response);

                    if (!response) {
                        button.disabled = false;
                        return;
                    }

                    if (response.code === 401 && response.data && response.data.redirect) {
                        window.location.href = response.data.redirect;
                        return;
                    }

                    if (response.code !== 200) {
                        button.disabled = false;
                        return;
                    }

                    button.innerHTML = '<i class="fa-solid fa-check"></i> Seguindo';
                    button.classList.remove('btn-outline-stellar-blue');
                    button.classList.add('btn-success');
                },
                error: function () {
                    button.disabled = false;
                }
            });
        });
    }

    loadProducts('');
})();
