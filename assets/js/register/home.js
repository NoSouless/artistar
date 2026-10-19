document.addEventListener("DOMContentLoaded", function () {

    function disableRegisterBtn() {
        $('#btn-confirm-register').prop('disabled', true);
        $('#spinner-confirm').show();
        $('#text-confirm').hide();
    }

    function enableRegisterBtn() {
        $('#btn-confirm-register').prop('disabled', false);
        $('#spinner-confirm').hide();
        $('#text-confirm').show();
    }

    function validateUser() {
        let isValid = true;
        let user = $('#user').val();
        if (user.length < 3) {
            $('#user').addClass('is-invalid').removeClass('is-valid'); // Marca o campo como inválido
            $('#userInvalidFeedback').text($('#userInvalidFeedback').data('default')); // Usa a mensagem de erro padrão
            isValid = false; // Define como inválido
        } else {
            $('#user').removeClass('is-valid').removeClass('is-invalid');
        }
        return isValid;
    }

    function validateUserDisponibility() {
        let user = $('#user').val();
        let isValid = true;
        $.ajax({
            url: '/register/validate-user',
            type: 'POST',
            data: {
                user: user,
            },
            async: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    $('#user').removeClass('is-invalid').addClass('is-valid');
                    document.getElementById('user').setCustomValidity(''); // Limpa erro customizado
                } else {
                    $('#user').addClass('is-invalid').removeClass('is-valid');
                    $('#userInvalidFeedback').text(response.message);
                    document.getElementById('user').setCustomValidity(response.message); // Define erro customizado
                    isValid = false;
                }
            },
            error: function (error) {
                console.log('An error occurred');
            }
        });
        return isValid;
    }

    function validateCompleteUsername() {
        let complete_user = $('#complete_user').val();
        let isValid = true;

        if (complete_user.length < 3) {
            $('#complete_user').addClass('is-invalid').removeClass('is-valid'); // Marca o campo como inválido
            $('#completeUserInvalidFeedback').text($('#completeUserInvalidFeedback').data('default')); // Usa a mensagem de erro padrão
            isValid = false; // Define como inválido
        } else {
            $('#complete_user').addClass('is-valid').removeClass('is-invalid');
        }

        return isValid;
    }

    function validateEmail() {
        let email = $('#email').val();
        let isValid = true;

        // Regex para validação de email
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            $('#email').addClass('is-invalid').removeClass('is-valid'); // Marca o campo como inválido
            $('#emailInvalidFeedback').text($('#emailInvalidFeedback').data('default')); // Usa a mensagem de erro padrão
            // $('#email').next('.invalid-feedback').text('Por favor, insira um email válido.');
            isValid = false; // Define como inválido
        } else {
            $('#email').removeClass('is-invalid').removeClass('is-valid');
        }

        return isValid;
    }

    function validateEmailDisponibility() {
        let email = $('#email').val();
        let isValid = true;
        $.ajax({
            url: '/register/validate-email',
            type: 'POST',
            data: {
                email: email,
            },
            async: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    $('#email').removeClass('is-invalid').addClass('is-valid');
                    document.getElementById('email').setCustomValidity(''); // Limpa erro customizado
                } else {
                    $('#email').addClass('is-invalid').removeClass('is-valid');
                    $('#emailInvalidFeedback').text(response.message);
                    document.getElementById('email').setCustomValidity(response.message); // Define erro customizado
                    isValid = false;
                }
            },
            error: function (error) {
                console.log('An error occurred');
            }
        });
        return isValid;
    }

    function validatePasswordConfirmation() {
        
        let senha = $('#senha').val();
        let confirmacaoSenha = $('#confirmacao-senha').val();
        let isValid = true;

        if (senha !== confirmacaoSenha) {
            $('#confirmacao-senha').addClass('is-invalid').removeClass('is-valid'); // Marca o campo como inválido
            $('#confirmacaoSenhaInvalidFeedback').text($('#confirmacaoSenhaInvalidFeedback').data('default')); // Usa a mensagem de erro padrão
            // $('#confirmacao-senha').next('.invalid-feedback').text('As senhas não coincidem.');
            document.getElementById('confirmacao-senha').setCustomValidity($('#confirmacaoSenhaInvalidFeedback').data('default')); // Define erro customizado
            isValid = false; // Define como inválido
        } else {
            $('#confirmacao-senha').removeClass('is-invalid').addClass('is-valid'); // Marca como válido
            document.getElementById('confirmacao-senha').setCustomValidity(''); // Limpa erro customizado
        }
        $('#form-register').removeClass('was-validated');

        return isValid;
    }

    function validatePassword() {
        let isValid = true; // Variável para controlar a validade do formulário
        document.getElementById('senha').setCustomValidity('');
        // Validação adicional para senha
        let senha = $('#senha').val();
        let senhaForteRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!senhaForteRegex.test(senha)) {
            $('#senha').addClass('is-invalid').removeClass('is-valid'); // Marca o campo como inválido
            $('#senhaInvalidFeedback').text($('#senhaInvalidFeedback').data('default')); // Usa a mensagem de erro padrão
            // $('#senha').next('.invalid-feedback').text('A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.');
            isValid = false; // Define como inválido
        } else {
            $('#senha').removeClass('is-invalid').addClass('is-valid'); // Marca como válido
        }

        let confirmation = validatePasswordConfirmation();

        return isValid && confirmation;
    }


    $('#user').on('input', function () {
        //Ao digitar espaços, trocar por -
        this.value = this.value.replace(/\s+/g, '-');
        //Apenas permitir letras, numeros, _, - e .
        this.value = this.value.replace(/[^a-zA-Z0-9_.-]/g, '');
        //Caracteres minúsculos
        this.value = this.value.toLowerCase();
        //Colocar o valor no campo de nome completo
        $('#complete_user').val(this.value);
        
        validateUser();
        validateCompleteUsername();
    });

    $('#user').on('blur', function () {
        if(validateUser()) validateUserDisponibility();
    });

    $('#complete_user').on('input change', function () {
        validateCompleteUsername();
    });

    $('#email').on('input', function () {
        validateEmail();
    });

    $('#email').on('blur', function () {
        if(validateEmail()) validateEmailDisponibility();
    });

    $('#senha').on('input', function () {
        validatePassword();
    });

    $('#confirmacao-senha').on('input', function () {
        validatePasswordConfirmation();
    });

    const forms = document.querySelectorAll(".needs-validation");

    Array.from(forms).forEach(function (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Impede o envio padrão do formulário
            event.stopPropagation();

            disableRegisterBtn();

            let isValid = true;

            if (!validateUser()) {
                isValid = false;
            } else if (!validateUserDisponibility()) {
                isValid = false;
            }

            if (!validateCompleteUsername()) {
                isValid = false;
            }

            if (!validateEmail()) {
                isValid = false;
            } else if (!validateEmailDisponibility()) {
                isValid = false;
            }

            // Validação adicional para senha
            if (!validatePassword()) {
                isValid = false;
            }

            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                isValid = false; // Define como inválido
            }

            if (!isValid) {
                enableRegisterBtn();
                return; // Sai da função se algum campo for inválido
            }

            let user = $('#user').val();
            let complete_user = $('#complete_user').val();
            let email = $('#email').val();
            let password = $('#senha').val();

            // Envia os dados via AJAX
            executeRecaptcha('register', form.dataset.recaptchaSitekey).then(function(token) {
                $.ajax({
                    url: '/register',
                    type: 'POST',
                    data: {
                        user: user,
                        complete_user: complete_user,
                        email: email,
                        password: password,
                        'g-recaptcha-response': token
                    },
                    success: function (response) {
                        response = JSON.parse(response);
                        $('#register-error').addClass('d-none').text('');
                        if (response.code == 200) {
                            location.href = $('#form-register').attr('action');
                        } else {
                            $('#register-error').removeClass('d-none').text(response.message);
                            let errors = response.data || [];

                            errors.forEach(function (error) {
                                if (error.field === 'user') {
                                    $('#user').addClass('is-invalid').removeClass('is-valid');
                                    $('#user').next('.invalid-feedback').text(error.message);
                                    document.getElementById('user').setCustomValidity(error.message);
                                }
                                if (error.field === 'email') {
                                    $('#email').addClass('is-invalid').removeClass('is-valid');
                                    $('#email').next('.invalid-feedback').text(error.message);
                                    document.getElementById('email').setCustomValidity(error.message);
                                }
                            });

                            console.error(response.message);
                            enableRegisterBtn();
                        }
                    },
                    error: function () {
                        console.log('An error occurred');
                        enableRegisterBtn();
                    }
                });
            }).catch(function() {
                $('#register-error').removeClass('d-none').text(form.dataset.recaptchaError);
                enableRegisterBtn();
            });
        }, false);
    });
});