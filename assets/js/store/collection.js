(function () {
    var productsList = document.getElementById('storeCollectionProductsList');
    var searchInput = document.getElementById('storeCollectionSearchInput');

    if (!productsList) return;

    var storeId = parseInt(productsList.dataset.storeId || '0', 10);
    var collectionId = parseInt(productsList.dataset.collectionId || '0', 10);
    var searchTimeout = null;
    var activeRequest = null;
    var currentProducts = [];
    var selectedProductsSortable = null;

    var apiUrls = {
        products: '/apis/store/manage-collection-products',
        toggleOrder: '/apis/store/toggle-collection-product',
        reorderOrder: '/apis/store/reorder-collection-product-order'
    };

    var messages = {
        invalidStore: 'Colecao invalida para carregar produtos.',
        invalidApiResponse: 'Erro ao processar resposta da API.',
        productsUnavailable: 'Nao foi possivel carregar os produtos.',
        searchFailed: 'Falha ao buscar produtos da colecao.',
        processResponseFailed: 'Nao foi possivel processar a resposta da API.',
        updateFailed: 'Falha ao atualizar produto da colecao.',
        saveOrderFailed: 'Falha ao salvar a nova ordem dos produtos.',
        noProductsInStore: 'Nenhum produto encontrado na loja.',
        noSelectedProducts: 'Nenhum produto encontrado na colecao'
    };

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

    function buildAlertHtml(message, type) {
        var alertType = type || 'light';

        return [
            '<div class="col-12">',
            '   <div class="alert alert-' + alertType + ' border text-center mb-0">',
            '       ' + message,
            '   </div>',
            '</div>'
        ].join('');
    }

    function favoriteButtonHtml(productId, selected, name) {
        var title = selected ? 'Remover da colecao' : 'Adicionar na colecao';

        return '<button type="button" class="store-product-favorite" data-product-id="' + productId + '" data-selected="' + (selected ? '1' : '0') + '" aria-label="' + title + ' ' + name + '" title="' + title + '" style="position:absolute; top:8px; right:8px; z-index:3; border-radius:100%; background:none; border:none; font-size:1.5rem;">'
            + '<i class="' + (selected ? 'fa-solid fa-heart' : 'fa-regular fa-heart') + ' link-nocturne-purple link-hover"></i>'
            + '</button>';
    }

    function buildPlaceholderCards(total) {
        var html = '';

        for (var i = 0; i < total; i++) {
            html += '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento">';
            html += '  <div class="card h-100 d-flex flex-column product-card store-product-card is-placeholder">';
            html += '      <div class="card-img-top position-relative pt-2 px-2">';
            html += '          <div class="store-product-image placeholder-glow thumbnail-product" style="display:block; width:100%; aspect-ratio:1 / 1;">';
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
            html += '      </div>';
            html += '  </div>';
            html += '</div>';
        }

        return html;
    }

    function buildProductCard(product) {
        var productId = parseInt(product.id || 0, 10);
        var name = escapeHtml(product.nome || 'Produto sem nome');
        var currentPrice = escapeHtml(product.price || 'R$ 0,00');
        var thumbnail = escapeHtml(product.thumbnail || '/assets/image/200x300.png');
        var selected = !!product.selected;
        var rawBasePrice = product.valor;
        var rawDiscount = product.valor_desconto;
        var hasStockInfo = product.estoque !== undefined && product.estoque !== null && String(product.estoque) !== '';
        var stock = parseInt(product.estoque || '0', 10);
        var showNoStock = hasStockInfo && !isNaN(stock) && stock <= 0;

        if (typeof rawBasePrice === 'string') {
            rawBasePrice = rawBasePrice.replace(',', '.');
        }

        var basePrice = formatCurrencyBRL(rawBasePrice);
        var hasDiscount = parseFloat(String(rawDiscount || '0').replace(',', '.')) > 0;
        var html = '';

        html += '<div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento" data-product-id="' + productId + '" data-selected="' + (selected ? '1' : '0') + '">';
        html += '  <div class="card h-100 d-flex flex-column product-card store-product-card">';
        html += '      <div class="card-img-top position-relative pt-2 px-2">';
        html += '          <img src="' + thumbnail + '" class="img-fluid rounded thumbnail-product store-product-image" alt="' + name + '">';

        if (showNoStock) {
            html += '          <span class="badge bg-danger position-absolute top-0 end-0 m-3">Sem Estoque</span>';
        }

        html += favoriteButtonHtml(productId, selected, name);
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
        html += '                      <span>Preço Base:</span><br>';

        if (hasDiscount) {
            html += '                      <span class="badge bg-light text-dark me-1 text-decoration-line-through">' + basePrice + '</span>';
        } else {
            html += '                      <span class="badge bg-light text-dark me-1">' + basePrice + '</span>';
        }

        html += '                  </div>';

        if (hasDiscount) {
            html += '                  <div>';
            html += '                      <span>Preço Atual:</span> <br>';
            html += '                      <div class="text-end">';
            html += '                          <span class="badge bg-light text-dark me-1">' + currentPrice + '</span>';
            html += '                      </div>';
            html += '                  </div>';
        }

        html += '              </div>';
        html += '          </div>';
        html += '      </div>';
        html += '  </div>';
        html += '</div>';

        return html;
    }

    function destroySelectedSortable() {
        if (selectedProductsSortable && typeof selectedProductsSortable.destroy === 'function') {
            selectedProductsSortable.destroy();
        }

        selectedProductsSortable = null;
    }

    function updateSelectedProductsOrder() {
        var selectedList = document.getElementById('storeManageSelectedProductsList');

        if (!selectedList) return;

        var orderedIds = Array.prototype.map.call(selectedList.querySelectorAll('[data-product-id]'), function (item) {
            return parseInt(item.dataset.productId || '0', 10);
        }).filter(function (productId) {
            return productId > 0;
        });

        if (!orderedIds.length) return;

        $.ajax({
            url: apiUrls.reorderOrder,
            type: 'POST',
            data: {
                storeId: storeId,
                productIds: orderedIds
            },
            success: function (response) {
                response = parseApiResponse(response);

                if (!response || response.code !== 200) {
                    window.alert((response && response.message) ? response.message : messages.saveOrderFailed);
                    loadProducts(searchInput ? searchInput.value : '');
                }
            },
            error: function () {
                window.alert(messages.saveOrderFailed);
                loadProducts(searchInput ? searchInput.value : '');
            }
        });
    }

    function initSelectedSortable() {
        var selectedList = document.getElementById('storeManageSelectedProductsList');

        if (!selectedList || typeof Sortable === 'undefined') return;

        destroySelectedSortable();

        selectedProductsSortable = new Sortable(selectedList, {
            animation: 150,
            ghostClass: 'is-sorting',
            draggable: '.evento',
            filter: '.store-product-favorite',
            preventOnFilter: false,
            onEnd: function () {
                updateSelectedProductsOrder();
            }
        });
    }

    function renderProductsGroup(products) {
        var html = '';

        products.forEach(function (product) {
            html += buildProductCard(product);
        });

        return html;
    }

    function renderProducts(products) {
        if (!Array.isArray(products) || products.length === 0) {
            currentProducts = [];
            destroySelectedSortable();
            productsList.innerHTML = buildAlertHtml(messages.noProductsInStore, 'light');
            return;
        }

        currentProducts = products.slice();

        var selectedProducts = [];
        var unselectedProducts = [];

        currentProducts.forEach(function (product) {
            if (product.selected) {
                selectedProducts.push(product);
            } else {
                unselectedProducts.push(product);
            }
        });

        var html = '';

        if (!selectedProducts.length) {
            destroySelectedSortable();
        }

        if (selectedProducts.length) {
            html += '<div class="col-12">';
            html += '   <div id="storeManageSelectedProductsList" class="row g-3">';
            html += renderProductsGroup(selectedProducts);
            html += '   </div>';
            html += '</div>';
        } else if (unselectedProducts.length) {
            html += buildAlertHtml(messages.noSelectedProducts, 'light');
        }

        if (selectedProducts.length && unselectedProducts.length) {
            html += '<div class="col-12"><hr class="my-4"></div>';
        } else if (!selectedProducts.length && unselectedProducts.length) {
            html += '<div class="col-12"><hr class="my-4"></div>';
        }

        if (unselectedProducts.length) {
            html += '<div class="col-12">';
            html += '   <div id="storeManageUnselectedProductsList" class="row g-3">';
            html += renderProductsGroup(unselectedProducts);
            html += '   </div>';
            html += '</div>';
        }

        productsList.innerHTML = html;
        initSelectedSortable();
    }

    function updateCurrentProductSelection(productId, selected) {
        for (var i = 0; i < currentProducts.length; i++) {
            if (parseInt(currentProducts[i].id || 0, 10) === productId) {
                currentProducts[i].selected = selected;
                break;
            }
        }

        renderProducts(currentProducts);
    }

    function loadProducts(searchTerm) {
        if (!storeId) {
            productsList.innerHTML = buildAlertHtml(messages.invalidStore, 'warning');
            return;
        }

        if (activeRequest && activeRequest.readyState !== 4) {
            activeRequest.abort();
        }

        productsList.innerHTML = buildPlaceholderCards(6);

        activeRequest = $.ajax({
            url: apiUrls.products,
            type: 'POST',
            data: {
                storeId: storeId,
                collectionId: collectionId,
                search: searchTerm || ''
            },
            success: function (response) {
                response = parseApiResponse(response);

                if (!response) {
                    productsList.innerHTML = buildAlertHtml(messages.invalidApiResponse, 'danger');
                    return;
                }

                if (response.code !== 200 || !response.data) {
                    productsList.innerHTML = buildAlertHtml(messages.productsUnavailable, 'danger');
                    return;
                }

                renderProducts(response.data.products || []);
            },
            error: function () {
                if (activeRequest && activeRequest.readyState === 0) {
                    return;
                }

                productsList.innerHTML = buildAlertHtml(messages.searchFailed, 'danger');
            }
        });
    }

    function setButtonState(button, selected) {
        var icon = button.querySelector('i');

        if (!icon) return;

        button.dataset.selected = selected ? '1' : '0';
        button.title = selected ? 'Remover da colecao' : 'Adicionar na colecao';
        icon.className = (selected ? 'fa-solid fa-heart' : 'fa-regular fa-heart') + ' link-nocturne-purple link-hover';
    }

    function toggleProduct(button) {
        var productId = parseInt(button.dataset.productId || '0', 10);

        if (!productId || !storeId || !collectionId) return;
        if (button.disabled) return;

        button.disabled = true;

        $.ajax({
            url: apiUrls.toggleOrder,
            type: 'POST',
            data: {
                storeId: storeId,
                collectionId: collectionId,
                productId: productId
            },
            success: function (response) {
                response = parseApiResponse(response);

                if (!response) {
                    window.alert(messages.processResponseFailed);
                    return;
                }

                if (response.code !== 200 || !response.data) {
                    if (response.message) {
                        window.alert(response.message);
                    }
                    return;
                }

                updateCurrentProductSelection(productId, !!response.data.selected);
            },
            error: function () {
                window.alert(messages.updateFailed);
            },
            complete: function () {
                button.disabled = false;
            }
        });
    }

    productsList.addEventListener('click', function (event) {
        var target = event.target;
        var button = target.closest('.store-product-favorite');

        if (!button) return;

        event.preventDefault();
        event.stopPropagation();
        toggleProduct(button);
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);

            var value = this.value;
            searchTimeout = setTimeout(function () {
                loadProducts(value);
            }, 120);
        });
    }

    loadProducts('');
})();