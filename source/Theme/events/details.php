<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css" integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= url("assets/css/events/details.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>

<section class="content avoid-navbar">
    <?php if (!empty($photos)) {?>
    <section class="section-slide">
        <div id="photosCarousel" class="col-12 carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators" id="photo-carousel-indicators">
                <?php 
                    foreach($photos as $photoKey => $photo) {
                        $current = $photoKey == 0 ? 'class="active"' : '';
                ?>
                    <button type="button" data-bs-target="#photosCarousel" data-bs-slide-to="<?= $photoKey ?>" <?= $current ?> aria-label="<?= $photo['label']?>"></button>
                <?php } ?>
            </div>
            <div class="carousel-inner" id="carousel-items">
                <?php 
                    foreach($photos as $photoKey => $photo) {
                        $active = $photoKey == 0 ? 'active' : '';
                ?>
                    <div class="carousel-item <?= $active?>">
                        <div class="slide d-flex justify-content-center">
                            <div class="slide-background" data-bg="<?= $photo['url'] ?>"></div>
                            <?php if ($photoKey == 0) { ?>
                                <!-- Preload the first image -->
                                <link rel="preload" href="<?= $photo['url'] ?>" as="image">
                                <img class="slide-item" src="<?= $photo['url'] ?>" alt="<?= $photo['label'] ?>" loading="eager">
                            <?php } else { ?>
                                <img class="slide-item" src="<?= $photo['url'] ?>" alt="<?= $photo['label'] ?>" loading="lazy">
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <button id="slide-btn-previous" class="carousel-control-prev" type="button" data-bs-target="#photosCarousel" data-bs-slide="prev" <?php if (count($photos) == 1) echo 'style="display: none;"'?>>
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button id="slide-btn-next" class="carousel-control-next" type="button" data-bs-target="#photosCarousel" data-bs-slide="next" <?php if (count($photos) == 1) echo 'style="display: none;"'?>>
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </section>
    <?php } ?>
    <section class="container pt-4 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6 color-nocturne-purple d-flex align-items-center" id="eventTitle">
                <h1><?= $event['evento_nome']?></h1>
                <?php if ($user == $event['evento_proprietario']): ?>
                    <a class="btn btn-stellar-blue text-white text-decoration-none ms-3" href="<?= url('events/edit/'.$event['evento_id']) ?>">
                        <i class="fa fa-pen-to-square"></i> <?= $translator->translate("Editar") ?>
                    </a>
                <?php endif;?>
                <?php if ($event['total_inscritos'] > 0): ?>
                <span class="badge bg-lavanda ms-3" id="totalSubscribersBadge">
                    <span class="iconify" data-icon="mdi:account-multiple" data-inline="false"></span>
                    <?= $event['total_inscritos'] ?> <?= $translator->translate($event['total_inscritos'] == 1 ? 'Seguidor' : 'Seguidores') ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="col-md-6 d-md-flex d-block justify-content-end" id="column-buttons">
                <div class="event-buttons">
                    <button class="btn btn-favorite shadow-none btn-event-action" id="btn-favorite"><span class="iconify btn-icon" id="btn-favorite-icon" data-icon="<?= !empty($event['inscricao_id']) && $event['inscricao_cancelada'] != 1 ? 'mdi:heart' : 'mdi:heart-outline' ?>" data-inline="false"></span></button>
                    <button class="btn btn-share shadow-none btn-event-action" id="btn-share"><span class="iconify btn-icon" data-icon="mdi:share-variant" data-inline="false"></span></button>
                </div>
            </div>
        </div>
        <div class="row p-2 show-with-animation" id="eventTabs" style="<?= !empty($event['inscricao_id']) && $event['inscricao_cancelada'] != 1 ? '' : 'display:none;' ?>">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link link-stellar-blue text-decoration-none active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><?= $translator->translate("Informações") ?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link link-stellar-blue text-decoration-none" id="meus-dados-tab" data-bs-toggle="tab" data-bs-target="#meus-dados" type="button" role="tab" aria-controls="meus-dados" aria-selected="false"><?= $translator->translate("Meus Dados") ?></button>
                </li>
            </ul>
        </div>
    </section>
    <div class="tab-content">
        <div id="home" class="tab-pane fade show active" role="tabpanel" aria-labelledby="home-tab">
            <section class="container mb-5">
                <div class="row my-2">
                    <div class="col-md-6" id="eventAddress">
                        <b class="color-stellar-blue"><span class="iconify" data-icon="mdi:map-marker" data-inline="false"></span> <?= !empty($event['endereco_com_complemento']) ? $event['endereco_com_complemento'] : $translator->translate('Local não definido') ?></b>
                    </div>
                    <?php if (!empty($event['evento_produtor'])) {?>
                        <div class="col-md-6 d-md-flex d-block justify-content-end" id="column-productor">
                            <p>Produtor: <span class="color-stellar-blue" id="eventProductor"><?= $event['evento_produtor'] ?></span></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="row mt-3">
                    <?php if (!empty($event['evento_descricao'])) {?>
                        <div class="col-md-6 col-12" id="column-description">
                            <h4 class="mb-3"><?= $translator->translate("Descrição") ?></h4>
                            <section><?= nl2br($event['evento_descricao']) ?></section>
                        </div>
                    <?php } ?>
                    <?php if (!empty($days)): ?>
                        <div class="col-md-6 col-12">
                            <div class="row" id="daysRow">
                                <h4 class="mb-3"><?= $translator->translate("Datas") ?></h4>
                                <?php foreach($days as $day) {?>
                                    <div class="col-xxl-4 col-xl-6 col-12 mb-3 date-card">
                                        <div class="card h-100 flex-column position-relative">
                                            <div class="p-3 pb-2">
                                                <h5 class="mb-0"><?= $translator->getLang() == 'pt-br' ? formatWeekDateToPortuguese($day['evento_data_dia']) : $translator->translate(date('l', strtotime($day['evento_data_dia']))) ?></h5>
                                            </div>
                                            <div class="card-body pt-0">
                                                <h5 class="card-title"><?= date('d/m/Y', strtotime($day['evento_data_dia']))?></h5>
                                                <span class="card-subtitle mb-2 text-muted"><?= date('H:i', strtotime($day['evento_data_hora_inicial'])).(!empty($day['evento_data_hora_final']) ? ' - '.date('H:i', strtotime($day['evento_data_hora_final'])) : '') ?></span>
                                                <?php if (!empty($day['evento_data_observacao'])) { ?>
                                                    <span class="btn btn-stellar-blue d-flex align-items-center mt-3 edit-date" data-dateId="<?= $day['evento_data_id'] ?>">
                                                        <i class="fa-solid fa-eye ms-1 me-2" style="text-align: center;"></i> <?= $translator->translate("Observações") ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($advantages)): ?>
                        <div class="col-md-6 col-12">
                            <h4 class="my-3"><?= $translator->translate("Vantagens") ?></h4>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($advantages as $advantage) { ?>
                                    <span class="badge bg-nocturne-purple"><?= $advantage['nome'] ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($prices)): ?>
                        <div class="col-md-6 col-12">
                            <div class="row" id="pricesRow">
                                <h4 class="my-3"><?= $translator->translate("Taxas e Custos") ?></h4>
                                <?php foreach ($prices as $price) { ?>
                                    <div class="col-xxl-4 col-xl-6 col-12 mb-3">
                                        <div class="card h-100 flex-column position-relative">
                                            <div class="card-body d-flex flex-column">
                                                <div class="">
                                                    <h5 class="card-title"><?= $price['evento_taxa_titulo'] ?></h5>
                                                </div>
                                                <h6 class="card-subtitle mb-2 text-muted flex-grow-1"><?= $event['evento_moeda'] ?> <?= moedaReal($price['evento_taxa_valor']) ?></h6>
                                                <?php if (!empty($price['evento_taxa_observacao'])) { ?>
                                                    <span class="btn btn-stellar-blue d-flex align-items-center mt-3 edit-date" data-priceId="<?= $price['evento_taxa_id'] ?>">
                                                        <i class="fa-solid fa-eye ms-1 me-2" style="text-align: center;"></i> Observações
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php if (!empty($event['endereco_completo'])) {?>
            <section id="section-map" class="bg-nocturne-purple py-3">
                <div class="container">
                    <div class="row mb-3" id="row-map">
                        <h4 class="mb-3 color-snow-white"><?= $translator->translate("Local") ?></h4>
                        <div id="mapa">
                            <iframe frameborder='0' title="Google Maps do local do evento" style='border:0; width: 100%; min-height: 500px;' src='https://www.google.com/maps?q=<?= urlencode($event['endereco_completo'])?>&output=embed' allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </section>
            <?php } ?>
        </div>
        <div id="meus-dados" class="tab-pane fade" role="tabpanel" aria-labelledby="meus-dados-tab">
            <form class="container mb-5" id="form-userSubscription">
                <div class="row my-2">
                    <div class="col-12"><h3><?= $translator->translate("Meus Dados") ?></h3></div>
                </div>
                <div class="row my-2"> 
                    <div class="col-md-6 col-12 mb-3">
                        <div class="">
                            <label for="inputName" class="form-label px-0"><?= $translator->translate("Status da Inscrição") ?></label>
                            <select class="form-select input-stellar-blue" name="inputSubscriptionStatus" id="inputSubscriptionStatus">
                                <option value="pendente" <?= $event['status'] == 'pendente' ? 'selected' : '' ?>><?= $translator->translate("Pendente") ?></option>
                                <option value="realizada" <?= $event['status'] == 'realizada' ? 'selected' : '' ?>><?= $translator->translate("Realizada") ?></option>
                                <option value="aprovada" <?= $event['status'] == 'aprovada' ? 'selected' : '' ?>><?= $translator->translate("Aprovada") ?></option>
                                <option value="reprovada" <?= $event['status'] == 'reprovada' ? 'selected' : '' ?>><?= $translator->translate("Reprovada") ?></option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <label for="inputUserTags" class="form-label px-0"><?= $translator->translate("Tags") ?></label>
                            <select class="form-select input-stellar-blue" name="inputUserTags[]" id="inputUserTags" multiple="multiple">
                                <?php if (!empty($event['inscricao_tags_evento'])): ?>
                                    <?php foreach (explode('|', $event['inscricao_tags_evento']) as $tag): ?>
                                        <option value="<?= trim($tag) ?>" selected><?= trim($tag) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-3">
                        <label for="inputUserObservation" class="form-label"><?= $translator->translate("Observações") ?></label>
                        <textarea class="form-control input-stellar-blue" name="inputUserObservation" id="inputUserObservation" rows="4" placeholder="<?= $translator->translate("Observações do usuário...") ?>"><?= !empty($event['inscricao_observacao_loja']) ? $event['inscricao_observacao_loja'] : '' ?></textarea>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-12">
                        <label class="form-label px-0"><?= $translator->translate("Feedback (preencher após o evento)") ?></label>
                        <textarea class="form-control input-stellar-blue" name="inputUserFeedback" id="inputUserFeedback" rows="4" placeholder="<?= $translator->translate("Feedback do usuário...") ?>" <?= ($event['evento_data_final'] < date('Y-m-d')) ? '' : 'disabled' ?>><?= !empty($event['inscricao_feedback_loja']) ? $event['inscricao_feedback_loja'] : '' ?></textarea>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-stellar-blue" id="btn-save-userSubscription"><?= $translator->translate("Salvar") ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <section id="slide-item-modal">
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered slide-dialog">
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <img  class="img-fluid img-modal" alt="Imagem">
                </div>
            </div>
        </div>
    </section>
    <section id="share-modal">
        <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shareModalLabel"><?= $translator->translate("Compartilhar") ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
                            <div class="flex-grow-1 text-start p-2 border rounded overflow-hidden text-nowrap" id="linkToCopy">
                                <?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>
                            </div>
                            <button class="btn btn-stellar-blue ms-2" id="copyUrl">
                                <i class="fas fa-link"></i> <?= $translator->translate("Copiar") ?>
                            </button>
                        </div>
                        <div class="d-flex justify-content-center mb-5">
                            <div id="qrcode"></div>
                        </div>
                        <div class="wrap-modal-slider mb-3">
                            <div class="share-carousel">
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-success mb-2" id="shareWhatsApp" href="https://api.whatsapp.com/send?text=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-whatsapp fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-instagram mb-2" id="shareInstagram" href="https://www.instagram.com/share?text=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-instagram fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-dark mb-2" id="shareTwitter" href="https://twitter.com/intent/tweet?text=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-x-twitter fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-primary mb-2" id="shareTelegram" href="https://telegram.me/share/url?url=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-telegram-plane fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-danger mb-2" id="sharePinterest" href="https://www.pinterest.com/pin/create/button/?url=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-pinterest fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-primary mb-2" id="shareFacebook" href="https://www.facebook.com/sharer/sharer.php?u=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-facebook-f fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-info mb-2" id="shareLinkedIn" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-linkedin-in fa-2x"></i>
                                    </a>
                                </div>
                                <div class="share-button d-flex flex-column align-items-center">
                                    <a class="btn btn-secondary mb-2" id="shareEmail" href="mailto:?subject=Check%20out%20this%20event&body=<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-envelope fa-2x"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast bg-stellar-blue" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body text-light">
                <?= $translator->translate("URL copiada para a área de transferência!") ?>
            </div>
        </div>
    </div>
</section>
<section id="modal-date-observation">
    <div class="modal fade" id="dateObservationModal" tabindex="-1" aria-labelledby="dateObservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateObservationModalLabel"><?= $translator->translate("Observações da Data") ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="dateStartHour"></span><span id="dateEndHour"></span>
                    <hr>
                    <div id="dateObservationContent"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="modal-price-observation">
    <div class="modal fade" id="priceObservationModal" tabindex="-1" aria-labelledby="priceObservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="priceObservationModalLabel"><?= $translator->translate("Observações da Taxa") ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="priceValue"></span>
                    <hr>
                    <div id="priceObservationContent"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="toasts-section">
    <div class="toast align-items-center text-light bg-success border-0 toast-sucesso m-3" id="myToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Título</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody">
            Mensagem do toast.
        </div>
    </div>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<!-- Outros scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js" integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script defer>
    const eventId = <?= $event['evento_id'] ?>;
    const url = window.location.href;
    const text = document.title;
    const datesWithObservations = <?php
        $jsonDays = [];
        foreach ($days as $day) {
            if (empty($day['evento_data_observacao'])) continue;
            $jsonDays[$day['evento_data_id']] = $day;
            $jsonDays[$day['evento_data_id']]['evento_data_observacao'] = htmlspecialchars($day['evento_data_observacao'], ENT_QUOTES, 'UTF-8');
            $jsonDays[$day['evento_data_id']]['evento_data_dia'] = date('d/m/Y', strtotime($day['evento_data_dia']));
            $jsonDays[$day['evento_data_id']]['evento_data_hora_inicial'] = date('H:i', strtotime($day['evento_data_hora_inicial']));
            if (!empty($day['evento_data_hora_final'])) 
                $jsonDays[$day['evento_data_id']]['evento_data_hora_final'] = date('H:i', strtotime($day['evento_data_hora_final']));
        }
        echo empty($jsonDays) ? '{}' : json_encode($jsonDays);
        unset($jsonDays);
        
    ?>;
    const pricesWithObservations = <?php
        foreach ($prices as $price) {
            if (empty($price['evento_taxa_observacao'])) continue;
            $jsonPrices[$price['evento_taxa_id']] = $price;
            $jsonPrices[$price['evento_taxa_id']]['evento_taxa_observacao'] = htmlspecialchars($price['evento_taxa_observacao'], ENT_QUOTES, 'UTF-8');
            $jsonPrices[$price['evento_taxa_id']]['evento_taxa_titulo'] = htmlspecialchars($price['evento_taxa_titulo'], ENT_QUOTES, 'UTF-8');
            $jsonPrices[$price['evento_taxa_id']]['evento_taxa_valor'] = moedaReal($price['evento_taxa_valor']);
        }
        echo empty($jsonPrices) ? '{}' : json_encode($jsonPrices);
        unset($jsonPrices);
    ?>;
</script>
<script>
    const translator = {
        tagsPlaceholder: "<?= $translator->translate("Selecione as tags do evento") ?>",
        tagsLanguage: "<?= $translator->translate("Adicione tags personalizadas") ?>",
        saveSuccessTitle: "<?= $translator->translate("Alteração Salva!") ?>",
        errorTitle: "<?= $translator->translate("Erro!") ?>",
    };
</script>
<script src="<?= url("assets/js/events/details.js") ?>" defer></script>
<?= $this->stop() ?>