function disableRegisterBtn() {
    $('#btn-confirm-password-reset').prop('disabled', true);
    $('#spinner-confirm').show();
    $('#text-confirm').hide();
}

function enableRegisterBtn() {
    $('#btn-confirm-password-reset').prop('disabled', false);
    $('#spinner-confirm').hide();
    $('#text-confirm').show();
}

$('#form-password-reset').on('submit', function(e) {
    e.preventDefault();
    disableRegisterBtn();
    let email = $('#email').val();

    const form = this;
    executeRecaptcha('password_reset', form.dataset.recaptchaSitekey).then(function(token) {
        $.ajax({
            url: '/password-reset',
            type: 'POST',
            data: {
                email: email,
                'g-recaptcha-response': token
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    location.href = $('#form-password-reset').attr('action');
                }  else {    
                    $('#toastTitle').text(response.message);
                    $('#toastBody').text('');
                    $('#myToast').addClass('bg-danger');
                    $('#myToast').removeClass('bg-success');
                    var myToast = new bootstrap.Toast(document.getElementById('myToast'));
                    myToast.show();
                }
                enableRegisterBtn();
            },
            error: function(error) {
                console.log('An error occurred');
                console.error(error);
                enableRegisterBtn();
            }
        });
    }).catch(function() {
        $('#toastTitle').text(form.dataset.recaptchaError);
        $('#toastBody').text('');
        $('#myToast').addClass('bg-danger').removeClass('bg-success');
        new bootstrap.Toast(document.getElementById('myToast')).show();
        enableRegisterBtn();
    });
});