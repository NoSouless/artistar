<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<link rel="stylesheet" href="<?= url("assets/css/events/myEvents.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="minimum-height avoid-navbar">
    <section class="section-banner">
        <div class="container">
            <div class="row py-5">
                <div class="col-xl-6 col-12">
                    <div class="row">
                        <a class="h1 mb-3 link-nocturne-purple" href="<?= url('events/my-events') ?>"><?= $translator->translate("Meus Eventos") ?></a>
                        <p class="fs-5">
                            <?= $translator->translate("Veja em que situação estão suas inscrições em eventos!") ?><br>
                        </p>
                        <span class="text-muted mb-3">
                            <?= $translator->translate("Clique em \"Novo evento\" ou selecione uma data no calendário para criar um novo evento.Arraste entre os dias para selecionar mais de um dia.") ?><br>
                            <?= $translator->translate('Arraste entre os dias para selecionar mais de um dia.') ?>
                        </span>
                        <ul class="list-unstyled row">
                            <li class="col-md-6 col-12 d-flex align-items-center mb-2">
                                <span class="dot bg-lavanda me-2 rounded-5" style="height:10px; width:10px;"></span><?php echo $translator->translate("Criados por mim"); ?> (<?= $totals['total_criados'] ?>)
                            </li>
                            <li class="col-md-6 col-12 d-flex align-items-center mb-2">
                                <span class="dot bg-cotton-candy me-2 rounded-5" style="height:10px; width:10px;"></span><?php echo $translator->translate("Pendentes"); ?> (<?= $totals['total_pendente'] ?>)
                            </li>
                            <li class="col-md-6 col-12 d-flex align-items-center mb-2">
                                <span class="dot bg-stellar-blue me-2 rounded-5" style="height:10px; width:10px;"></span><?php echo $translator->translate("Realizadas"); ?> (<?= $totals['total_realizada'] ?>)
                            </li>
                            <li class="col-md-6 col-12 d-flex align-items-center mb-2">
                                <span class="dot bg-nocturne-purple me-2 rounded-5" style="height:10px; width:10px;"></span><?php echo $translator->translate("Aprovadas"); ?> (<?= $totals['total_aprovada'] ?>)
                            </li>
                            <li class="col-md-6 col-12 d-flex align-items-center mb-2">
                                <span class="dot bg-danger me-2 rounded-5" style="height:10px; width:10px;"></span><?php echo $translator->translate("Reprovadas"); ?> (<?= $totals['total_reprovada'] ?>)
                            </li>
                            <li class="col-md-6 col-12 d-flex align-items-center mb-2">
                                <span class="dot bg-gray me-2 rounded-5" style="height:10px; width:10px;"></span><?php echo $translator->translate("Finalizados"); ?> (<?= $totals['total_finalizados'] ?>)
                            </li>
                        </ul>
                    </div>
                    <hr>
                    <div class="row">
                        <span class="h4 color-gray"><?= $translator->translate("Eventos de Hoje") ?></span>
                    </div>
                    <?php foreach ($todayEvents as $event): ?>
                        <div class="row mb-3 p-3">
                            <div class="lembrete-evento">
                                <span class="h5">
                                    <a class="link-nocturne-purple" href="<?= url('events/id/'.$event['evento_id']) ?>"><?= $event['evento_nome'] ?></a>
                                </span>
                                <br>
                                <span class="text-muted">
                                    <?= $event['hora_inicial'].' - '.$event['hora_final'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>     
                </div>
                <div class="col-xl-6 col-12">
                    <div id="calendar"></div>
                </div>
            </div>
            <hr>
        </div>    
    </section>
    <section class="section-eventos">
        <div class="container">
            <div class="pb-5">
                <span class="h2"><?= $translator->translate("Todos os Eventos") ?></span>
            </div>
            <div class="row lista-eventos" id="eventos">
                <?php foreach ($events as $event): ?>
                <a class="col-lg-3 col-md-4 col-sm-6 mb-4 evento" href="<?= url('events/id/'.$event['evento_id']) ?>">
                    <div class="card">
                        <img class="card-img-top" src="<?= !empty($event['thumbnail']) ? $event['thumbnail'] : '/assets/image/200x300.png' ?>" alt="Card image cap">
                        <?php if (!empty($event['evento_data_inicial']) && !empty($event['evento_data_final'])): ?>
                            <span class="image-overlay">
                                <?php if (date('Y-m-d', strtotime($event['evento_data_inicial'])) != date('Y-m-d', strtotime($event['evento_data_final']))) : ?>
                                    <?= date('d/m/Y', strtotime($event['evento_data_inicial'])) . ' - ' . date('d/m/Y', strtotime($event['evento_data_final'])) ?>
                                <?php else: ?>
                                    <?= date('d/m/Y', strtotime($event['evento_data_inicial'])).' '.(date('H:i', strtotime($event['evento_data_inicial'])).' - '.date('H:i', strtotime($event['evento_data_final']))) ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                <span class="color-stellar-blue nome-produto"><?= $event['evento_nome'] ?></span>
                                <?php 
                                    switch ($event['status']) {
                                        case 'criado':
                                            $badgeClass = 'bg-lavanda';
                                            $badgeText = $translator->translate('Criado');
                                            break;
                                        case 'finalizado':
                                            $badgeClass = 'bg-secondary';
                                            $badgeText = $translator->translate('Finalizado');
                                            break;
                                        case 'pendente':
                                            $badgeClass = 'bg-cotton-candy';
                                            $badgeText = $translator->translate('Pendente');
                                            break;
                                        case 'realizada':
                                            $badgeClass = 'bg-stellar-blue';
                                            $badgeText = $translator->translate('Realizada');
                                            break;
                                        case 'aprovada':
                                            $badgeClass = 'bg-nocturne-purple';
                                            $badgeText = $translator->translate('Aprovada');
                                            break;
                                        case 'reprovada':
                                            $badgeClass = 'bg-danger';
                                            $badgeText = $translator->translate('Reprovada');
                                            break;
                                        default:
                                            $badgeClass = 'bg-gray';
                                            $badgeText = $translator->translate('Desconhecido');
                                    }
                                    echo "<span class='badge $badgeClass'>$badgeText</span>";
                                ?>
                            </h5>
                            <p class="card-text descricao-evento">
                                <?php foreach (explode('|', $event['inscricao_tags_evento']) as $tag): ?>
                                    <span class="badge bg-light text-dark me-1"><?= trim($tag) ?></span>
                                <?php endforeach; ?>                    
                            </p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/pt-br.global.min.js"></script>
<script>
    <?php 
        $jsonEvents = [];
        foreach ($events as $key =>$event) {
            // if (empty($event['evento_data_inicial'])) continue;
            $jsonEvents[$key] = [
                'title' => $event['evento_nome'],
                'start' => $event['evento_data_inicial'],
                'url' => url('events/id/'.$event['evento_id']),
                'classNames' => ['calendario-evento', 'evento-'.$event['status']]
            ];
            if (date('Y-m-d', strtotime($event['evento_data_inicial'])) != date('Y-m-d', strtotime($event['evento_data_final'])))
                $jsonEvents[$key]['end'] = $event['evento_data_final'];
        }
        echo "const events = ".json_encode($jsonEvents).";";
    ?>
    const buttonHints = {
        today: "<?= $translator->translate('Hoje') ?>",
        month: "<?= $translator->translate('Mês') ?>",
        week: "<?= $translator->translate('Semana') ?>",
        day: "<?= $translator->translate('Dia') ?>",
        prev: "<?= $translator->translate('Mês anterior') ?>",
        next: "<?= $translator->translate('Próximo mês') ?>"
    };
    const buttonText = {
        today: "<?= $translator->translate('Hoje') ?>",
        month: "<?= $translator->translate('Mês') ?>",
        week: "<?= $translator->translate('Semana') ?>",
        day: "<?= $translator->translate('Dia') ?>"
    };
    const locale = "<?= $translator->getLang() ?>";
    const newEventText = "<?= $translator->translate('Novo evento') ?>";
</script>
<script src="<?= url("assets/js/events/myEvents.js?ts=" . time()) ?>"></script>

<?= $this->stop() ?>