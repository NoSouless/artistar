<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/sales-statement/home.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <div class="col-sm-6 col-12 mb-2 mt-2 ">
            <div>
                <h1 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Extrato de Vendas") ?></h1>
            </div>
        </div>
    </div>
    <div class="row" id="sales-banner">
        <div class="col-12 mb-3 px-4">
            <div class="row p-md-5 p-3 bg-lavanda text-white rounded">
                <div class="col-12">
                    <h1><?= $translator->translate("Você já vendeu") ?> <?= $currency ?> <?= moedaReal($totalSales['total_value'])?> <?= $translator->translate("em") ?> <?= $totalSales['total_sales']-$totalSales['total_canceled'] ?> <?= $translator->translate("Vendas!") ?></h1> 
                </div>
                <div class="col-md-6 col-12">
                    <h5><?= $translator->translate("Valor Médio das Vendas") ?>: <?= $currency ?> <?= moedaReal($totalSales['total_value'] / ($totalSales['total_sales']-$totalSales['total_canceled']  ?: 1)) ?></h5>
                    <h5><?= $translator->translate("Unidades Vendidas") ?>: <?= $totalSales['total_items'] ?></h5>
                </div>
                <div class="col-md-6 col-12">
                    <h5><?= $translator->translate("Produtos Diferentes Vendidos") ?>: <?= $totalSales['total_products'] ?></h5>
                    <h5><?= $translator->translate("Vendas Canceladas") ?>: <?= $totalSales['total_canceled'] ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="filters">
        <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <a class="btn btn-stellar-blue btn-sm" id="export-sales" href="<?= url('sales-statement/export') . '?' . http_build_query(['event' => $_GET['event'] ?? 'all', 'sort' => $sort]) ?>">
                    <i class="fa fa-file-export me-1" aria-hidden="true"></i> <?= $translator->translate("Exportar Extrato") ?>
                </a>
            </div>
            <div class="d-flex align-items-end">
                <form id="id-sort-form" class="d-md-flex d-block align-items-center" method="GET" action="/sales-statement">
                    <select class="form-select form-select-sm input-stellar-blue me-2 mb-md-0 mb-2" id="events" name="event" onchange="this.form.submit()">
                        <option value="all">-- <?= $translator->translate("Todos") ?> --</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?= $event['evento_id'] ?>" <?= ($_GET['event'] ?? '') == $event['evento_id'] ? 'selected' : '' ?>><?= $event['evento_nome'] ?></option>
                        <?php endforeach; ?>
                        <option value="none" <?= ($_GET['event'] ?? '') == 'none' ? 'selected' : '' ?>>-- <?= $translator->translate("Vendas Avulsas") ?> --</option>
                    </select>
                    <select class="form-select form-select-sm input-stellar-blue" id="sort-options" name="sort" onchange="this.form.submit()">
                        <?php foreach ($orderList as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $value['selected'] ? 'selected' : '' ?>><?= $value['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 table-responsive-lg">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">
                            <span class="color-nocturne-purple"><?= $translator->translate("Venda") ?></span>
                        </th>
                        <th scope="col" class="text-center">
                            <span class="color-nocturne-purple"><?= $translator->translate("Data (Venda)") ?><br><?= $translator->translate("Data (Criação)") ?></span>
                        </th>
                        <th scope="col" class="text-center">
                            <span class="color-nocturne-purple"><?= $translator->translate("Status do<br>Pagamento") ?></span>
                        </th>
                        <th scope="col" class="text-center">
                            <span class="color-nocturne-purple"><?= $translator->translate("Status da<br>Entrega") ?></span>
                        </th>
                        <th scope="col" class="text-center">
                            <span class="color-nocturne-purple"><?= $translator->translate("Método de Pagamento") ?><br><?= $translator->translate("Evento") ?></span>
                        </th>
                        <th scope="col" class="text-center">
                            <span class="color-nocturne-purple"><?= $translator->translate("Produtos") ?></span><br>
                            <span class="color-nocturne-purple"><?= $translator->translate("Unidades") ?></span>
                        </th>
                        <th scope="col" class="text-end">
                            <span class="color-nocturne-purple"><?= $translator->translate("Valor") ?></span><br>
                            <span class="color-nocturne-purple"><?= $translator->translate("Desconto") ?></span>
                        </th>
                        <th scope="col">&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="accordion accordion-flush">
                    <?php foreach ($sales as $sale): ?>
                    <tr class="link-nocturne-purple">
                        <td class="text-center">
                            <i class="accordion-button collapsed bg-none shadow-none accordion-header"
                                id="heading-<?= $sale['id'] ?>" 
                                class="accordion-button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse-<?= $sale['id'] ?>"
                                aria-expanded="true" 
                                aria-controls="collapse-<?= $sale['id'] ?>">
                            </i>
                        </td>
                        <th class="align-middle" scope="row" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <span class="color-stellar-blue">#<?= $sale['numero'] ?></span><br>
                            <small class="text-danger"><?php if ($sale['cancelada'] == 1) echo $translator->translate('Cancelada'); ?></small>
                        </th>
                        <td class="text-center align-middle" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <?= $sale['data_venda'] ?><br>
                            <small class="color-gray"><?= $sale['data_criacao'] ?></small>
                        </td>
                        <td class="text-center align-middle" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <?= $sale['pago'] ? '<span class="badge text-bg-success rounded-pill p-2">' . $translator->translate('Pago') . '</span>' : '<span class="badge bg-sunshine-yellow color-graphite-gray rounded-pill p-2">' . $translator->translate('Pendente') . '</span>' ?>
                        </td>
                        <td class="text-center align-middle" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <?= $sale['entregue'] ? '<span class="badge text-bg-success rounded-pill p-2">' . $translator->translate('Entregue') . '</span>' : '<span class="badge bg-sunshine-yellow color-graphite-gray rounded-pill p-2">' . $translator->translate('Pendente') . '</span>' ?>
                        </td>
                        <td class="text-center align-middle" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <?= $translator->translate($sale['pagamento']) ?><br>
                            <small class="color-gray"><?= $sale['evento_nome'] ?? $translator->translate('(Venda Avulsa)') ?></small>
                        </td>
                        <td class="text-center align-middle" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <?= $sale['total_itens'] ?><br>
                            <small class="color-gray"><?= $sale['total_unidades'] ?></small>
                        </td>
                        <td class="text-end align-middle" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $sale['id'] ?>">
                            <span class="color-nocturne-purple"><?= $currency ?> <?= moedaReal($sale['total_valor']) ?></span><br>
                            <small class="color-gray"><?= $currency ?> <?= moedaReal($sale['total_desconto']) ?></small>
                        </td>
                        <td class="text-center align-right">
                            <div class="d-flex justify-content-sm-end justify-content-between">
                                <a class="btn btn-stellar-blue text-white text-decoration-none" href="<?= url("sales-statement/sale/{$sale['id']}") ?>">
                                    <i class="fa fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr-accordion">
                        <td colspan="9" class="p-0 px-2">
                            <div            
                                id="collapse-<?= $sale['id'] ?>" 
                                class="accordion-collapse collapse" 
                                aria-labelledby="heading-<?= $sale['id'] ?>" 
                                data-bs-parent="#accordionExample"
                            >
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th width="10" scope="col">&nbsp;</th>
                                            <th scope="col">
                                                <span class="color-gray"><?= $translator->translate('Produto') ?></span>
                                            </th>
                                            <th scope="col" class="text-center">
                                                <span class="color-gray"><?= $translator->translate('Qtd.') ?></span>
                                            </th>
                                            <th scope="col" class="text-end">
                                                <span class="color-gray"><?= $translator->translate('Desconto') ?></span>
                                            </th>
                                            <th scope="col" class="text-end">
                                                <span class="color-gray"><?= $translator->translate('Valor') ?></span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items[$sale['id']] as $item): ?>
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <img src="<?= empty($item['thumbnail']) ? 'assets/image/200x300.png' : $item['thumbnail'] ?>" alt="<?= $item['nome'] ?>" class="img-fluid" style="max-width: 100px; max-height:100px;">
                                                </td>
                                                <td class="align-middle">
                                                    <span class="color-nocturne-purple"><?= $item['nome'] ?></span><br>
                                                    <small class="color-gray"><?= $item['codigo_interno'] ?></small>
                                                </td>
                                                <td class="text-center align-middle"><?= $item['qtd'] ?></td>
                                                <td class="text-end align-middle">
                                                    <span class="color-nocturne-purple"><?= $currency ?> <?= $item['desconto'] ?></span><br>
                                                    <small class="color-gray"><?= $currency ?> <?= $item['desconto_unitario'] ?>/<?= $translator->translate('uni') ?></small>
                                                </td>
                                                <td class="text-end align-middle">
                                                    <span class="color-nocturne-purple"><?= $currency ?> <?= $item['valor'] ?></span><br>
                                                    <small class="color-gray"><?= $currency ?> <?= $item['valor_unitario'] ?>/<?= $translator->translate('uni') ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <section class="section-pagination">
        <div class="container">
            <div class="row justify-content-between align-items-center" id="pagination-controls">
                <div class="col-md-6 col-12 d-flex justify-content-md-start justify-content-center mb-2 mb-md-0 px-0">
                    <p class="text-muted"><?= $translator->translate('Mostrando') ?> <span id="result-count"><?= count($sales)?></span> <?= $translator->translate('resultados de') ?> <span id="total-count"><?= $totalSales['total_sales'] ?></span></p>
                </div>
                <div class="col-md-6 col-12 d-flex justify-content-md-end justify-content-center px-0">
                    <?php if (!empty($sales)): ?>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php
                                $url = url("sales-statement").'?';
                                $url .= 'sort=' . urlencode($sort);
                                                    
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
         
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/js/sales-statement/home.js") ?>"></script>
<?= $this->stop() ?>