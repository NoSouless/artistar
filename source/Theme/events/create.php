<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" rel="preload" href="<?= url("assets/css/events/create.css") ?>">
<style>
.select2-selection__rendered {
    line-height: 40px !important;
}
.select2-container .select2-selection--single {
    height: 40px !important;
}
.select2-selection__arrow {
    height: 39px !important;
}

.select2-container--default .select2-selection--single .select2-selection__clear {
    height: 40px;
}
</style>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="content avoid-navbar pt-4">
    <form id="eventForm" method="POST" action="<?= url("events/insert") ?>" enctype="multipart/form-data">
        <section class="container">
            <div class="row mb-3">
                <div class="col-sm-6 col-12">
                    <h1 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Novo Evento") ?></h1>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="d-flex justify-content-sm-end justify-content-between">
                        <a href="<?=  url('events/my-events') ?>?>" class="btn btn-gray me-sm-2" id="cancel-event-btn"><?= $translator->translate("Cancelar") ?></a>
                        <button type="submit" class="btn btn-stellar-blue" id="create-event-btn" form="eventForm"><?= $translator->translate("Criar Evento") ?></button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-12 mb-3">
                    <label for="filter-name" class="form-label">*<?= $translator->translate("Nome") ?></label>
                    <input class="form-control input-stellar-blue" name="eventTitle" type="text" id="eventTitle" placeholder="<?= $translator->translate("Nome do Evento") ?>" required> 
                </div>
                <div class="col-md-4 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Produtor") ?></label>
                    <input class="form-control input-stellar-blue" name="eventProducer" type="text" id="eventProducer" placeholder="<?= $translator->translate("Nome do Produtor") ?>"> 
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3"><?= $translator->translate("Endereço") ?></h4>
                </div>
                <!-- <div class="col-md-2 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("CEP") ?></label>
                    <input class="form-control input-stellar-blue" name="eventCep" type="text" id="eventCep" placeholder="<?= $translator->translate("CEP") ?>"> 
                </div> -->
                <div class="col-md-2 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("País") ?></label>
                    <input type="text" class="form-control input-stellar-blue" name="eventCountry" id="eventCountry" placeholder="<?= $translator->translate("Ex: Brasil") ?>">
                </div> 
                <div class="col-md-1 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Moeda") ?></label>
                    <input type="text" class="form-control input-stellar-blue" name="eventCurrency" id="eventCurrency" placeholder="<?= $translator->translate("Ex: R$") ?>">
                </div> 
                <div class="col-md-2 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Estado/Província") ?></label>
                    <input type="text" class="form-control input-stellar-blue" name="eventState" id="eventState" placeholder="<?= $translator->translate("Estado/Província") ?>">
                </div>
                <div class="col-md-4 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Cidade") ?></label>
                    <input type="text" class="form-control input-stellar-blue" name="eventCity" id="eventCity" placeholder="<?= $translator->translate("Cidade") ?>">
                </div>
                 <div class="col-md-3 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Bairro") ?></label>
                    <input class="form-control input-stellar-blue" name="eventNeighborhood" type="text" id="eventNeighborhood" placeholder="<?= $translator->translate("Bairro") ?>"> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Endereço") ?></label>
                    <input class="form-control input-stellar-blue" name="eventAddress" type="text" id="eventAddress" placeholder="<?= $translator->translate("Endereço") ?>"> 
                </div>
                <div class="col-md-3 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Número") ?></label>
                    <input class="form-control input-stellar-blue" name="eventNumber" type="text" id="eventNumber" placeholder="<?= $translator->translate("Número") ?>"> 
                </div>
                <div class="col-md-3 col-12 mb-3">
                    <label for="filter-name" class="form-label"><?= $translator->translate("Complemento") ?></label>
                    <input class="form-control input-stellar-blue" name="eventComplement" type="text" id="eventComplement" placeholder="<?= $translator->translate("Complemento") ?>"> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3" id="column-description">
                    <div class="mb-3">
                        <h4 class="mb-3"><?= $translator->translate("Descrição") ?></h4>
                        <textarea class="form-control input-stellar-blue" name="eventDescription" rows="6" resizable></textarea>
                    </div>
                    <!-- <div class="mb-3">
                        <h4 class="mb-3"><?= $translator->translate("Vantagens") ?></h4>
                        <select id="eventAdvantagesSelect" name="eventAdvantages[]" class="form-select input-stellar-blue mb-3" multiple>
                            <?php foreach ($advantages as $advantage): ?>
                                <option value="<?= $advantage['id'] ?>"><?= $advantage['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> -->
                    <div class="mb-3">
                        <div class="mb-3 form-check form-switch form-switch-sm">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexPrivatEvent" name="private" value="1">
                            <label class="form-check-label" for="flexPrivatEvent"><?= $translator->translate("Evento Privado") ?></label>
                            <i class="fa-solid fa-circle-info color-gray ms-2" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" aria-label="<?= $translator->translate("Eventos privados não aparecerão para o público geral, apenas para você e usuários já seguidores do evento.") ?>" data-bs-original-title="<?= $translator->translate("Eventos privados não aparecerão para o público geral, apenas para você e usuários já seguidores do evento.") ?>"></i>
                        </div>
                    </div>  
                </div>
                <div class="col-md-6" id="column-pi">
                    <h4 class="mb-3"><?= $translator->translate("Datas") ?></h4>
                    <div class="row align-items-stretch" id="daysRow">
                        <div role="button" class="col-xxl-4 col-xl-6 col-12 mb-3 text-decoration-none" id="addDateCard">
                            <div class="card text-center h-100 card-hover bg-stellar-blue color-snow-white border-0 flex-column">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-plus fa-2x m-3"></i>
                                    <h6 class="card-title"><?= $translator->translate("Nova Data") ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="my-3"><?= $translator->translate("Taxas e Custos") ?></h4>
                    <div class="row align-items-stretch" id="pricesRow">
                        <div role="button" class="col-xxl-4 col-xl-6 col-12 mb-3 text-decoration-none unsortable" id="addPriceCard">
                            <div class="card text-center h-100 card-hover bg-stellar-blue color-snow-white border-0 flex-column">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-plus fa-2x m-3"></i>
                                    <h6 class="card-title"><?= $translator->translate("Nova Taxa") ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</form>
<section id="datesModal">
    <div class="modal fade" id="newDateModal" tabindex="-1" role="dialog" aria-labelledby="newDateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newDateModalLabel"><?= $translator->translate("Nova Data") ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newDateForm">
                        <div class="mb-3">
                            <label for="dateDay" class="form-label"><?= $translator->translate("Dia") ?></label>
                            <input type="date" class="form-control input-stellar-blue" id="dateDay" required>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 mb-3">
                                <label for="dateTime" class="form-label">*<?= $translator->translate("Hora Inicial") ?></label>
                                <input type="time" class="form-control input-stellar-blue" id="dateTime" required>
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="dateDeadline" class="form-label">
                                    <?= $translator->translate("Hora Final") ?>
                                    <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" 
                                    aria-label="<?= $translator->translate("Deixar em branco caso o evento não tenha hora final definida.") ?>" 
                                    data-bs-original-title="<?= $translator->translate("Deixar em branco caso o evento não tenha hora final definida.") ?>"
                                    ></i>
                                </label>
                                <input type="time" class="form-control input-stellar-blue" id="dateEndTime">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dateObservation" class="form-label"><?= $translator->translate("Observações") ?></label>
                            <textarea class="form-control input-stellar-blue" id="dateObservation" rows="3" resizable></textarea>
                        </div>
                        <button type="submit" class="btn btn-stellar-blue float-end"><?= $translator->translate("Adicionar Data") ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="pricesModal">
    <div class="modal fade" id="newPriceModal" tabindex="-1" role="dialog" aria-labelledby="newPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newPriceModalLabel"><?= $translator->translate("Nova Taxa") ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newPriceForm">
                        <div class="mb-3">
                            <label for="priceName" class="form-label"><?= $translator->translate("Nome da Taxa") ?></label>
                            <input type="text" class="form-control input-stellar-blue" id="priceName" required>
                        </div>
                        <div class="mb-3">
                            <label for="priceAmount" class="form-label"><?= $translator->translate("Valor") ?></label>
                            <input type="text" class="form-control moedaReal input-stellar-blue" id="priceAmount" required value="0,00">
                        </div>
                        <div class="mb-3">
                            <label for="priceObservation" class="form-label"><?= $translator->translate("Observações") ?></label>
                            <textarea class="form-control input-stellar-blue" id="priceObservation" rows="3" resizable></textarea>
                        </div>
                        <input type="hidden" id="priceOrder" value="">
                        <button type="submit" class="btn btn-stellar-blue float-end"><?= $translator->translate("Adicionar Taxa") ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
    const translator = {
        edit: "<?= $translator->translate("Editar") ?>",
        selectCountry: "<?= $translator->translate("Selecione o País") ?>",
        language: "<?= $translator->getLang() ?>"
    };
</script>
<script src="<?= url("assets/js/events/create.js") ?>"></script>
<?= $this->stop() ?>