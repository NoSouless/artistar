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
	$isOwner = !empty($isOwner);
	$followersCount = isset($followersCount) ? (int) $followersCount : 0;
	$loginRedirect = base64_encode(urlencode($_SERVER['REQUEST_URI'] ?? '/'));
	$storeInitial = strtoupper(substr(trim($storeName), 0, 1));
	$collectionPlaceholders = [
		[
			'nome' => 'Colecao A',
			'itens' => ['Item 1', 'Item 2', 'Item 3']
		],
		[
			'nome' => 'Colecao B',
			'itens' => ['Item 4', 'Item 5', 'Item 6']
		]
	];
	$bannerPlaceholder = url('assets/image/800x400.png');
?>

<section class="minimum-height store-details-page py-4">
	<div class="store-profile-top" style="background-image:url('<?= $bannerPlaceholder ?>'); background-size:cover; background-position:center;">
		<div class="mb-0 container d-md-flex flex-column  justify-content-end" style="min-height: 230px;">
			<div class="row d-block d-md-none mb-5 pb-2"></div>
			<div class="row ">
				<div class="col-12 d-flex gap-2 justify-content-end mb-2">
					<button type="button" class="btn store-action-icon" aria-label="Instagram da loja">
						<i class="fa-brands fa-instagram"></i>
					</button>
					<button type="button" class="btn store-action-icon" aria-label="Twitter da loja">
						<i class="fa-brands fa-x-twitter"></i>
					</button>
					<?php if ($isOwner): ?>
						<a href="<?= url('store/showcase') ?>" class="btn btn-polar-gray store-follow-btn">
							<i class="fa-solid fa-pen"></i>
							Editar Vitrine
						</a>
					<?php elseif ($logado): ?>
						<button type="button" class="btn btn-stellar-blue store-follow-btn" data-store-id="<?= $storeId ?>" data-login-redirect="<?= $loginRedirect ?>">
							<i class="fa-solid fa-plus"></i>
							Seguir
						</button>
					<?php else: ?>
						<a href="<?= url('login?r=' . $loginRedirect) ?>" class="btn btn-stellar-blue store-follow-btn">
							<i class="fa-solid fa-plus"></i>
							Seguir
						</a>
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
					<p class="store-description mb-4"><?= ($storeDescription) ?></p>
                    <p class="store-name mb-1">Santos - SP</p>

					<div class="store-stats" style="display:flex; flex-wrap:nowrap; gap:0.65rem; align-items:stretch;">
						<div class="store-stat-item" style="flex:1 1 0; min-width:0;">
							<span class="store-stat-value"><?= count($products) ?></span>
							<span class="store-stat-label">produtos</span>
						</div>
						<div class="store-stat-item" style="flex:1 1 0; min-width:0;">
							<span class="store-stat-value"><?= $followersCount ?></span>
							<span class="store-stat-label">seguidores</span>
						</div>
					</div>
				</aside>
			</div>

			<div class="col-lg-10 store-catalog-column">
				<div class="store-details-card">
					<div class="store-content-panel">
						<div class="store-content-header">
							<div>
								<p class="store-content-subtitle mb-1">catalogo</p>
								<h2 class="store-content-title mb-0">Destaques da loja</h2>
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

							<div class="col-12">
								<div id="storeProductsList" class="row g-3" data-store-id="<?= $storeId ?>">
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
<script src="<?= url('assets/js/store/details.js?t=' . time()) ?>"></script>
<?= $this->stop() ?>