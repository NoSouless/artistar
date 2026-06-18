<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="<?= url("assets/css/settings/categories/details.css") ?>" rel="stylesheet" />
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <?= $menu ?>
        <div class="col-md-9 col-12 ps-5 p-3">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="px-5">
                        <h2 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Categoria: ").$category['nome'] ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="login-form p-5">
                        <form id="settings-profile-form" method="post" action="<?= url('settings/categories/update') ?>" enctype="multipart/form-data" data-success="<?= $translator->translate("Alteração Salva!") ?>" data-error="<?= $translator->translate("Erro ao Salvar!") ?>">
                            <div class="row mb-3">
                                <div class="mb-3 col-lg-4 col-12 d-flex flex-column align-items-center">
                                    <label class="form-label mb-2" for="thumbnail-input"><?= $translator->translate("Thumbnail") ?></label>
                                    <div id="thumbnail-drop-area" class="image-drop-area profile-image-drop-area thumbnail-drop-area d-flex align-items-center justify-content-center">
                                        <?php if (!empty($category['foto'])): ?>
                                            <img id="thumbnail-preview" src="<?= storageURL($category['foto']) ?>">
                                        <?php else: ?>
                                            <span id="thumbnail-drop-text"><?= $translator->translate("Clique ou arraste uma imagem aqui") ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted d-block mt-2">
                                        <?= $translator->translate("Tamanho máximo: 5MB") ?>
                                    </small>
                                    <input type="file" id="thumbnail-input" name="thumbnail" accept="image/*" class="d-none" data-size-error="<?= $translator->translate("A imagem deve ter no máximo 5MB") ?>">
                                </div>
                                <div class="mb-3 col-lg-8 col-12">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label"><?= $translator->translate("Nome") ?></label>
                                        <input type="text" class="form-control input-stellar-blue" id="nome" name="name" required="true" value="<?= $category['nome'] ?>">
                                    </div>      
                                    <div class="mb-3 d-flex justify-content-between">      
                                        <div class="form-check form-switch form-switch-sm d-block">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckActive" name="active" value="1" <?= $category['ativa'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="flexSwitchCheckActive">
                                                <?= $translator->translate("Ativa") ?>
                                            </label>
                                        </div>                                  
                                        <div class="form-check form-switch form-switch-sm d-block">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckPublic" name="public" value="1" <?= $category['publica'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="flexSwitchCheckPublic">
                                                <?= $translator->translate("Pública") ?>
                                            </label>
                                        </div>
                                    </div>      
                                </div>
                            </div> 
                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
                            <div class="row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?= url('settings/categories') ?>" class="btn btn-gray"><?= $translator->translate("Voltar") ?></a>
                                    <div class="w-50 d-flex justify-content-end gap-2">
                                        <button class="btn btn-danger w-50" id="delete-category"><?= $translator->translate("Excluir") ?></button>
                                        <button type="submit" class="btn btn-stellar-blue w-50" id="update-category"><?= $translator->translate("Salvar") ?></button>
                                    </div>
                                </div>
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
<script src="<?= url("assets/js/settings/categories/details.js") ?>"></script>
<?= $this->stop() ?>