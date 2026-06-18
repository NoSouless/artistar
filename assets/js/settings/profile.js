$(document).ready(function() {
    initImageDropArea('#profile-photo-drop-area', '#profile-photo-input', '#profile-photo-drop-text');

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
});