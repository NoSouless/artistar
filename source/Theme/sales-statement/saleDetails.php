<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= url("assets/css/sales-statement/saleDetails.css?t=" . time()) ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<form id="new-sale-form" class="container pt-3 minimum-height" method="post" action="<?= url("sales/edit") ?>">
    <div class="row avoid-navbar">
        <div class="col-sm-6 col-12 mb-3 mb-sm-0 px-sm-0">
            <div class="d-flex align-items-baseline gap-3">
                <h1 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Venda") ?> #<?= $saleInfo['numero']?></h1>
                <h5 class="text-center text-sm-start color-gray"><?= $translator->translate("Criada em").' '.$saleInfo['data_criacao'].' '.$translator->translate("às").' '.$saleInfo['hora_criacao'] ?></h5>
            </div>
        </div>
        <div class="col-sm-6 col-12 px-sm-0">
            <div class="d-flex justify-content-sm-end justify-content-between">
                <a class="btn btn-gray mx-sm-3" href="<?=url('sales-statement')?>"><?= $translator->translate("Voltar") ?></a>
                <button type="submit" class="btn btn-stellar-blue" id="save-sale" form="sale-form"><?= $translator->translate("Salvar Alterações") ?></button>
            </div>
        </div>
    </div>
    <div class="row px-sm-0 p-3">
        <div class="col-lg-4 border rounded col-12 mb-3">
            <div class="row p-3">
                <div class="col-12 mb-3">
                    <input type="text" id="search" class="form-control input-stellar-blue" placeholder="Digite o nome do produto...">
                    <div id="suggestions" class="list-group rounded mt-1 shadow-sm" style="max-height: 200px; overflow-y: auto;">
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label for="event-select" class="form-label"><?= $translator->translate("Evento") ?></label>
                    <select class="form-select form-select-sm input-stellar-blue" id="event-select" name="event" aria-label="<?= $translator->translate("Evento") ?>">
                        <?php foreach ($events as $event): ?>
                            <option value="<?= $event['evento_id'] ?>" <?= $saleInfo['evento_id'] === $event['evento_id'] ? 'selected' : '' ?>><?= $event['evento_nome'] ?></option>
                        <?php endforeach; ?>
                        <option value="" <?= empty($saleInfo['evento_id']) ? 'selected' : '' ?>>-- <?= $translator->translate("Venda Avulsa") ?> --</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label for="payment-select" class="form-label"><?= $translator->translate("Método de Pagamento") ?></label>
                    <select class="form-select form-select-sm input-stellar-blue" id="payment-select" name="payment_method" aria-label="<?= $translator->translate("Método de Pagamento") ?>">
                        <?php foreach ($paymentMethods as $alias => $name): ?>
                            <option value="<?= $alias ?>" <?= $saleInfo['pagamento'] === $name ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label for="sale-datetime" class="form-label"><?= $translator->translate("Data e Hora da Venda") ?></label>
                    <input class="form-control form-control-sm input-stellar-blue" type="datetime-local" id="sale-datetime" name="sale_datetime" value="<?= date('Y-m-d H:i', strtotime($saleInfo['data_venda'])) ?>" >
                </div>
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-check form-switch form-switch-sm">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckPaid" name="paid" value="1" <?= $saleInfo['pago'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="flexSwitchCheckPaid">
                                    <?= $translator->translate("Pago") ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 d-flex justify-content-end">
                            <input class="form-control form-control-sm input-stellar-blue" style="<?= empty($saleInfo['data_pagamento']) ? 'display:none;' : '' ?>" type="datetime-local" id="payment_date" name="payment_date" value="<?= empty($saleInfo['data_pagamento']) ? date('Y-m-d H:i') : date('Y-m-d H:i', strtotime($saleInfo['data_pagamento'])) ?>" >
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-check form-switch form-switch-sm">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDelivered" name="delivered" value="1" <?= $saleInfo['entregue'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="flexSwitchCheckDelivered">
                                    <?= $translator->translate("Entregue") ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 d-flex justify-content-end">
                            <input class="form-control form-control-sm input-stellar-blue" style="<?= empty($saleInfo['data_entrega']) ? 'display:none;' : '' ?>" type="datetime-local" id="delivery_date" name="delivery_date" value="<?= empty($saleInfo['data_entrega']) ? date('Y-m-d H:i') : date('Y-m-d H:i', strtotime($saleInfo['data_entrega'])) ?>" >
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-check form-switch form-switch-sm">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCanceled" name="canceled" value="1" <?= $saleInfo['cancelada'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="flexSwitchCheckCanceled">
                                    <?= $translator->translate("Cancelada") ?>
                                </label>
                                <i class="fa-solid fa-circle-info color-gray ms-2" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate("Vendas canceladas não aparecem em relatórios e produtos vendidos terão estoque reposto!") ?>"></i>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 d-flex justify-content-end">
                            <input class="form-control form-control-sm input-stellar-blue" style="<?= empty($saleInfo['data_cancelamento']) ? 'display:none;' : '' ?>" type="datetime-local" id="cancellation_date" name="cancellation_date" value="<?= empty($saleInfo['data_cancelamento']) ? date('Y-m-d H:i') : date('Y-m-d H:i', strtotime($saleInfo['data_cancelamento'])) ?>" >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="row ps-lg-3">
                <div class="col-12 border rounded p-3 mb-3">
                    <h5 class="mb-3"><?= $translator->translate("Carrinho") ?></h5>
                    <div class="row selected-products" id="selected">
                        <div class="no-products"><?= $translator->translate("Adicione produtos ao carrinho para iniciar a venda.") ?></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-xl-6 col-12 mt-xl-0 mt-2 row mx-0">
                            <div class="col-6 d-flex align-items-center  p-0">
                                <?= $translator->translate("Desconto") ?> (R$):
                            </div>
                            <div class="col-6 d-flex align-items-center  p-0">
                                <input id="total-discount-input" name="total_discount" type="text" class="form-control moedaReal input-stellar-blue" value="0,00" inputmode="decimal">
                            </div>
                        </div>
                        <div class="col-xl-6 col-12 mt-xl-0 mt-2 row mx-0">
                            <div class="col-6 d-flex align-items-center p-0">
                                <?= $translator->translate("Total") ?> (R$):
                            </div>
                            <div class="col-6 d-flex align-items-center p-0">
                                <input id="total-input" name="total_price" type="text" class="form-control moedaReal input-stellar-blue" value="0,00" inputmode="decimal">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-end pe-0">
                    <div class="d-flex justify-content-sm-end justify-content-between">
                        <a class="btn btn-gray mx-sm-3" id="discard-changes-btn" href="<?=url('sales-statement/sale/'.$saleInfo['id'])?>"><?= $translator->translate("Descartar Alterações") ?></a>
                        <button type="submit" class="btn btn-stellar-blue" id="save-sale-2" form="sale-form"><?= $translator->translate("Salvar Alterações") ?></button>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    <input type="hidden" name="sale_id" value="<?= $saleInfo['id'] ?>">
</form>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
  const produtos = <?php array_walk_recursive($products,function(&$item){$item=strval($item);}); echo json_encode($products); ?>;
  const selecionados = new Set();
  const dictionary = {
    uni: "<?= $translator->translate("uni") ?>",
    remove: "<?= $translator->translate("Remover") ?>",
    quantity: "<?= $translator->translate("Quantidade") ?>",
    discount: "<?= $translator->translate("Desconto") ?>",
    value: "<?= $translator->translate("Valor") ?>",
    add_product: "<?= $translator->translate("Adicione pelo menos um produto ao carrinho antes de finalizar a venda.") ?>",
    toast: {
        success: {
            title: "<?= $translator->translate("Venda Atualizada") ?>",
            body: "<?= $translator->translate("A venda foi atualizada com sucesso.") ?>"
        },
        error: {
            title: "<?= $translator->translate("Erro ao atualizar venda") ?>",
            body: "<?= $translator->translate("Ocorreu um erro ao tentar atualizar a venda. Por favor, tente novamente.") ?>"
        }
    }
  };
</script>
<script src="<?= url("assets/js/sales-statement/saleDetails.js?t=" . time()) ?>"></script>
<script>
    adicionarProdutosEmLote(<?php 
        $saleItems = array_values(array_filter($products, function($item) {return !empty($item['id_venda_item']);}));
        array_walk_recursive($saleItems,function(&$item){$item=strval($item);}); 
        echo json_encode($saleItems); 
    ?>)
</script>
<?= $this->stop() ?>