<?= $this->layout("base", $layout); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/store/details.css") ?>">
<link rel="stylesheet" href="<?= url("assets/css/stock/home.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<?php
    $storeName = !empty($store['nome']) ? $store['nome'] : 'Loja sem nome';
    $storeUsername = !empty($store['nome_unico']) ? '@' . $store['nome_unico'] : '@loja';
    $storeDescription = !empty($store['descricao']) ? $store['descricao'] : 'Sem descricao cadastrada.';
    $storePhoto = !empty($store['foto']) ? storageURL($store['foto']) : '';
    $storeId = !empty($store['codigo']) ? (int) $store['codigo'] : 0;
    $storeInitial = strtoupper(substr(trim($storeName), 0, 1));
    $bannerPlaceholder = url('assets/image/800x400.png');
?>

<section class="minimum-height store-details-page py-4">
    <div class="store-profile-top">
        <div class="store-banner mb-0">
            <img src="<?= $bannerPlaceholder ?>" alt="Banner da loja <?= ($storeName) ?>" class="store-banner-image">
            <div class="store-banner-overlay"></div>
            <div class="store-banner-actions">
                <a href="<?= url('store/id/' . $storeId) ?>" class="btn btn-light store-follow-btn">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Ver Loja
                </a>
            </div>
        </div>
    </div>

    <div class="container store-main-layout">
        <div class="row g-4 align-items-start">
            <div class="col-lg-2 store-profile-column">
                <aside class="store-profile-panel store-profile-overview">
                    <div class="store-avatar-wrap">
                        <?php if (!empty($storePhoto)): ?>
                            <img src="<?= $storePhoto ?>" class="store-avatar" alt="Foto da loja <?= ($storeName) ?>">
                        <?php else: ?>
                            <div class="store-avatar store-avatar-fallback"><?= ($storeInitial) ?></div>
                        <?php endif; ?>
                    </div>

                    <h1 class="store-name mb-1"><?= ($storeName) ?></h1>
                    <p class="store-username mb-2"><?= ($storeUsername) ?></p>
                    <p class="store-description mb-4"><?= ($storeDescription) ?></p>

                    <div class="alert alert-light border small mb-0">
                        Clique no coração para incluir ou remover produtos da vitrine publica.
                    </div>
                </aside>
            </div>

            <div class="col-lg-10 store-catalog-column">
                <div class="store-details-card">
                    <div class="store-content-panel">
                        <div class="store-content-header">
                            <div>
                                <p class="store-content-subtitle mb-1">gestao</p>
                                <h2 class="store-content-title mb-0">Selecione os produtos da vitrine</h2>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12 d-flex gap-2 flex-wrap align-items-center store-catalog-toolbar">
                                <div class="d-flex gap-2 flex-wrap align-items-center store-catalog-filters">
									<button type="button" class="btn btn-stellar-blue btn-md">
										<i class="fa-solid"></i>
										Produtos
									</button>
									<button type="button" class="btn btn-stellar-blue btn-md">
										<i class="fa-solid"></i>
										Coleções
									</button>
								</div>
                                <div class="ms-auto text-end store-catalog-search">
                                    <div class="store-search-wrap">
                                        <i class="fa-solid fa-search store-search-icon"></i>
                                        <input type="search" id="storeManageSearchInput" class="store-search-input" aria-label="Buscar no catalogo" placeholder="Buscar produtos...">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div id="storeManageProductsList" class="row g-3" data-store-id="<?= $storeId ?>">
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <div class="col-md-6 col-xl-2">
                                            <div class="store-product-card is-placeholder h-100">
                                                <div class="store-product-image placeholder-glow">
                                                    <span class="placeholder w-100 h-100 d-block"></span>
                                                </div>
                                                <div class="store-product-info">
                                                    <p class="store-product-name mb-1">Carregando...</p>
                                                    <p class="store-product-price mb-0">R$ 0,00</p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="<?= url('assets/js/store/manage.js?t=' . time()) ?>"></script>
<?= $this->stop() ?>
