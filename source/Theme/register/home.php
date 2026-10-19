<?= $this->layout("base", [
    'title' => $title, 
    'header' => false,
    'footer' => true
]); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/register/home.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-stellar-blue-nocturne-purple-gradient">
    <div class="row py-md-5 py-3 outer-box">
        <div class="d-block d-md-flex">
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="login-form border">
                    <h2 class="text-center"><?= $translator->translate("Cadastro") ?></h2>
                    <p class="text-center"><?= $translator->translate("Já possui conta?") ?> <a href="<?= url("login?r=".$redirect) ?>" class="link-stellar-blue"><?= $translator->translate("Entrar") ?></a></p>
                    <form id="form-register" class="needs-validation" method="post" action="<?= url("register/validate-email?r=".$redirect) ?>" data-recaptcha-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY, ENT_QUOTES, 'UTF-8') ?>" data-recaptcha-error="<?= htmlspecialchars($translator->translate("Não foi possível validar o reCAPTCHA. Tente novamente."), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                        <div class="mb-3">
                            <label for="user" class="form-label"><?= $translator->translate("Nome de Usuário") ?></label>
                            <input type="text" class="form-control input-stellar-blue" id="user" name="user" required minlength="3">
                            <small id="userCompleteHelp" class="form-text text-muted"><?= $translator->translate("Identificação única.") ?></small>
                            <div class="invalid-feedback" id="userInvalidFeedback" data-default="<?= $translator->translate("O nome de usuário deve ter pelo menos 3 caracteres.") ?>"></div>
                        </div>
                        <div class="mb-3">
                            <label for="complete_user" class="form-label"><?= $translator->translate("Nome Completo") ?></label>
                            <input type="text" class="form-control input-stellar-blue" id="complete_user" name="complete_user" required>
                            <small id="userCompleteHelp" class="form-text text-muted"><?= $translator->translate("Nome artístico/da loja") ?></small>
                            <div class="invalid-feedback" id="completeUserInvalidFeedback" data-default="<?= $translator->translate("O nome completo deve ter pelo menos 3 caracteres.") ?>"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><?= $translator->translate("E-mail") ?></label>
                            <input type="email" class="form-control input-stellar-blue" id="email" name="email" required>
                            <div class="invalid-feedback" id="emailInvalidFeedback" data-default="<?= $translator->translate("Por favor, insira um e-mail válido.") ?>">
                                <?= $translator->translate("Por favor, insira um e-mail válido.") ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label"><?= $translator->translate("Senha") ?></label>
                            <input type="password" class="form-control input-stellar-blue" id="senha" name="senha" required>
                            <div class="invalid-feedback" id="senhaInvalidFeedback" data-default="<?= $translator->translate("A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.") ?>">
                                <?= $translator->translate("A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.") ?>
                            </div>
                        </div>
                        <div class="mb-3 position">
                            <label for="confirmacao-senha" class="form-label"><?= $translator->translate("Confirmação de senha") ?></label>
                            <input type="password" class="form-control input-stellar-blue" id="confirmacao-senha" name="confirmacao-senha" required>
                            <div class="invalid-feedback" id="confirmacaoSenhaInvalidFeedback" data-default="<?= $translator->translate("As senhas não coincidem.") ?>">
                                <?= $translator->translate("As senhas não coincidem.") ?>
                            </div>
                        </div>
                        <?php if (!RECAPTCHA_ENABLED): ?>
                            <div class="alert alert-warning" role="alert"><?= $translator->translate("Verificação anti-robô indisponível no momento.") ?></div>
                        <?php endif; ?>
                        <div id="register-error" class="alert alert-danger d-none" role="alert"></div>
                        <!-- <div class="mb-3 position d-flex">
                            <input type="checkbox" class="form-check-input checkbox-kiklit-2" id="aceito-termos" name="aceito-termos" required>
                            <label class="form-check label" for="aceito-termos">Li e aceito as <a href="<?= url('legal/privacy')?>" target="_blank" class="link-kitlit-2">Políticas de Privacidade</a> e os <a href="<?= url('legal/terms')?>" target="_blank" class="link-kitlit-2">Termos de Uso</a></label>
                        </div> -->
                        <div class="d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-nocturne-purple" id="btn-confirm-register" <?= RECAPTCHA_ENABLED ? '' : 'disabled' ?>>
                                <span id="spinner-confirm" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                <span id="text-confirm"><?= $translator->translate("Confirmar") ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="<?= url('assets/image/luna.svg') ?>" alt="Login Image" class="img-fluid" style="max-width: 60%;">
            </div>
        </div>
    </div>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<?php if (RECAPTCHA_ENABLED): ?><script src="https://www.google.com/recaptcha/api.js?render=<?= rawurlencode(RECAPTCHA_SITE_KEY) ?>&amp;hl=<?= $translator->getLang() === 'pt-br' ? 'pt-BR' : 'en' ?>" defer></script><?php endif; ?>
<script src="<?= url("assets/js/recaptcha.js") ?>" defer></script>
<script src="<?= url("assets/js/register/home.js") ?>" defer></script>
<?= $this->stop() ?>