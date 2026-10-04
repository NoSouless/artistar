<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="<?= url("assets/css/events/home.css") ?>">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
#events-map { height: 400px; border-radius: 8px; }
#events .evento { text-decoration: none; }
#events .evento .card { height: 160px; }
#events .evento .card-body { overflow: hidden; display: flex; flex-direction: column; }
#events .evento .evento-data { font-size: .8rem; opacity: .75; margin-bottom: .25rem; }
#events .evento .nome-produto { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
#events .evento .descricao-evento { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; margin: 0; }
</style>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>

<section class="avoid-navbar minimum-height container">
<section id="search-bar">
        <form id="searchForm" method="GET" action="<?= url('events')?>" class="pt-3">
            <div class="row position-relative">
                <div class="col-12 d-flex align-items-center">
                    <div class="search-wrapper flex-grow-1 position-relative px-1">
                    <div class="store-search-wrap">
                            <i class="fa-solid fa-search store-search-icon"></i>
                            <input type="search" name="search" id="storeSearchInput" class="store-search-input" aria-label="Search in catalog" placeholder="<?= $translator->translate("Pesquisar eventos...") ?>" value="<?= $search ?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <div id="events-map" class="mt-3"></div>
    <hr>
    <section id="events" class="row lista-eventos">
        <?php foreach ($events as $event): ?>
            <a class="col-lg-3 col-md-4 col-sm-6 mb-4 evento" href="<?= url('events/id/'.$event['evento_id']) ?>">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($event['evento_data_inicial']) && !empty($event['evento_data_final'])): ?>
                            <span class="evento-data">
                                <?php if (date('Y-m-d', strtotime($event['evento_data_inicial'])) != date('Y-m-d', strtotime($event['evento_data_final']))) : ?>
                                    <?= date('d/m/Y', strtotime($event['evento_data_inicial'])) . ' - ' . date('d/m/Y', strtotime($event['evento_data_final'])) ?>
                                <?php else: ?>
                                    <?= date('d/m/Y', strtotime($event['evento_data_inicial'])).' '.(date('H:i', strtotime($event['evento_data_inicial'])).' - '.date('H:i', strtotime($event['evento_data_final']))) ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <h5 class="card-title">
                            <span class="color-stellar-blue nome-produto"><?= $event['evento_nome'] ?></span>
                        </h5>
                        <?php
                            $street = trim(($event['evento_endereco_logradouro'] ?? '') . ' ' . ($event['evento_endereco_numero'] ?? ''));
                            $cityState = implode(' - ', array_filter([$event['evento_endereco_cidade'] ?? '', $event['evento_endereco_estado'] ?? '']));
                            $addressLines = array_filter([
                                $street,
                                $event['evento_endereco_complemento'] ?? '',
                                $event['evento_endereco_bairro'] ?? '',
                                $cityState,
                                $event['evento_pais'] ?? '',
                            ]);
                        ?>
                        <p class="card-text descricao-evento">
                            <i class="fa-solid fa-location-dot"></i>
                            <?= htmlspecialchars(implode(', ', $addressLines)) ?>
                        </p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </section>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const isFiltering = <?= !empty($search) ? 'true' : 'false' ?>;
const mapEvents = <?= json_encode(array_map(fn($e) => [
    'name' => $e['evento_nome'],
    'url' => url('events/id/' . $e['evento_id']),
    'address' => implode(', ', array_filter([
        trim(($e['evento_endereco_logradouro'] ?? '') . ' ' . ($e['evento_endereco_numero'] ?? '')),
        $e['evento_endereco_cidade'] ?? '',
        $e['evento_endereco_estado'] ?? '',
        $e['evento_pais'] ?? ''
    ])),
    'city' => implode(', ', array_filter([$e['evento_endereco_cidade'] ?? '', $e['evento_endereco_estado'] ?? '', $e['evento_pais'] ?? ''])),
], $events), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

</script>
<script src="<?= url('assets/js/events/home.js') ?>"></script>
<?= $this->stop() ?>