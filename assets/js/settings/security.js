$(document).ready(function() {
    function showToast(title, message, success) {
        $('#toastTitle').text(title);
        $('#toastBody').text(message);
        $('#myToast').toggleClass('bg-success', success).toggleClass('bg-danger', !success);
        new bootstrap.Toast(document.getElementById('myToast')).show();
    }

    $(document).on('click', '.revoke-device, #revoke-all-devices', function() {
        var $list = $('#devices-list');
        var all = this.id === 'revoke-all-devices';
        if (!window.confirm($list.data('confirm'))) return;
        $.post($list.data('url'), all ? { all: 1 } : { id: $(this).data('id') }).done(function(response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                location.reload();
            } else {
                showToast($list.data('error'), response.message, false);
            }
        }).fail(function() {
            showToast($list.data('error'), $list.data('error'), false);
        });
    });

    $(document).on('submit', '#settings-security-form', function(event) {
        event.preventDefault();
        var form = this;
        var $form = $(form);

        if ($('#nova-senha').val() !== $('#confirmacao-senha').val()) {
            showToast($form.data('error'), $form.data('mismatch'), false);
            return;
        }

        var $button = $('#settings-security-submit').prop('disabled', true);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize()
        }).done(function(response) {
            response = JSON.parse(response);
            var ok = response.code == 200;
            showToast($form.data(ok ? 'success' : 'error'), response.message, ok);
            if (ok) form.reset();
        }).fail(function() {
            showToast($form.data('error'), $form.data('error'), false);
        }).always(function() {
            $button.prop('disabled', false);
        });
    });
});
