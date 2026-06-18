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
                defaultToast.showSuccess('Sucesso', response.message || 'Alterações salvas com sucesso!');
            } else {
                defaultToast.showError('Erro', response.message || 'Erro ao salvar as alterações.');
            }
        }).fail(function() {
            defaultToast.showError('Erro', 'Erro ao salvar as alterações.');
        });
    });
});