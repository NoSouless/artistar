function loadProducts() {
    $.ajax({
        url: '/apis/store/products',
        type: 'POST',
        data: {
            storeId: $('#storeManageSelectedProductsList').data('store-id'),
        },
        success: function (response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                response.data.products.forEach(function (product, index) {
                    $('#storeManageSelectedProductsList').append(buildProductCard(product, index, false));
                });

                $('#storeManageSkeletonList').hide();
            }
        },
        error: function () {
            alert(messages.productsLoadError);
        }
    });
}

$(document).ready(function () {
    defaultToast = new ToastManager('myToast');
    loadProducts();
    $('#storeSearchInput').on('input', function () {
        var searchTerm = ($(this).val() || '').toString().toLowerCase().trim();
        if (!searchTerm) {
            $('.draggable-product').show();
            return;
        }
        $('.draggable-product').each(function () {
            var productName = ($(this).data('product-name') || '').toString().toLowerCase();
            var productKeywords = ($(this).data('product-keywords') || '').toString().toLowerCase();
            if (productName.indexOf(searchTerm) !== -1 || productKeywords.indexOf(searchTerm) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

function buildProductCard(product, order, selected = false) {
    return `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 draggable-product" id="product-${product.id}" data-product-name="${product.nome}" data-product-keywords="${product.palavras_chave}">
            <div class="card h-100 d-flex flex-column product-card">
                <div class="card-img-top position-relative pt-2 px-2">
                    <img src="${product.thumbnail}" class="img-fluid rounded thumbnail-product store-product-image" alt="${product.nome}">
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">
                        <span class="color-stellar-blue nome-produto">${product.nome}</span>
                    </h6>
                    <div class="card-text mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <div class="col-9">
                                <div class="text-start mb-0 fw-bold">
                                    <span class="h5 fw-bold color-graphite-gray">${currency}${product.valor}</span>
                                    ${product.valor_desconto > 0 ? `<small style="font-size: 12px;" class=" ms-1 text-decoration-line-through color-gray">${currency}${product.valor_original}</small>` : ''}
                                </div>
                            </div>
                            <div class="col-3 text-end">
                                ${product.discount_percentage > 0 ? `<span class="badge bg-lavanda">-${product.discount_percentage}%</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}


// (function () {
//     var productsList = document.getElementById('storeProductsList');
//     var searchInput = document.getElementById('storeSearchInput');
//     var followButton = document.querySelector('.store-follow-btn[data-store-id]');

//     if (!productsList) return;

//     var storeId = parseInt(productsList.dataset.storeId || '0', 10);
//     var searchTimeout = null;
//     var activeRequest = null;

//     function parseApiResponse(response) {
//         if (response && typeof response === 'object') return response;
//         if (typeof response !== 'string') return null;

//         var trimmed = response.trim();
//         try {
//             return JSON.parse(trimmed);
//         } catch (e) {
//             var firstBrace = trimmed.indexOf('{');
//             var lastBrace = trimmed.lastIndexOf('}');
//             if (firstBrace === -1 || lastBrace === -1 || lastBrace <= firstBrace) return null;

//             try {
//                 return JSON.parse(trimmed.substring(firstBrace, lastBrace + 1));
//             } catch (err) {
//                 return null;
//             }
//         }
//     }

//     function escapeHtml(value) {
//         return String(value || '')
//             .replace(/&/g, '&amp;')
//             .replace(/</g, '&lt;')
//             .replace(/>/g, '&gt;')
//             .replace(/"/g, '&quot;')
//             .replace(/'/g, '&#039;');
//     }

//     function formatCurrencyBRL(value) {
//         var number = Number(value);
//         if (!isFinite(number)) return '0,00';
//         return number.toFixed(2).replace('.', ',');
//     }

//     function buildPlaceholderCards(total) {
//         var html = '';
//         for (var i = 0; i < total; i++) {
//             html = `
//                 <div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento">
//                 <div class="card h-100 d-flex flex-column product-card is-placeholder">
//                     <div class="card-img-top position-relative pt-2 px-2">
//                         <div class="store-product-image placeholder-glow thumbnail-product" style="display:block; width:100%;">
//                             <span class="placeholder w-100 h-100 d-block"></span>
//                         </div>
//                     </div>
//                     <div class="card-body d-flex flex-column">
//                         <h5 class="card-title d-flex justify-content-between align-items-center">
//                             <span class="color-stellar-blue nome-produto">Carregando...</span>
//                             <span class="badge bg-lavanda">Ativo</span>
//                         </h5>
//                         <p class="card-text mt-auto">
//                             <span class="badge bg-light text-dark me-1"></span>
//                         </p>
//                         <div class="card-text">
//                             <div class="d-flex justify-content-between align-items-center">
//                                 <div>
//                                     <span>Preco Base:</span><br>
//                                     <span class="badge bg-light text-dark me-1">R$ 0,00</span>
//                                 </div>
//                                 <div>
//                                     <span>Preco Atual:</span> <br>
//                                     <div class="text-end">
//                                         <span class="badge bg-light text-dark me-1">R$ 0,00</span>
//                                     </div>
//                                 </div>
//                             </div>
//                         </div>
//                         </p>
//                     </div>
//                 </div>
//                 </div>
//             `;
//         }
//         return html;
//     }

//     function renderProducts(products) {
//         if (!Array.isArray(products) || products.length === 0) {
//             productsList.innerHTML = [
//                 '<div class="col-12">',
//                 '   <div class="alert alert-light border text-center mb-0">',
//                 '       Nenhum produto encontrado na loja.',
//                 '   </div>',
//                 '</div>'
//             ].join('');
//             return;
//         }

//         var html = '';
//         products.forEach(function (product) {
//             var productId = parseInt(product.id || 0, 10);
//             var name = escapeHtml(product.nome || 'Produto sem nome');
//             var currentPrice = escapeHtml(product.price || 'R$ 0,00');
//             var thumbnail = escapeHtml(product.thumbnail || '/assets/image/200x300.png');
//             var productUrl = '/store/product/' + productId;
//             var estoque = parseInt(product.estoque || 0, 10);

//             var rawBasePrice = product.valor;
//             // if (typeof rawBasePrice === 'string') {
//             //     rawBasePrice = rawBasePrice.replace(',', '.');
//             // }
//             var basePrice = formatCurrencyBRL(rawBasePrice);

//             var hasDiscount = Number(product.valor_desconto || 0) > 0;

//             var estoqueBadge = estoque === 0 ? '<span class="badge bg-danger position-absolute top-0 end-0 m-3">Sem Estoque</span>' : '';
//             var displayDiscount = product.valor != product.price ? 'block' : 'none';

//             html = `
//             <div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento">
//               <a href="${productUrl}" class="card h-100 d-flex flex-column product-card">
//                   <div class="card-img-top position-relative pt-2 px-2">
//                         <img src="${thumbnail}" alt="${name}" class="img-fluid rounded thumbnail-product">
//                         ${estoqueBadge}
//                   </div>
//                   <div class="card-body d-flex flex-column">
//                       <h5 class="card-title d-flex justify-content-between align-items-center">
//                           <span class="color-stellar-blue nome-produto">${name}</span>
//                       </h5>
//                       <p class="card-text mt-auto">
//                           <span class="badge bg-light text-dark me-1"></span>
//                       </p>
//                       <div class="card-text">
//                           <div class="d-flex justify-content-between align-items-center d-${displayDiscount}">
//                                 <small class="text-end color-graphite-gray mb-0 fw-bold text-decoration-line-through">
//                                     R$ ${basePrice}
//                                 </small>
//                           </div>
//                           <div class="d-flex justify-content-between align-items-center">
//                                 <div class="text-end color-graphite-gray h4 mb-0 fw-bold">
//                                     R$ ${currentPrice}
//                                 </div>
//                           </div>
//                       </div>
//                       </p>
//                   </div>
//               </a>
//             </div>
//             `;
//         });

//         productsList.innerHTML = html;
//     }

//     function loadProducts(searchTerm) {
//         if (!storeId) {
//             productsList.innerHTML = [
//                 '<div class="col-12">',
//                 '   <div class="alert alert-warning text-center mb-0">',
//                 '       Loja inválida para carregar produtos.',
//                 '   </div>',
//                 '</div>'
//             ].join('');
//             return;
//         }

//         if (activeRequest && activeRequest.readyState !== 4) {
//             activeRequest.abort();
//         }

//         productsList.innerHTML = buildPlaceholderCards(6);

//         activeRequest = $.ajax({
//             url: '/apis/store/products',
//             type: 'POST',
//             data: {
//                 storeId: storeId,
//                 search: searchTerm || ''
//             },
//             success: function (response) {
//                 response = parseApiResponse(response);
//                 if (!response) {
//                     productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center mb-0">Erro ao processar resposta da API.</div></div>';
//                     return;
//                 }

//                 if (response.code !== 200 || !response.data) {
//                     productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center mb-0">Não foi possível carregar os produtos.</div></div>';
//                     return;
//                 }

//                 renderProducts(response.data.products || []);
//             },
//             error: function () {
//                 if (activeRequest && activeRequest.readyState === 0) {
//                     return;
//                 }
//                 productsList.innerHTML = '<div class="col-12"><div class="alert alert-danger text-center mb-0">Falha ao buscar produtos da loja.</div></div>';
//             }
//         });
//     }

//     if (searchInput) {
//         searchInput.addEventListener('input', function () {
//             clearTimeout(searchTimeout);
//             var value = this.value;
//             searchTimeout = setTimeout(function () {
//                 loadProducts(value);
//             }, 30);
//         });
//     }

//     if (followButton) {
//         followButton.addEventListener('click', function () {
//             var button = this;
//             var buttonStoreId = parseInt(button.dataset.storeId || '0', 10);
//             var loginRedirect = button.dataset.loginRedirect || '';

//             if (!buttonStoreId || button.disabled) return;

//             button.disabled = true;

//             $.ajax({
//                 url: '/apis/store/follow',
//                 type: 'POST',
//                 data: {
//                     storeId: buttonStoreId,
//                     returnUrl: loginRedirect
//                 },
//                 success: function (response) {
//                     response = parseApiResponse(response);

//                     if (!response) {
//                         button.disabled = false;
//                         return;
//                     }

//                     if (response.code === 401 && response.data && response.data.redirect) {
//                         window.location.href = response.data.redirect;
//                         return;
//                     }

//                     if (response.code !== 200) {
//                         button.disabled = false;
//                         return;
//                     }

//                     button.innerHTML = '<i class="fa-solid fa-check"></i> Seguindo';
//                     button.classList.remove('btn-outline-stellar-blue');
//                     button.classList.add('btn-success');
//                 },
//                 error: function () {
//                     button.disabled = false;
//                 }
//             });
//         });
//     }

//     loadProducts('');
// })();
