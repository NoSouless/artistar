function loadProducts(categoryId = null) {
    $('#storeSkeletonList').show();
    $('#storeSelectedProductsList').hide();
    $('#storeSelectedProductsList').empty();
    $.ajax({
        url: '/apis/store/products',
        type: 'POST',
        data: {
            storeId: $('#storeSelectedProductsList').data('store-id'),
            categoryId: categoryId
        },
        success: function (response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                response.data.products.forEach(function (product, index) {
                    $('#storeSelectedProductsList').append(buildProductCard(product, index, false));
                });

                $('#storeSkeletonList').hide();
                $('#storeSelectedProductsList').show();
            }
        },
        error: function () {
            alert(messages.productsLoadError);
        }
    });
}

function loadCategories() {
        $.ajax({
        url: '/apis/store/categories',
        type: 'POST',
        data: {
            storeId: $('#storeSelectedProductsList').data('store-id'),
        },
        success: function (response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                response.data.categories.forEach(function (category, index) {
                    var categoryLink = `
                        <a href="#" class="d-flex page-link flex-column align-items-center justify-content-center text-center gap-1 store-category-link" data-category-id="${category.id}" aria-label="Filtrar por categoria ${category.nome}">
                            <span class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center store-category-btn" style="background-image: url('${category.thumbnail}'); background-size: cover;" aria-label="Categoria ${category.nome}">
                            </span>
                            <span class="store-category-name">${category.nome}</span>
                        </a>
                    `;
                    $('.store-catalog-categories').append(categoryLink);
                });
                $('.store-catalog-categories').slick({
                    slidesToShow: 8,	
                    infinite: false,
                    arrows: true,
                    autoplay: true,
                    autoplaySpeed: 1000,
                    prevArrow: '<button type="button" class="slick-prev">Previous</button>',
                    nextArrow: '<button type="button" class="slick-next">Next</button>',
                    responsive: [
                        {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1,
                        },
                        },
                        {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        },
                        },
                    ],
                });
                $('.store-category-link').on('click', function (e) {
                    e.preventDefault();
                    var categoryId = $(this).data('category-id');
                    $('#storeShowcaseTitle').text($(this).find('.store-category-name').text());
                    $('#backToShowcaseBtn').show();
                    loadProducts(categoryId);
                }); 
            }
        },
        error: function () {
            alert(messages.productsLoadError);
        }
    });
}

$(document).ready(function () {
    defaultToast = new ToastManager('myToast');
    loadCategories();
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

    $('#backToShowcaseBtn').on('click', function (e) {
        e.preventDefault();
        $('#storeShowcaseTitle').text(translator["Destaques"]);
        loadProducts();
        $('#backToShowcaseBtn').hide();
    });
});

function buildProductCard(product, order, selected = false) {
    let stockBadge = '';
    if (product.mostrar_estoque) {
    if (product.estoque <= 0) {
        stockBadge = `<span class="badge bg-danger position-absolute top-0 end-0 m-3">${translator["Esgotado!"]}</span>`;
    } else {
        stockBadge = `<span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3">${product.estoque} ${translator["uni"]}</span>`;
    }
}
    return `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 draggable-product" id="product-${product.id}" data-product-name="${product.nome}" data-product-keywords="${product.palavras_chave}">
            <div class="card h-100 d-flex flex-column product-card">
                <div class="card-img-top position-relative pt-2 px-2">
                    <img src="${product.thumbnail}" class="img-fluid rounded thumbnail-product store-product-image" alt="${product.nome}">
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">
                        <span class="color-stellar-blue nome-produto">${product.nome}</span>
                        ${stockBadge}
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