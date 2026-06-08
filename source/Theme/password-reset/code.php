<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/password-reset/new-password.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-nocturne-purple-stellar-blue-gradient">
    <!-- <div class="row w-75 h-75 outer-box"> -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="login-form border">
                <h2 class="text-center"><?= $translator->translate("Confirmação de E-mail") ?></h2>
                <form id="form-confirmation-code" method="get" action="<?= url("?r=" . $redirect) ?>">
                    <div class="mb-3">
                        <label for="codigo" class="form-label"><?= $translator->translate("Insira o código de confirmação enviado para o seu e-mail") ?></label>
                        <div class="d-flex justify-content-between">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo1" name="code[0]" maxlength="1" required style="text-transform: uppercase;">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo2" name="code[1]" maxlength="1" required style="text-transform: uppercase;">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo3" name="code[2]" maxlength="1" required style="text-transform: uppercase;">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo4" name="code[3]" maxlength="1" required style="text-transform: uppercase;">
                            <span class="mx-2">-</span>
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo5" name="code[4]" maxlength="1" required style="text-transform: uppercase;">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo6" name="code[5]" maxlength="1" required style="text-transform: uppercase;">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo7" name="code[6]" maxlength="1" required style="text-transform: uppercase;">
                            <input type="text" class="form-control text-center m-1 input-validate input-stellar-blue" id="codigo8" name="code[7]" maxlength="1" required style="text-transform: uppercase;">
                            <img src="<?= url("assets/images/loader.gif") ?>" id="loader" class="d-none" alt="Loading...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <a href="#" class="link-stellar-blue" id="resend-code">
                            <span id="spinner-resend" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                            <span id="text-resend"><?= $translator->translate("Reenviar") ?></span>
                        </a>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <a class="btn btn-gray mx-1" href="<?= url('auth/logout')?>"><?= $translator->translate("Cancelar") ?></a>
                        <button type="submit" class="btn btn-nocturne-purple" id="confirm-button">
                            <span id="spinner-confirm" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                            <span id="text-confirm"><?= $translator->translate("Confirmar") ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <!-- </div> -->
</section>

<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/js/password-reset/code.js") ?>"></script>

<?= $this->stop() ?>