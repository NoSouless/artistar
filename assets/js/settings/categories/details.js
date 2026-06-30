$(document).ready(function() {

    var defaultToast = new ToastManager('myToast');

    initImageDropArea('#thumbnail-drop-area', '#thumbnail-input', '#thumbnail-drop-text');

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
                defaultToast.showSuccess(translator.success, response.message);
            } else {
                defaultToast.showError(translator.error, response.message || translator.errorSave);
            }
        }).fail(function() {
            defaultToast.showError(translator.error, translator.errorSave);
        });
    });

    $(document).on('click', '#accept-delete', function() {
        $.ajax({
            url: '/settings/categories/delete',
            type: 'POST',
            data: { categoryId: $('#category-id').val() },
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                window.location.href = '/settings/categories';
            } else {
                defaultToast.showError(translator.error, response.message);
            }
        }).fail(function() {
            defaultToast.showError(translator.error, translator.errorDelete);
        });
    });
});