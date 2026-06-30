<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= url("assets/css/stock/home.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="minimum-height">
    <section class="section-banner">
        <div class="container">
            <div class="row">
                <div class="py-5 avoid-navbar">
                    <div class="row align-items-center">
                        <div class="col-xl-6 col-12">
                            <a class="h1 mb-3 link-nocturne-purple" href="<?= url('stock')?>"><?= $translator->translate('Inventário') ?></a>
                            <p class="fs-4"><?= $translator->translate('Últimas informações de estoque') ?></p>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <span class="dot bg-success me-2 rounded-5" style="height:10px; width:10px;"></span> <a href="/stock?filter%5Bstock_status%5D=good" class="link-nocturne-purple"><?= $translator->translate('Estoque bom') ?></a>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="dot bg-warning me-2 rounded-5" style="height:10px; width:10px;"></span> <a href="/stock?filter%5Bstock_status%5D=low" class="link-nocturne-purple"><?= $translator->translate('Estoque baixo') ?></a>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="dot bg-danger me-2 rounded-5" style="height:10px; width:10px;"></span> <a href="/stock?filter%5Bstock_status%5D=out" class="link-nocturne-purple"><?= $translator->translate('Sem estoque') ?></a>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="dot bg-secondary me-2 rounded-5" style="height:10px; width:10px;"></span> <a href="/stock?filter%5Bstock_status%5D=dead" class="link-nocturne-purple"><?= $translator->translate('Estoque morto') ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xl-6 col-12 text-center">
                            <canvas id="myChart" class="w-100 h-100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </section>
    <section class="section-filter">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <form id="search-form" class="d-flex align-items-center mb-4">
                        <div class="input-group me-3">
                            <input type="search" class="form-control input-stellar-blue" placeholder="<?= $translator->translate('Pesquisar produtos...') ?>" name="search" value="<?= $search ?>">
                            <button class="btn btn-outline-stellar-blue" type="submit">
                                <i class="fas fa-search"></i> 
                            </button>
                        </div>
                        <a class="btn btn-outline-stellar-blue btn-md" id="filter-button" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <?= $translator->translate('Filtros') ?>
                        </a>
                        <?php
                            if (!empty($filter['status'])) echo '<input type="hidden" name="filter[status]" value="' . htmlspecialchars($filter['status']) . '">';
                            if (!empty($filter['category'])) 
                                foreach ($filter['category'] as $cat)
                                    echo '<input type="hidden" name="filter[category][]" value="' . htmlspecialchars($cat) . '">';
                            if (!empty($filter['price'])) echo '<input type="hidden" name="filter[price]" value="' . htmlspecialchars($filter['price']) . '">';
                            if (!empty($filter['cost'])) echo '<input type="hidden" name="filter[cost]" value="' . htmlspecialchars($filter['cost']) . '">';
                            if (!empty($filter['discount'])) echo '<input type="hidden" name="filter[discount]" value="' . htmlspecialchars($filter['discount']) . '">';
                            if (!empty($filter['profit'])) echo '<input type="hidden" name="filter[profit]" value="' . htmlspecialchars($filter['profit']) . '">';
                            if (!empty($filter['stock'])) echo '<input type="hidden" name="filter[stock]" value="' . htmlspecialchars($filter['stock']) . '">';
                            if (!empty($filter['min_stock'])) echo '<input type="hidden" name="filter[min_stock]" value="' . htmlspecialchars($filter['min_stock']) . '">';
                            if (!empty($filter['stock_status'])) echo '<input type="hidden" name="filter[stock_status]" value="' . htmlspecialchars($filter['stock_status']) . '">';
                        ?>
                    </form>
                </div>
            </div>
            <div class="row" id="filters">
                <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <?php
                            if (!empty($filter['status'])) echo '<span class="badge bg-light text-dark me-1">' . $translator->translate('Status') . ': ' . $translator->translate($filter['status'] == 'active' ? 'Ativo' : 'Inativo') . '</span>';
                            if (!empty($filter['category'])) {
                                $selectedCategoriesLabels = [];
                                foreach ($categories as $category)
                                    if (in_array($category['id'], $filter['category']))
                                        $selectedCategoriesLabels[] = htmlspecialchars($category['nome']);
                                echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Categoria(s)').': ' . implode(', ', $selectedCategoriesLabels) . '</span>';
                            }
                            if (!empty($filter['price'])) echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Preço').': R$ ' . htmlspecialchars($filter['price']) . '</span>';
                            if (!empty($filter['cost'])) echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Custo').': R$ ' . htmlspecialchars($filter['cost']) . '</span>';
                            if (!empty($filter['discount'])) echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Desconto').': R$ ' . htmlspecialchars($filter['discount']) . '</span>';
                            if (!empty($filter['real_price'])) echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Preço Atual').': R$ ' . htmlspecialchars($filter['real_price']) . '</span>';
                            if (!empty($filter['stock'])) echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Estoque').': ' . htmlspecialchars($filter['stock']) . '</span>';
                            if (!empty($filter['min_stock'])) echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Estoque Mínimo').': ' . htmlspecialchars($filter['min_stock']) . '</span>';
                            if (!empty($filter['stock_status'])) {
                                echo '<span class="badge bg-light text-dark me-1">'.$translator->translate('Status de Estoque').': ';
                                switch ($filter['stock_status']) {
                                    case 'good':
                                        echo $translator->translate('Bom');
                                        break;
                                    case 'low':
                                        echo $translator->translate('Baixo');
                                        break;
                                    case 'out':
                                        echo $translator->translate('Esgotado');
                                        break;
                                    case 'dead':
                                        echo $translator->translate('Morto');
                                        break;

                                }
                                echo '</span>';
                            }
                        ?>
                    </div>
                    <div class="d-flex align-items-center">
                        <form id="id-sort-form" class="d-flex align-items-center me-3" method="GET" action="<?= url("stock") ?>">
                            <?php
                            if (!empty($search)) echo '<input type="hidden" name="search" value="' . htmlspecialchars($search) . '">';
                            if (!empty($filter['status'])) echo '<input type="hidden" name="filter[status]" value="' . htmlspecialchars($filter['status']) . '">';
                            if (!empty($filter['category']))
                                foreach ($filter['category'] as $cat)
                                    echo '<input type="hidden" name="filter[category][]" value="' . htmlspecialchars($cat) . '">';
                            if (!empty($filter['price'])) echo '<input type="hidden" name="filter[price]" value="' . htmlspecialchars($filter['price']) . '">';
                            if (!empty($filter['cost'])) echo '<input type="hidden" name="filter[cost]" value="' . htmlspecialchars($filter['cost']) . '">';
                            if (!empty($filter['discount'])) echo '<input type="hidden" name="filter[discount]" value="' . htmlspecialchars($filter['discount']) . '">';
                            if (!empty($filter['real_price'])) echo '<input type="hidden" name="filter[real_price]" value="' . htmlspecialchars($filter['real_price']) . '">';
                            if (!empty($filter['stock'])) echo '<input type="hidden" name="filter[stock]" value="' . htmlspecialchars($filter['stock']) . '">';
                            if (!empty($filter['min_stock'])) echo '<input type="hidden" name="filter[min_stock]" value="' . htmlspecialchars($filter['min_stock']) . '">';
                            if (!empty($filter['stock_status'])) echo '<input type="hidden" name="filter[stock_status]" value="' . htmlspecialchars($filter['stock_status']) . '">';
                            ?>
                            <select class="form-select form-select-sm input-stellar-blue me-2" id="sort-options" name="sort" onchange="this.form.submit()">
                                <?php foreach ($orderList as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $value['selected'] ? 'selected' : '' ?>><?= $value['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                        <div class="me-3">
                            <button type="button" class="btn btn-cotton-candy dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-screwdriver-wrench bi" style="width:24px; text-align: center;"></i> <?= $translator->translate('Ações') ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="fa fa-file-import me-1" aria-hidden="true"></i> <?= $translator->translate('Importar Produtos') ?>                   
                                    </a>
                                </li>
                                <!-- <li>
                                    <a class="dropdown-item" href="#" id="export-products" data-bs-toggle="modal">
                                        <i class="fa fa-file-export me-1" aria-hidden="true"></i> <?= $translator->translate('Exportar Produtos') ?>                      
                                    </a>
                                </li> -->
                            </ul>
                        </div>
                        <a class="btn btn-stellar-blue btn-md" id="newProduct" data-bs-toggle="modal" data-bs-target="#newModal">
                            <i class="fa-solid fa-plus bi" style="width:24px; text-align: center;"></i> <?= $translator->translate('Novo Produto') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-products">
        <div class="container">
            <div class="row align-items-stretch" id="produtos">
                <?php foreach($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 evento">
                    <a class="card h-100 d-flex flex-column product-card" href="<?=url('stock/product/'.$product['id'])?>">
                        <div class="card-img-top position-relative pt-2 px-2">
                            <img src="<?= empty($product['thumbnail']) ? url('assets/image/200x300.png') : storageURL($product['thumbnail']) ?>" class="img-fluid rounded thumbnail-product" alt="Evento">
                            <?php if ($product['estoque'] <= 0): ?>
                            <span class="badge bg-danger position-absolute top-0 end-0 m-3"><?= $translator->translate('Sem Estoque') ?></span>
                            <?php elseif ($product['estoque'] < $product['estoque_minimo']): ?>
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3"><?= $product['estoque'] ?> <?= $translator->translate('uni') ?></span>
                            <?php else: ?>
                            <span class="badge bg-white text-dark position-absolute top-0 end-0 m-3"><?= $product['estoque'] ?> <?= $translator->translate('uni') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                <span class="color-stellar-blue nome-produto"><?= $product['nome'] ?></span>
                                <?= $product['ativo'] ? '<span class="badge bg-lavanda">'.$translator->translate('Ativo').'</span>' : '<span class="badge bg-graphite-gray">'.$translator->translate('Inativo').'</span>' ?>
                            </h5> 
                            <p class="card-text mt-auto">
                                <?php
                                    $productKeywords = explode('|', $product['palavras_chave']);
                                    foreach ($productKeywords as $keyword) {
                                        echo '<span class="badge bg-light text-dark me-1">' . htmlspecialchars($keyword) . '</span>';
                                    }
                                ?>
                            </p>
                            <div class="card-text">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span><?= $translator->translate('Preço Base:') ?></span><br>
                                        <?php if ($product['valor_desconto'] > 0): ?>
                                            <span class="badge bg-light text-dark me-1 text-decoration-line-through">R$ <?= number_format($product['valor'], 2, ',', '.') ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark me-1">R$ <?= number_format($product['valor'], 2, ',', '.') ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span ><?= $translator->translate('Preço Atual:') ?></span> <br>
                                        <div class="text-end">
                                            <span class="badge bg-light text-dark me-1">R$ <?= number_format($product['valor']-$product['valor_desconto'], 2, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-pagination">
        <div class="container">
            <div class="row justify-content-between align-items-center" id="pagination-controls">
                <div class="col-md-6 col-12 d-flex justify-content-md-start justify-content-center mb-2 mb-md-0">
                    <p class="text-muted"><?= $translator->translate('Mostrando') ?> <span id="result-count"><?= count($products)?></span> <?= $translator->translate('resultados de') ?> <span id="total-count"><?= $stocks['totalProducts'] ?></span></p>
                </div>
                <div class="col-md-6 col-12 d-flex justify-content-md-end justify-content-center">
                    <?php if (!empty($products)): ?>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php
                                $url = url("stock").'?';
                                $url .= 'sort=' . urlencode($sort);
                                if (!empty($search)) $url .= '&search=' . urlencode($search);
                                if (!empty($filter['status'])) $url .= '&filter[status]=' . urlencode($filter['status']);
                                if (!empty($filter['category']))
                                    foreach ($filter['category'] as $cat)
                                        $url .= '&filter[category][]=' . urlencode($cat);
                                if (!empty($filter['price'])) $url .= '&filter[price]=' . urlencode($filter['price']);
                                if (!empty($filter['cost'])) $url .= '&filter[cost]=' . urlencode($filter['cost']);
                                if (!empty($filter['discount'])) $url .= '&filter[discount]=' . urlencode($filter['discount']);
                                if (!empty($filter['real_price'])) $url .= '&filter[real_price]=' . urlencode($filter['real_price']);
                                if (!empty($filter['stock'])) $url .= '&filter[stock]=' . urlencode($filter['stock']);
                                if (!empty($filter['min_stock'])) $url .= '&filter[min_stock]=' . urlencode($filter['min_stock']);  
                                if (!empty($filter['stock_status'])) $url .= '&filter[stock_status]=' . urlencode($filter['stock_status']);  
                                                    
                            ?>
                            <?php if ($pages['current'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link stellar-blue" href="<?= $url ?>&pagination[offset]=<?= ($pages['current'] - 2) * $pagination['limit'] ?>"><?= $translator->translate('Anterior') ?></a>
                            </li>
                            <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link stellar-blue"><?= $translator->translate('Anterior') ?></span>
                            </li>
                            <?php endif; ?>
                            <!-- Max of 4 pages before and 4 after -->
                            <?php
                            $start = max(1, $pages['current'] - 4);
                            $end = min($pages['total'], $pages['current'] + 4);
                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i == $pages['current'] ? 'active' : 'd-none d-md-flex' ?>" >
                                    <a class="page-link stellar-blue" href="<?= $i == $pages['current'] ? '#' : $url.'&pagination[offset]='.(($i - 1) * $pagination['limit']) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($pages['current'] < $pages['total']): ?>
                            <li class="page-item">
                                <a class="page-link stellar-blue" href="<?= $url ?>&pagination[offset]=<?= $pages['current'] * $pagination['limit'] ?>"><?= $translator->translate('Próxima') ?></a>
                            </li>
                            <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link stellar-blue"><?= $translator->translate('Próxima') ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</section>
<section class="modal-form">
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel"><?= $translator->translate('Opções de Filtros') ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filter-form" method="GET" action="<?= url('stock') ?>">
                        <div class="mb-3">
                            <label for="filter-status" class="form-label"><?= $translator->translate('Status') ?></label>
                            <select class="form-select" id="filter-status" name="filter[status]">
                                <option value=""><?= $translator->translate('Selecione') ?></option>
                                <option value="active" <?= $filter['status'] == 'active' ? 'selected' : '' ?>><?= $translator->translate('Ativo') ?></option>
                                <option value="inactive" <?= $filter['status'] == 'inactive' ? 'selected' : '' ?>><?= $translator->translate('Inativo') ?></option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="filter-category" class="form-label"><?= $translator->translate('Categoria(s)') ?></label>
                            <select class="form-select" id="filter-category" name="filter[category][]" multiple>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= in_array($category['id'], $filter['category']) ? 'selected' : '' ?>><?= $category['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="filter-price" class="form-label"><?= $translator->translate('Preço') ?></label>
                                <input type="text" class="form-control moedaReal" id="filter-price" name="filter[price]" value="<?= $filter['price'] ?? '0,00' ?>">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="filter-cost" class="form-label"><?= $translator->translate('Custo') ?></label>
                                <input type="text" class="form-control moedaReal" id="filter-cost" name="filter[cost]" value="<?= $filter['cost'] ?? '0,00' ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="filter-discount" class="form-label"><?= $translator->translate('Desconto') ?></label>
                                <input type="text" class="form-control moedaReal" id="filter-discount" name="filter[discount]" value="<?= $filter['discount'] ?? '0,00' ?>">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="filter-real_price" class="form-label"><?= $translator->translate('Preço Atual') ?></label>
                                <input type="text" class="form-control moedaReal" id="filter-real_price" name="filter[real_price]" value="<?= $filter['real_price'] ?? '0,00' ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="filter-stock" class="form-label"><?= $translator->translate('Estoque') ?></label>
                                <input type="number" class="form-control" id="filter-stock" name="filter[stock]" value="<?= $filter['stock'] ?? '0' ?>" min="0">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="filter-min-stock" class="form-label"><?= $translator->translate('Estoque Mín.') ?></label>
                                <input type="number" class="form-control" id="filter-min-stock" name="filter[min_stock]" value="<?= $filter['min_stock'] ?? '0' ?>" min="0">
                            </div>
                        </div>
                        <input type="hidden" name="search" value="<?= $get['search'] ?? '' ?>">
                        <?php
                            if (!empty($filter['stock_status'])) echo '<input type="hidden" name="filter[stock_status]" value="' . htmlspecialchars($filter['stock_status']) . '">';
                        ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate('Fechar') ?></button>
                    <a href="<?= url('stock') ?>" class="btn btn-cotton-candy" id="clear-filters"><?= $translator->translate('Limpar Filtros') ?></a>
                    <button type="submit" class="btn btn-stellar-blue" form="filter-form"><?= $translator->translate('Aplicar Filtros') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel"><?= $translator->translate('Novo Produto') ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-new-product" method="post" action="<?= url("stock") ?>">

                        
                        <div class="form-check form-switch form-switch-sm">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="active" checked>
                            <label class="form-check-label" for="flexSwitchCheckDefault" value="1"><?= $translator->translate('Produto Ativo') ?></label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><?= $translator->translate('Imagem do Produto') ?></label>
                            <div id="image-drop-area" class="image-drop-area d-flex align-items-center justify-content-center">
                                <span id="image-drop-text"><?= $translator->translate('Clique ou arraste uma imagem aqui') ?></span>
                            </div>
                        </div>
                        <small id="new-nameHelp" class="form-text text-muted">
                            <?= $translator->translate('Tamanho máximo') ?>: 5MB
                        </small>
                        <input type="file" id="new-image" name="thumbnail" accept="image/*" style="display:none;">

                        
                        <div class="mb-3">
                            <label for="filter-name" class="form-label">*<?= $translator->translate('Nome') ?></label>
                            <input type="text" class="form-control" id="new-name" name="name" placeholder="<?= $translator->translate('Digite o nome do produto') ?>">
                            <small id="new-nameHelp" class="form-text text-muted d-flex justify-content-between">
                                <span><?= $translator->translate('Nome oficial do produto') ?></span>
                                <span><span id="new-nameCount">0</span>/50</span>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="filter-insideId" class="form-label"><?= $translator->translate('Identificação Interna') ?></label>
                            <input type="text" class="form-control" id="new-insideId" name="insideId" placeholder="<?= $translator->translate('Digite a identificação interna') ?>">
                            <small id="new-insideIdHelp" class="form-text text-muted d-flex justify-content-between">
                                <span><?= $translator->translate('Um nome não oficial do produto') ?></span>
                                <span><span id="new-insideIdCount">0</span>/50</span>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="new-category" class="form-label"><?= $translator->translate('Categorias') ?></label>
                            <select class="form-select select2" id="new-category" name="category[]" multiple="multiple">
                                <?php foreach ($categories as $category): ?>
                                    <option value="{existing}<?= $category['id'] ?>"><?= $category['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="new-keywords" class="form-label">
                                <?= $translator->translate('Palavras-Chave') ?> <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate('Palavras-Chave ajudam na busca de produtos, sem serem parte do nome oficial.') ?>"></i>
                            </label>
                            <select class="form-select select2" id="new-keywords" name="keywords[]" multiple="multiple">
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="filter-name" class="form-label"><?= $translator->translate('Descrição') ?></label>
                            <textarea class="form-control" id="filter-description" name="description" rows="3" placeholder="<?= $translator->translate('Digite a descrição do produto') ?>"></textarea>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="new-price" class="form-label"><?= $translator->translate('Preço') ?></label>
                                <input type="text" class="form-control moedaReal" id="new-price" name="price" value="0,00">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="new-cost" class="form-label"><?= $translator->translate('Custo') ?></label>
                                <input type="text" class="form-control moedaReal" id="new-cost" name="cost" value="0,00">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="new-discount" class="form-label"><?= $translator->translate('Desconto') ?></label>
                                <input type="text" class="form-control moedaReal" id="new-discount" name="discount" value="0,00">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="new-profit" class="form-label">
                                    <?= $translator->translate('Margem') ?> <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate('A margem de contribuição é calculada subtraindo o custo e o desconto do preço de venda, ajuda a identificar a lucratividade do produto.') ?>"></i>
                                </label>
                                <input type="text" disabled class="form-control" id="new-profit" name="profit" value="0,00">
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="new-stock" class="form-label"><?= $translator->translate('Estoque') ?></label>
                                <input type="number" class="form-control" id="new-stock" name="stock" value="0" min="0">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="new-min-stock" class="form-label">
                                    <?= $translator->translate('Estoque Mín.') ?> <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate('Estoque mínimo é a quantidade mínima que você deseja manter em estoque para evitar falta de produtos. Quando o produto está na vitrine da loja, ele será exibido com um aviso de estoque baixo.') ?>"></i>
                                </label>
                                <input type="number" class="form-control" id="new-min-stock" name="min_stock" value="0" min="0">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate('Fechar') ?></button>
                    <button type="submit" class="btn btn-stellar-blue" id="create-product-btn" form="new-product-form"><?= $translator->translate('Inserir') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel"><?= $translator->translate('Importar Produtos') ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="import-modal-body">
                    <form id="import-form" method="POST" action="<?= url('stock') ?>">
                        <div class="mb-3">
                            <p><?= $translator->translate('Ao importar produtos, aqueles com nomes iguais serão atualizados, enquanto produtos com nomes diferentes serão criados.') ?></p>
                            <p><?= $translator->translate('Categorias com nomes iguais serão vinculadas aos produtos, e categorias não encontradas serão criadas automaticamente.') ?></p>
                            <p><?= $translator->translate('Você pode baixar o modelo de importação clicando no botão abaixo.') ?></p>
                            <p><?= $translator->translate('Não troque o nome das colunas do arquivo.') ?></p>
                        </div>
                        <div class="mb-3">
                            <a href="<?= url('assets/template/import_products_template.csv') ?>" class="btn btn-stellar-blue" download>
                                <i class="fa-solid fa-file-excel me-1"></i> <?= $translator->translate('Baixar Modelo de Importação') ?>
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="importFile" class="form-label"><?= $translator->translate('Envie um arquivo .csv') ?></label>
                            <input class="form-control" type="file" id="importFile" name="importFile" accept=".csv">
                            <small id="importFileHelp" class="form-text text-muted">
                                <?= $translator->translate('Tamanho máximo') ?>: 2MB
                            </small>
                            <br>
                            <small id="importFileError" class="form-text text-danger d-none">
                                <?= $translator->translate('Erro ao importar o arquivo. Por favor, verifique se o arquivo está no formato correto e tente novamente.') ?>
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate('Fechar') ?></button>
                    <button type="submit" class="btn btn-stellar-blue" id="import-form-btn" form="import-form"><i class="fa-solid fa-file-import me-1"></i> <?= $translator->translate('Importar') ?></button>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/vendors/chart.js/Chart.min.js") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
    const goodStock = <?= $stocks['goodStock']?>;
    const lowStock = <?= $stocks['lowStock']?>;
    const outOfStock = <?= $stocks['outOfStock']?>;
    const deadStock = <?= $stocks['deadStock']?>;
    const dictionary = {
        chart: {
            labels: [
                '<?= $translator->translate('Estoque bom') ?>',
                '<?= $translator->translate('Estoque baixo') ?>',
                '<?= $translator->translate('Sem estoque') ?>',
                '<?= $translator->translate('Estoque morto') ?>'
            ],
            datasetLabel: '<?= $translator->translate('Produtos') ?>'
        },
        select2: {
            new: {
                category: {
                    placeholder: '<?= $translator->translate("Selecione ou adicione uma nova categoria") ?>',
                    language: '<?= $translator->translate("Adicione uma nova categoria") ?>'
                },
                keywords: {
                    placeholder: '<?= $translator->translate("Selecione ou adicione uma nova palavra-chave") ?>',
                    language: '<?= $translator->translate("Adicione uma nova palavra-chave") ?>'
                }
            },
            filter: {
                category: {
                    placeholder: '<?= $translator->translate("Selecione uma ou mais categorias") ?>',
                    language: '<?= $translator->translate("Nenhuma categoria encontrada") ?>'
                },
            }
        },
        imageSizeError: '<?= $translator->translate("A imagem deve ter no máximo 5MB") ?>',
        validation: {
            nameRequired: '<?= $translator->translate("Por favor, preencha o campo de nome") ?>',
            error: '<?= $translator->translate("Erro ao criar produto:") ?>'
        }
    };
</script>
<script src="<?= url("assets/js/stock/home.js?ts=" . time()) ?>"></script>

<?= $this->stop() ?>