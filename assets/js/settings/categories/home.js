$(document).on('submit', '#settings-profile-form', function(event) {
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
            $('#toastTitle').text($('#settings-profile-form').attr('data-success'));
            $('#toastBody').text(response.message);
            $('#myToast').removeClass('bg-danger').addClass('bg-success');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        } else {
            $('#toastTitle').text($('#settings-profile-form').attr('data-error'));
            $('#toastBody').text(response.message || 'Erro ao salvar as alterações.');
            $('#myToast').removeClass('bg-success').addClass('bg-danger');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        }
    }).fail(function() {
        $('#toastTitle').text($('#settings-profile-form').attr('data-error'));
        $('#toastBody').text('Erro ao salvar as alterações.');
        $('#myToast').removeClass('bg-success').addClass('bg-danger');
        var myToast = new bootstrap.Toast(document.getElementById('myToast'));
        myToast.show();
    });
});

$(document).on('click', '.delete-category-btn', function() {
    const categoryId = $(this).data('category-id');
    if (confirm('Tem certeza que deseja excluir esta categoria?')) {
        $('.category-node[data-category-id="' + categoryId + '"]').remove();
    }
});

function initCategorySortables() {
    if (typeof Sortable === 'undefined') {
        return;
    }

    document.querySelectorAll('#categories-table-body').forEach(function(container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'ghost',
            chosenClass: 'chosen',
            handle: '.category-handle',
            draggable: '.category-row',
            filter: '.unsortable',
            preventOnFilter: false,
            scroll: true,                // Enable autoscroll plugin
            scrollSensitivity: 50,       // Distance from edge (in pixels) to trigger scroll
            scrollSpeed: 10,             // Speed of scroll (in pixels per frame)
            bubbleScroll: true,          // Apply scroll to parent containers if true
            onMove: function(evt) {
                if (evt.to && evt.to.classList && evt.to.classList.contains('category-children') && evt.to.dataset.acceptChildren !== '1') {
                    return false;
                }
                return true;
            },
            onEnd: function(evt) {
                var defaultToast = new ToastManager('myToast');
                $.ajax({
                    url: '/settings/categories/reorder',
                    type: 'POST',
                    data: {
                        order: Array.from(evt.to.children).map(child => child.dataset.categoryId)
                    }
                }).done(function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        defaultToast.showSuccess(translator.success, response.message);
                    } else {
                        defaultToast.showError(translator.error, response.message);
                    }
                }).fail(function() {
                    defaultToast.showError(translator.error, 'Erro ao salvar a ordem das categorias.');
                });
            }
        });
    });
}

$(document).ready(function() {
    var defaultToast = new ToastManager('myToast');
    initCategorySortables();
});

$('#add-category-btn').on('click', function() {
    var formData = new FormData();
    var defaultToast = new ToastManager('myToast');
    formData.append('name', $('#new-category-name').val());
    formData.append('active', $('#flexSwitchCheckActive').is(':checked') ? 1 : 0);
    formData.append('public', $('#flexSwitchCheckPublic').is(':checked') ? 1 : 0);
    $.ajax({
        url: '/settings/categories/new',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            window.location.reload();
        } else {
            defaultToast.showError(translator.error, response.message);  
        }
    }).fail(function() {
        defaultToast.showError(translator.error, 'Erro ao criar a categoria.');  
    });
});

$(document).on('click', '.save-category-btn', function() {
    var categoryId = $(this).data('category-id');
    var defaultToast = new ToastManager('myToast');
    var formData = new FormData();
    formData.append('id', categoryId);
    formData.append('name', $('#category-name-' + categoryId).val());
    formData.append('active', $('#category-active-' + categoryId).is(':checked') ? 1 : 0);
    formData.append('public', $('#category-public-' + categoryId).is(':checked') ? 1 : 0);

    $.ajax({
        url: '/settings/categories/update',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.code == 200) {
            defaultToast.showSuccess(translator.success, response.message);
        } else {
            defaultToast.showError(translator.error, response.message);
        }
    }).fail(function() {
        defaultToast.showError(translator.error, 'Erro ao salvar as alterações.');
    });
});
