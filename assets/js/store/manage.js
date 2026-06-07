function loadProducts(searchTerm) {
    $.ajax({
        url: '/store/products',
        type: 'POST',
        data: {},
        success: function (response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                if (response.data.selected) appendProductList(response.data.selected, true);
                if (response.data.unselected) {
                    appendProductList(response.data.unselected, false);
                } else {
                    $('#storeManageProductsSeparator').hide();
                }
                initSelectedSortable();

                $('#storeManageSkeletonList').hide();
            }
        },
        error: function () {
            alert(messages.productsLoadError);
        }
    });
}

function appendProductList(products, selected = false) {
    products.forEach(function (product) {
        $(selected ? '#storeManageSelectedProductsList' : '#storeManageUnselectedProductsList').append(buildProductCard(product, selected));
    });
}


function buildProductCard(product, selected = false) {
    return `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 draggable-product" id="product-${product.id}" data-product-id="${product.id}" data-product-selected="${selected}" data-product-name="${product.nome}" data-product-keywords="${product.palavras_chave}">
            <div class="card h-100 d-flex flex-column product-card">
                <div class="card-img-top position-relative pt-2 px-2">
                    <img src="${product.thumbnail}" class="img-fluid rounded thumbnail-product store-product-image" alt="${product.nome}">
                    <button type="button" class="store-product-favorite round position-absolute" data-product-id="${product.id}">
                        <i class="fa-solid fa-plus add-product link-nocturne-purple link-hover"></i>
                        <i class="fa-solid fa-minus remove-product link-nocturne-purple link-hover"></i>
                    </button>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">
                        <span class="color-stellar-blue nome-produto">${product.nome}</span>
                    </h6>
                </div>
            </div>
            ${selected ? `<input type="hidden" name="selected_products_order[${product.id}]" id="selected-product-${product.id}" value="">` : ''}
        </div>
    `;
}

$(document).ready(function () {
    loadProducts('');
    $('#storeManageSearchInput').on('input', function () {
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

    $('.store-product-favorite').on('click', function () {
        var productId = $(this).data('product-id');
        var isSelected = $('#product-' + productId).attr('data-product-selected') === 'true';
        if (isSelected) {
            removeProductFromSelection(productId);
        } else {
            addProductToSelection(productId);
        } 

        if ($('#storeManageSelectedProductsList > [data-product-selected="true"]').length > 0) {
            $('#emptySelectedProductsList').hide();
        } else {
            $('#emptySelectedProductsList').show();
        }
        if ($('#storeManageUnselectedProductsList > [data-product-selected="false"]').length > 0) {
            $('#storeManageProductsSeparator').show();
        } else {
            $('#storeManageProductsSeparator').hide();
        }
    });
});

function initSelectedSortable() {
    var selectedList = document.getElementById('storeManageSelectedProductsList');
    selectedProductsSortable = new Sortable(selectedList, {
        animation: 150,
        ghostClass: 'is-sorting',
        draggable: '.draggable-product',
        filter: '.store-product-favorite',
        preventOnFilter: false,
        onEnd: function () {
            $('#storeManageSelectedProductsList > .draggable-product').each(function (index) {
                var productId = $(this).data('product-id');
                $('#selected-product-' + productId).val(index);
            });
        }
    });
}

function addProductToSelection(productId) {
    $('#product-' + productId).appendTo('#storeManageSelectedProductsList');
    $('#product-' + productId).append('<input type="hidden" name="selected_products_order[' + productId + ']" id="selected-product-' + productId + '" value="">');
    $('#product-' + productId).attr('data-product-selected', true);

}

function removeProductFromSelection(productId) {
    $('#product-' + productId).appendTo('#storeManageUnselectedProductsList');
    $('#selected-product-' + productId).remove();
    $('#product-' + productId).attr('data-product-selected', false);
}