<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="<?= url("assets/css/settings/store.css") ?>" rel="stylesheet" />
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <?= $menu ?>
        <div class="col-md-9 col-12 ps-5 p-3">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="px-5">
                        <h2 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Editar Loja") ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="login-form p-5">
                        <form id="settings-store-form" method="post" action="<?= url('settings/store') ?>" enctype="multipart/form-data" data-success="<?= $translator->translate("Alteração Salva!") ?>" data-error="<?= $translator->translate("Erro ao Salvar!") ?>">
                            <div class="row g-4 mb-4">
                                <div class="col-xxl-4 col-12">
                                    <div class="h-100 d-flex flex-column align-items-center">
                                        <label class="form-label mb-2" for="store-photo-input"><?= $translator->translate("Logo da loja") ?></label>
                                        <div id="store-photo-drop-area" class="image-drop-area store-image-drop-area store-photo-drop-area d-flex align-items-center justify-content-center">
                                            <?php if (!empty($store['loja_foto'])): ?>
                                                <img id="store-photo-preview" src="<?= storageURL($store['loja_foto']) ?>" alt="Preview do logo da loja">
                                            <?php else: ?>
                                                <span id="store-photo-drop-text"><?= $translator->translate("Clique ou arraste uma imagem aqui") ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="form-text text-muted d-block mt-2">
                                            <?= $translator->translate("Tamanho máximo: 5MB") ?>
                                        </small>
                                        <input type="file" id="store-photo-input" name="storePhoto" accept="image/*" class="d-none" data-size-error="<?= $translator->translate("A imagem deve ter no máximo 5MB") ?>">
                                    </div>
                                </div>
                                <div class="col-xxl-8 col-12">
                                    <div class="h-100 d-flex flex-column align-items-center">
                                        <label class="form-label mb-2" for="store-banner-input"><?= $translator->translate("Banner da loja") ?></label>
                                        <div id="store-banner-drop-area" class="image-drop-area store-image-drop-area store-banner-drop-area d-flex align-items-center justify-content-center">
                                            <?php if (!empty($store['loja_banner'])): ?>
                                                <img id="store-banner-preview" src="<?= storageURL($store['loja_banner']) ?>" alt="Preview do banner da loja">
                                            <?php else: ?>
                                                <span id="store-banner-drop-text"><?= $translator->translate("Clique ou arraste uma imagem aqui") ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="form-text text-muted d-block mt-2">
                                            <?= $translator->translate("Tamanho máximo: 5MB") ?>
                                        </small>
                                        <input type="file" id="store-banner-input" name="storeBanner" accept="image/*" class="d-none" data-size-error="<?= $translator->translate("A imagem deve ter no máximo 5MB") ?>">
                                    </div>
                                </div>
                            </div>    
                            <div class="mb-3">
                                <label for="nome" class="form-label"><?= $translator->translate("Nome") ?></label>
                                <input type="text" class="form-control input-stellar-blue" id="nome" name="nome" required="true" value="<?= $store['loja_nome'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label"><?= $translator->translate("Bio") ?></label>
                                <textarea class="form-control input-stellar-blue" id="descricao" name="descricao" rows="3"  ><?= $store['loja_descricao'] ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="filter-name" class="form-label"><?= $translator->translate("Moeda") ?></label>
                                <select class="form-select input-stellar-blue" name="storeCountry" id="storeCountry" data-default="<?= $store['loja_pais'] ?>">
                                    <option value="" selected><?= $translator->translate("Selecione a Moeda") ?></option>
                                </select>
                            </div> 
                            <input type="hidden" id="storeCurrency" name="storeCurrency" value='<?= $store['loja_moeda'] ?>'>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= url('settings/store') ?>" class="btn btn-gray"><?= $translator->translate("Descartar") ?></a>
                                <button type="submit" class="btn btn-stellar-blue w-25" id="settings-store-submit"><?= $translator->translate("Salvar") ?></button>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const translator = {
        selectCountry: "<?= $translator->translate("Selecione a Moeda") ?>"
    };
</script>
<script src="<?= url("assets/js/settings/store.js") ?>"></script>
<?= $this->stop() ?>