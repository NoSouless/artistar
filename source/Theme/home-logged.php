<?= $this->layout("base") ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<link rel="stylesheet" href="<?= url("assets/css/events/myEvents.css") ?>">
<link rel="stylesheet" href="<?= url("assets/css/statistics/home.css") ?>">
<style> 
    body {
        background-color: var(--polar-gray);
    }
</style>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<?php
    $totalSalesCount = (int) $totalSales['total_sales'] - (int) $totalSales['total_canceled'];
    $quickLinks = [
        ['url' => 'sales', 'icon' => 'fa-cash-register', 'color' => 'color-nocturne-purple', 'label' => $translator->translate('Nova Venda')],
        ['url' => 'stock', 'icon' => 'fa-box', 'color' => 'color-lavanda', 'label' => $translator->translate('Inventário')],
        ['url' => 'sales-statement', 'icon' => 'fa-receipt', 'color' => 'color-cotton-candy', 'label' => $translator->translate('Extrato de Vendas')],
        ['url' => 'statistics', 'icon' => 'fa-chart-line', 'color' => 'color-stellar-blue', 'label' => $translator->translate('Estatísticas')],
        ['url' => 'events', 'icon' => 'fa-magnifying-glass', 'color' => 'color-nocturne-purple', 'label' => $translator->translate('Procurar Eventos')],
        ['url' => 'events/my-events', 'icon' => 'fa-calendar-days', 'color' => 'color-stellar-blue', 'label' => $translator->translate('Meus Eventos')],
        ['url' => 'settings/profile', 'icon' => 'fa-gear', 'color' => 'color-lavanda', 'label' => $translator->translate('Configurações')],
    ];
    if (!empty($_SESSION['artistar']['user']['loja_nome_unico'])) {
        $quickLinks[] = ['url' => $_SESSION['artistar']['user']['loja_nome_unico'], 'icon' => 'fa-store', 'color' => 'color-cotton-candy', 'label' => $translator->translate('Minha Loja')];
    }
    if ($isAdmin) {
        $quickLinks[] = ['url' => 'admin', 'icon' => 'fa-user-tie', 'color' => 'color-nocturne-purple', 'label' => $translator->translate('Painel de Administrador')];
    }
