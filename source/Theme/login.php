<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/login.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-nocturne-purple-stellar-blue-gradient">
    <div class="row min-h-75 outer-box">
        <div class="py-5 d-flex flex-column-reverse flex-md-row">
            <div class="col-md-6 d-flex align-items-center justify-content-center order-2 order-md-1">
                <img src="<?= url('assets/image/luna.svg') ?>" alt="Login Image" class="img-fluid" style="max-width: 60%;">
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center order-1 order-md-2">
                <div class="login-form border">
                    <h2 class="text-center"><?= $translator->translate("Login") ?></h2>
                    <p class="text-center"><?= $translator->translate("Não possui conta?") ?> <a href="<?= url("register?r=" . $redirect) ?>" class="link-stellar-blue"><?= $translator->translate("Cadastre-se") ?></a></p>
                    <form id="form-login" method="post" action="<?= url("?r=" . $redirect) ?>" data-recaptcha-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY, ENT_QUOTES, 'UTF-8') ?>" data-recaptcha-error="<?= htmlspecialchars($translator->translate("Não foi possível validar o reCAPTCHA. Tente novamente."), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label"><?= $translator->translate("E-mail") ?></label>
                            <input type="email" class="form-control input-stellar-blue" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label"><?= $translator->translate("Senha") ?></label>
                            <input type="password" class="form-control input-stellar-blue" id="senha" name="senha" required>
                        </div>
                        <?php if (!RECAPTCHA_ENABLED): ?>
                            <div class="alert alert-warning" role="alert"><?= $translator->translate("Verificação anti-robô indisponível no momento.") ?></div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= url("password-reset?r=" . $redirect) ?>" class="link-stellar-blue"><?= $translator->translate("Esqueci minha senha") ?></a>
                            <button type="submit" class="btn btn-nocturne-purple w-25" <?= RECAPTCHA_ENABLED ? '' : 'disabled' ?>><?= $translator->translate("Entrar") ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->stop() ?>

<?= $this->start("js") ?>
<?php if (RECAPTCHA_ENABLED): ?><script src="https://www.google.com/recaptcha/api.js?render=<?= rawurlencode(RECAPTCHA_SITE_KEY) ?>&amp;hl=<?= $translator->getLang() === 'pt-br' ? 'pt-BR' : 'en' ?>" defer></script><?php endif; ?>
<script src="<?= url("assets/js/recaptcha.js") ?>" defer></script>
<script src="<?= url("assets/js/login.js") ?>" defer></script>
<?= $this->stop() ?>