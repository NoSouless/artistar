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

    document.querySelectorAll('#categories-tree, .category-children').forEach(function(container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'ghost',
            chosenClass: 'chosen',
            handle: '.category-handle',
            draggable: '.category-row',
            filter: '.unsortable',
            preventOnFilter: false,
            // Core Scrolling Options
            scroll: true,                // Enable autoscroll plugin
            scrollSensitivity: 50,       // Distance from edge (in pixels) to trigger scroll
            scrollSpeed: 10,             // Speed of scroll (in pixels per frame)
            bubbleScroll: true,          // Apply scroll to parent containers if true
            group: {
                name: 'categories-tree',
                pull: true,
                put: true
            },
            onMove: function(evt) {
                if (evt.to && evt.to.classList && evt.to.classList.contains('category-children') && evt.to.dataset.acceptChildren !== '1') {
                    return false;
                }

                return true;
            },
            onEnd: function() {
                // Um item com filhos não pode ser movido para dentro de um item sem filhos, não desabilita o destino de ter filhos, mas apenas desfaz o ato de mover
                // if (evt.to && evt.to.classList && evt.to.classList.contains('category-children') && evt.to.dataset.acceptChildren !== '1') {
                //     evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                // }
            }
        });
    });
}

$(document).ready(function() {
    initCategorySortables();
});
