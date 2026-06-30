<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/store/details.css") ?>">
<link rel="stylesheet" href="<?= url("assets/css/stock/home.css") ?>">
<link rel="stylesheet" href="<?= url("assets/css/store/manage.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>

<form class="minimum-height store-details-page py-4" method="post" enctype="multipart/form-data" id="storeManageForm" action="<?= url('store/showcase') ?>">
    <div id="storeBannerWrap" class="store-profile-top" role="button" tabindex="0" style="background-image:url('<?= $storeBanner ?>'); background-size:cover; background-position:center;">
		<div class="mb-0 container d-md-flex flex-column  justify-content-end" style="min-height: 230px;">
			<div class="row d-block d-md-none mb-5 pb-2"></div>
			<div class="row ">
                <div class="col-12 d-flex gap-2 justify-content-end mb-2">
                    <a href="<?= url($storeUsername) ?>" class="btn btn-polar-gray store-follow-btn">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        <?= $translator->translate("Ver Loja") ?>
                    </a>
                    <button type="submit" class="btn btn-stellar-blue store-follow-btn" id="saveShowcaseBtn">
                        <i class="fa-solid fa-save me-1"></i>
                        <?= $translator->translate("Salvar") ?>
                    </button>
				</div>
			</div>
		</div>
	</div>

    <div class="container store-main-layout">
        <div class="row g-4 align-items-start">
            <div class="col-lg-2 store-profile-column">
                <aside class="store-profile-panel store-profile-overview">
                    <div class="store-avatar-wrap" id="storePhotoWrap" role="button" tabindex="0" aria-label="Editar foto de perfil da loja">
                        <?php if (!empty($storePhoto)): ?>
                            <img src="<?= $storePhoto ?>" class="store-avatar" id="storePhotoPreview" alt="Foto da loja <?= ($storeName) ?>">
                        <?php else: ?>
                            <div class="store-avatar store-avatar-fallback" id="storePhotoPreview"><?= ($storeInitial) ?></div>
                        <?php endif; ?>
                        
                    </div>

                    <h1 class="store-name mb-1"><?= ($storeName) ?></h1>
                    <p class="store-description mb-4"><?= ($storeDescription) ?></p>

                    <a href="<?= url('settings/store') ?>" class="btn btn-outline-stellar-blue btn-sm ms-2" aria-label="Editar vitrine da loja">
                        <i class="fa-solid fa-pen"></i>
                        <?= $translator->translate("Editar Loja") ?>
                    </a>
                </aside>
            </div>

            <div class="col-lg-10 store-catalog-column">
                <div class="store-details-card">
                    <div class="store-content-panel">
                        <div class="store-content-header">
                            <div>
                                <p class="store-content-subtitle mb-1"><?= $translator->translate("gestao") ?></p>
                                <h2 class="store-content-title mb-0"><?= $translator->translate("Selecione os produtos da vitrine") ?></h2>
                            </div>
                        </div>

                        <div class="g-3 mt-3">
                            <div class="col-12 d-flex gap-2 flex-wrap align-items-center store-catalog-toolbar">
                                <div class="d-flex gap-2 flex-wrap align-items-center store-catalog-filters">
                                    <a href="<?= url('settings/categories') ?>" class="btn btn-outline-stellar-blue btn-sm ms-2" aria-label="Editar vitrine da loja">
                                        <i class="fa-solid fa-pen"></i>
                                        <?= $translator->translate("Editar Categorias") ?>
                                    </a>
                                </div>
                                <div class="ms-auto text-end store-catalog-search">
                                    <div class="store-search-wrap">
                                        <i class="fa-solid fa-search store-search-icon"></i>
                                        <input type="search" id="storeManageSearchInput" class="store-search-input" aria-label="<?= $translator->translate("Buscar no catalogo") ?>" placeholder="<?= $translator->translate("Buscar produtos...") ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="row" id="storeManageSkeletonList">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento placeholder-glow">
                                                <div class="card h-100 d-flex flex-column product-card store-product-card is-placeholder">
                                                    <div class="card-img-top position-relative pt-2 px-2">
                                                        <div class="img-fluid rounded thumbnail-product store-product-image">
                                                            <span class="placeholder w-100 h-100 d-block rounded"></span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body d-flex flex-column">
                                                        <h5 class="card-title d-flex justify-content-between align-items-center">
                                                            <span class="nome-produto placeholder col-7"></span>
                                                            <span class="placeholder col-3"></span>
                                                        </h5>
                                                        <p class="card-text mt-auto">
                                                            <span class="badge bg-light text-dark me-1"></span>
                                                        </p>
                                                        <div class="card-text">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="col-6">
                                                                    <span class="placeholder col-6"></span><br>
                                                                    <span class="placeholder col-10"></span>
                                                                </div>
                                                                <div class="col-6 text-end">
                                                                    <span class="placeholder col-6"></span><br>
                                                                    <span class="placeholder col-10"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                </div>
                                <div class="row" id="storeManageSelectedProductsList"></div>
                                <div class="row" id="emptySelectedProductsList" style="display:none;">
                                    <div class="col-12 text-center">
                                        <p class="text-muted"><?= $translator->translate("Nenhum produto selecionado.") ?></p>
                                    </div>
                                </div>
                                <hr id="storeManageProductsSeparator" class="m-0 mb-4 w-100">
                                <div class="row" id="storeManageUnselectedProductsList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    const messages = {
        invalidStore: "<?= $translator->translate("Loja invalida para carregar produtos.") ?>",
        invalidApiResponse: "<?= $translator->translate("Erro ao processar resposta da API.") ?>",
        productsUnavailable: "<?= $translator->translate("Nao foi possivel carregar os produtos.") ?>",
        searchFailed: "<?= $translator->translate("Falha ao buscar produtos da loja.") ?>",
        processResponseFailed: "<?= $translator->translate("Nao foi possivel processar a resposta da API.") ?>",
        updateFailed: "<?= $translator->translate("Falha ao atualizar produto da vitrine.") ?>",
        saveOrderFailed: "<?= $translator->translate("Falha ao salvar a nova ordem dos produtos.") ?>",
        noProductsInStore: "<?= $translator->translate("Nenhum produto encontrado na loja.") ?>",
        noSelectedProducts: "<?= $translator->translate("Nenhum produto encontrado na vitrine.") ?>",
        successUpdate: "<?= $translator->translate("Alterações salvas com sucesso!") ?>",
        errorTitle: "<?= $translator->translate("Erro") ?>",
        errorBody: "<?= $translator->translate("Um erro ocorreu ao salvar as alterações. Por favor, tente novamente.") ?>"
    };
</script>
<script src="<?= url('assets/js/store/manage.js?t=' . time()) ?>"></script>
<?= $this->stop() ?>
