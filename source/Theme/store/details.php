<?= $this->layout("base", $layout); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/store/details.css") ?>">
<link rel="stylesheet" href="<?= url("assets/css/stock/home.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="minimum-height store-details-page py-4">
	<div class="store-profile-top" style="background-image:url('<?= $bannerPlaceholder ?>'); background-size:cover; background-position:center;">
		<div class="mb-0 container d-md-flex flex-column  justify-content-end" style="min-height: 230px;">
			<div class="row d-block d-md-none mb-5 pb-2"></div>
			<div class="row ">
				<div class="col-12 d-flex gap-2 justify-content-end mb-2">
					<?php if (!$isOwner): ?>
						<?php if ($logado): ?>
							<!-- <button type="button" class="btn btn-stellar-blue store-follow-btn" data-store-id="<?= $storeId ?>" data-login-redirect="<?= $loginRedirect ?>">
								<i class="fa-solid fa-plus"></i>
								Seguir
							</button> -->
						<?php else: ?>
							<!-- <a href="<?= url('login?r=' . $loginRedirect) ?>" class="btn btn-stellar-blue store-follow-btn">
								<i class="fa-solid fa-plus"></i>
								Seguir
							</a> -->
						<?php endif; ?>
					<?php endif; ?>
				</div>
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
					<p class="store-description"><?= ($storeDescription) ?></p>
					<?php if ($isOwner): ?>
						<a href="<?= url('settings/store') ?>" class="btn btn-outline-stellar-blue btn-sm ms-2" aria-label="Editar vitrine da loja">
							<i class="fa-solid fa-pen"></i>
							Editar Loja
						</a>
					<?php endif; ?>
					<div class="store-stats mt-4" style="display:flex; flex-wrap:nowrap; gap:0.65rem; align-items:stretch;">
						<div class="store-stat-item" style="flex:1 1 0; min-width:0;">
							<span class="store-stat-value"><?= $products ?></span>
							<span class="store-stat-label">produtos</span>
						</div>
						<!-- <div class="store-stat-item" style="flex:1 1 0; min-width:0;">
							<span class="store-stat-value"><?= $followersCount ?></span>
							<span class="store-stat-label">seguidores</span>
						</div> -->
					</div>
				</aside>
			</div>

			<div class="col-lg-10 store-catalog-column">
				<div class="store-details-card">
					<div class="store-content-panel">
						<div class="store-content-header">
							<div>
								<p class="store-content-subtitle mb-1">vitrine</p>
								<h2 class="store-content-title mb-0">
									Destaques da loja 
									<?php if ($isOwner): ?>
										<a href="<?= url('store/showcase') ?>" class="btn btn-outline-stellar-blue btn-sm ms-2" aria-label="Editar vitrine da loja">
											<i class="fa-solid fa-pen"></i>
											Editar Vitrine
										</a>
									<?php endif; ?>
								</h2>
							</div>
						</div>

						<div class="row g-3 mt-3">
							<div class="col-12 d-flex gap-2 flex-wrap align-items-center store-catalog-toolbar">
								<div class="d-flex gap-2 flex-wrap align-items-center store-catalog-filters">
									<!-- <button type="button" class="btn btn-stellar-blue btn-md">
										<i class="fa-solid"></i>
										Produtos
									</button>
									<button type="button" class="btn btn-stellar-blue btn-md">
										<i class="fa-solid"></i>
										Coleções
									</button> -->
								</div>
								<div class="ms-auto text-end store-catalog-search">
									<div class="store-search-wrap">
										<i class="fa-solid fa-search store-search-icon"></i>
										<input type="search" id="storeSearchInput" class="store-search-input" aria-label="Buscar no catalogo" placeholder="Buscar produtos...">
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
                                <div class="row" id="storeManageSelectedProductsList" data-store-id="<?= $storeId ?>"></div>
                                <div class="row" id="emptySelectedProductsList" style="display:none;">
                                    <div class="col-12 text-center">
                                        <p class="text-muted">Nenhum produto selecionado.</p>
                                    </div>
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
<script>
	const storeId = "<?= $storeId ?>";
	const currency = "<?= $store['moeda'] ?>";
</script>
<script src="<?= url('assets/js/store/details.js?t=' . time()) ?>"></script>
<?= $this->stop() ?>