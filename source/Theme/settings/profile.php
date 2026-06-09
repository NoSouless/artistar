<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="<?= url("assets/css/settings/profile.css") ?>" rel="stylesheet" />
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <?= $menu ?>
        <div class="col-md-9 col-12 ps-5 p-3">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="px-5">
                        <h2 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Editar Perfil ") ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="login-form p-5">
                        <form id="settings-profile-form" method="post" action="<?= url('settings/profile') ?>" enctype="multipart/form-data" data-success="<?= $translator->translate("Alteração Salva!") ?>" data-error="<?= $translator->translate("Erro ao Salvar!") ?>">
                            <div class="mb-3">
                                <label class="form-label mb-2" for="profile-photo-input"><?= $translator->translate("Foto de Perfil") ?></label>
                                <div id="profile-photo-drop-area" class="image-drop-area profile-image-drop-area profile-photo-drop-area d-flex align-items-center justify-content-center">
                                    <?php if (!empty($user['usuario_foto'])): ?>
                                        <img id="profile-photo-preview" src="<?= storageURL($user['usuario_foto']) ?>" alt="Preview da foto de perfil">
                                    <?php else: ?>
                                        <span id="profile-photo-drop-text"><?= $translator->translate("Clique ou arraste uma imagem aqui") ?></span>
                                    <?php endif; ?>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    <?= $translator->translate("Tamanho máximo: 5MB") ?>
                                </small>
                                <input type="file" id="profile-photo-input" name="profilePhoto" accept="image/*" class="d-none" data-size-error="<?= $translator->translate("A imagem deve ter no máximo 5MB") ?>">
                            </div>    
                            <div class="mb-3">
                                <label for="nome" class="form-label"><?= $translator->translate("Nome") ?></label>
                                <input type="text" class="form-control input-stellar-blue" id="nome" name="nome" required="true" value="<?= $user['usuario_nome_completo'] ?>">
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= url('settings/profile') ?>" class="btn btn-gray"><?= $translator->translate("Descartar") ?></a>
                                <button type="submit" class="btn btn-nocturne-purple w-25" id="settings-profile-submit"><?= $translator->translate("Salvar") ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/js/settings/profile.js") ?>"></script>
<?= $this->stop() ?>