?>
<div class="container pt-3 minimum-height avoid-navbar">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="color-nocturne-purple mb-3"><i class="fa-solid fa-bolt me-2"></i><?= $translator->translate('Acesso rápido') ?></h4>
                    <div class="row g-4">
                        <?php foreach ($quickLinks as $link): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="<?= url($link['url']) ?>" class="text-decoration-none">
                                    <div class="card text-center shadow-sm h-100">
                                        <div class="card-body">
                                            <i class="fas <?= $link['icon'] ?> fa-2x mb-3 <?= $link['color'] ?>"></i>
                                            <h6 class="card-title"><?= $link['label'] ?></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <a class="h4 link-nocturne-purple" href="<?= url('stock') ?>"><?= $translator->translate('Inventário') ?></a>
                    <div class="row align-items-center mt-3">
                        <div class="col-md-4 col-12">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bg-success me-2 rounded-5" style="height:10px; width:10px;"></span>
                                    <a href="<?= url('stock') ?>?filter%5Bstock_status%5D=good" class="link-nocturne-purple"><?= $translator->translate('Estoque bom') ?></a>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bg-warning me-2 rounded-5" style="height:10px; width:10px;"></span>
                                    <a href="<?= url('stock') ?>?filter%5Bstock_status%5D=low" class="link-nocturne-purple"><?= $translator->translate('Estoque baixo') ?></a>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bg-danger me-2 rounded-5" style="height:10px; width:10px;"></span>
                                    <a href="<?= url('stock') ?>?filter%5Bstock_status%5D=out" class="link-nocturne-purple"><?= $translator->translate('Sem estoque') ?></a>
                                </li>
                                <li class="d-flex align-items-center">
                                    <span class="bg-secondary me-2 rounded-5" style="height:10px; width:10px;"></span>
                                    <a href="<?= url('stock') ?>?filter%5Bstock_status%5D=dead" class="link-nocturne-purple"><?= $translator->translate('Estoque morto') ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-8 col-12">
                            <canvas id="stockChart" class="w-100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h4 class="color-nocturne-purple"><i class="fa-solid fa-bell me-2"></i><?= $translator->translate('Notificações') ?></h4>
                    <?php if (empty($notifications)): ?>
                        <p class="text-muted mt-3 mb-0"><?= $translator->translate('Nenhuma notificação no momento.') ?></p>
                    <?php else: ?>
                        <div class="list-group list-group-flush mt-2" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($notifications as $notification): ?>
                                <a href="<?= $notification['link'] ?>" class="list-group-item list-group-item-action d-flex align-items-start px-0">
                                    <i class="fa-solid <?= $notification['icon'] ?> <?= $notification['color'] ?> me-3 mt-1" style="width:20px; text-align:center;"></i>
                                    <span><?= htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8') ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="row p-md-5 p-3 mx-0 bg-lavanda text-white rounded">
                <div class="col-12">
                    <h1><?= $translator->translate("Você já vendeu") ?> <?= $currency ?> <?= moedaReal((float) $totalSales['total_value']) ?> <?= $translator->translate("em") ?> <?= $totalSalesCount ?> <?= $translator->translate("Vendas!") ?></h1>
                </div>
                <div class="col-md-6 col-12">
                    <h5><?= $translator->translate("Valor Médio das Vendas") ?>: <?= $currency ?> <?= moedaReal((float) $totalSales['total_value'] / ($totalSalesCount ?: 1)) ?></h5>
                    <h5><?= $translator->translate("Unidades Vendidas") ?>: <?= (int) $totalSales['total_items'] ?></h5>
                </div>
                <div class="col-md-6 col-12">
                    <h5><?= $translator->translate("Produtos Diferentes Vendidos") ?>: <?= (int) $totalSales['total_products'] ?></h5>
                    <h5><?= $translator->translate("Vendas Canceladas") ?>: <?= (int) $totalSales['total_canceled'] ?></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a class="h4 link-nocturne-purple" href="<?= url('statistics') ?>"><?= $translator->translate('Estatísticas') ?> <small class="text-muted fs-6">(<?= $translator->translate('Último mês') ?>)</small></a>
                        <div class="toggle-container shadow-sm" id="toggle">
                            <div class="slider left" id="slider"></div>
                            <div class="toggle-btn active text-dark" data-side="left" id="graphByValue"><?= $translator->translate("Valor") ?></div>
                            <div class="toggle-btn text-dark" data-side="right" id="graphByQuantity"><?= $translator->translate("Vendas") ?></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <h3 class="color-nocturne-purple mb-0"><?= $currency ?> <?= moedaReal((float) $graphData['totals']['vendas']) ?></h3>
                            <span class="color-stellar-blue"><?= $translator->translate("Faturamento") ?></span>
                        </div>
                        <div class="col-6">
                            <h3 class="color-nocturne-purple mb-0"><?= (int) $graphData['totals']['transacoes'] ?></h3>
                            <span class="color-stellar-blue"><?= $translator->translate("Total de Vendas") ?></span>
                        </div>
                    </div>
                    <canvas id="salesChart" class="w-100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <a class="h4 link-nocturne-purple" href="<?= url('events/my-events') ?>"><?= $translator->translate('Meus Eventos') ?></a>
                    <?php foreach ($todayEvents as $event): ?>
                        <div class="lembrete-evento ps-3 my-2">
                            <a class="link-nocturne-purple" href="<?= url('events/id/' . $event['evento_id']) ?>"><?= htmlspecialchars($event['evento_nome']) ?></a>
                            <?php if (!empty($event['hora_inicial'])): ?>
                                <span class="text-muted ms-2"><?= $event['hora_inicial'] . ' - ' . $event['hora_final'] ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div id="calendar" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/vendors/chart.js/Chart.min.js") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/pt-br.global.min.js"></script>
<script>
    <?php
        $jsonEvents = [];
        foreach ($events as $event) {
            $item = [
                'title' => $event['evento_nome'],
                'start' => $event['evento_data_inicial'],
                'url' => url('events/id/' . $event['evento_id']),
                'classNames' => ['calendario-evento', 'evento-' . $event['status']]
            ];
            if (date('Y-m-d', strtotime($event['evento_data_inicial'])) != date('Y-m-d', strtotime($event['evento_data_final']))) {
                $item['end'] = $event['evento_data_final'];
            }
            $jsonEvents[] = $item;
        }
    ?>
    const homeData = {
        currency: <?= json_encode($currency) ?>,
        stock: {
            labels: <?= json_encode([
                $translator->translate('Estoque bom'),
                $translator->translate('Estoque baixo'),
                $translator->translate('Sem estoque'),
                $translator->translate('Estoque morto')
            ]) ?>,
            values: <?= json_encode([
                (int) ($stocks['goodStock'] ?? 0),
                (int) ($stocks['lowStock'] ?? 0),
                (int) ($stocks['outOfStock'] ?? 0),
                (int) ($stocks['deadStock'] ?? 0)
            ]) ?>,
            datasetLabel: <?= json_encode($translator->translate('Produtos')) ?>
        },
        sales: {
            labels: <?= json_encode(array_values($graphData['labels'])) ?>,
            value: <?= json_encode(array_values($graphData['values']['total_vendas'])) ?>,
            quantity: <?= json_encode(array_values($graphData['values']['total_transacoes'])) ?>
        },
        events: <?= json_encode($jsonEvents) ?>,
        locale: <?= json_encode($translator->getLang()) ?>,
        buttonText: {
            today: <?= json_encode($translator->translate('Hoje')) ?>,
            month: <?= json_encode($translator->translate('Mês')) ?>,
            week: <?= json_encode($translator->translate('Semana')) ?>,
            day: <?= json_encode($translator->translate('Dia')) ?>
        },
        newEventText: <?= json_encode($translator->translate('Novo evento')) ?>
    };
</script>
<script src="<?= url("assets/js/home-logged.js?ts=" . time()) ?>"></script>
<?= $this->stop() ?>
