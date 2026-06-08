<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/password-reset/home.css") ?>">
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
                    <h2 class="text-center"><?= $translator->translate("Esqueci minha senha") ?></h2>
                    <form id="form-password-reset" method="get" action="<?= url("password-reset/code?r=" . $redirect) ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label"><?= $translator->translate("E-mail") ?></label>
                            <input type="email" class="form-control input-stellar-blue" id="email" name="email" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= url('login?r=' . $redirect) ?>" class="link-stellar-blue"><?= $translator->translate("Cancelar") ?></a>
                            <button type="submit" class="btn btn-nocturne-purple" id="btn-confirm-password-reset">
                                <span id="spinner-confirm" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                <span id="text-confirm"><?= $translator->translate("Enviar e-mail") ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/js/password-reset/home.js") ?>"></script>
<?= $this->stop() ?>