$('#form-login').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    const button = $(form).find('button[type="submit"]');
    let email = $('#email').val();
    let senha = $('#senha').val();
    button.prop('disabled', true);
    executeRecaptcha('login', form.dataset.recaptchaSitekey).then(function(token) {
        $.ajax({
            url: '/auth/login',
            type: 'POST',
            data: {
                email: email,
                password: senha,
                'g-recaptcha-response': token
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    location.href = $('#form-login').attr('action');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Não foi possível concluir o login. Tente novamente.');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    }).catch(function() {
        alert(form.dataset.recaptchaError);
        button.prop('disabled', false);
    });
});