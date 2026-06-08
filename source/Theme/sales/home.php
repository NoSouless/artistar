<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= url("assets/css/sales/home.css?t=" . time()) ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<form id="new-sale-form" class="container pt-3 minimum-height" method="post" action="<?= url("/sales/insert") ?>">
    <div class="row avoid-navbar">
        <div class="col-sm-6 col-12 mb-3 mb-sm-0 px-sm-0">
            <div>
                <h1 class="text-center text-sm-start color-nocturne-purple"><?=  $translator->translate("Nova Venda") ?></h1>
            </div>
        </div>
    </div>
    <div class="row px-sm-0 p-3">
        <div class="col-lg-4 border rounded col-12 mb-3">
            <div class="row p-3">
                <input type="text" id="search" class="form-control input-stellar-blue" placeholder="<?= $translator->translate("Digite o nome do produto...") ?>">
                <div id="suggestions" class="list-group rounded mt-1 shadow-sm" style="max-height: 200px; overflow-y: auto;">
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="row ps-lg-3">
                <div class="col-12 border rounded p-3 mb-3">
                    <h5 class="mb-3"><?=  $translator->translate("Carrinho") ?></h5>
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
                    <button type="button" class="btn btn-stellar-blue" id="finalizar-venda"><?= $translator->translate("Finalizar Venda") ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertModalLabel"><?= $translator->translate("Finalizar Venda") ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= $translator->translate("Você está prestes a finalizar a venda. Por favor, confirme os detalhes abaixo:") ?>
                    <div class="my-3">
                        <?= $translator->translate("Total da Venda:") ?> <span class="color-nocturne-purple">R$<span id="total-price-modal">0,00</span></span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="event-select" class="form-label"><?= $translator->translate("Evento") ?></label>
                            <select class="form-select form-select-sm input-stellar-blue" id="event-select" name="event_id" aria-label="Evento">
                                <?php foreach ($storeEvents as $event): ?>
                                    <option value="<?= $event['id'] ?>"><?= $event['nome'] ?></option>
                                <?php endforeach; ?>
                                <option value="0">-- <?= $translator->translate("Venda Avulsa") ?> --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="payment-select" class="form-label"><?= $translator->translate("Método de Pagamento") ?></label>
                            <select class="form-select form-select-sm input-stellar-blue" id="payment-select" name="payment_method" aria-label="<?= $translator->translate("Método de pagamento") ?>">
                                <?php foreach ($paymentMethods as $alias => $name): ?>
                                    <option value="<?= $alias ?>"><?= $translator->translate($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="payment-select" class="form-label"><?= $translator->translate("Momento da venda") ?></label>
                            <input type="datetime-local" class="form-control form-control-sm input-stellar-blue" id="sale-datetime" name="sale_datetime" value="<?= date("Y-m-d\TH:i") ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6 col-12">
                            <div class="mb-3 form-check form-switch form-switch-sm">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="paid" checked value="1">
                                <label class="form-check-label" for="flexSwitchCheckDefault"><?= $translator->translate("Pedido pago") ?></label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="mb-3 form-check form-switch form-switch-sm">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="delivered" checked value="1">
                                <label class="form-check-label" for="flexSwitchCheckDefault"><?= $translator->translate("Pedido entregue") ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate("Cancelar") ?></button>
                    <button type="button" class="btn btn-nocturne-purple" id="accept-insert"><?= $translator->translate("Finalizar!") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<section id="modal-section">
    <!-- Modal for "Sale Inserted!" -->
    <div class="modal fade" id="saleInsertedModal" tabindex="-1" aria-labelledby="saleInsertedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleInsertedModalLabel"><?= $translator->translate("Venda Registrada!") ?></h5>
                </div>
                <div class="modal-body">
                    <?= $translator->translate("A venda") ?> <span class="fw-bold color-stellar-blue">#<span id="sale-id"></span></span> <?= $translator->translate("foi registrada com sucesso! Você pode visualizar os detalhes da venda na seção de vendas.") ?>
                </div>
                <div class="modal-footer">
                    <a href="<?= url("sales-statement") ?>" class="btn btn-nocturne-purple"><?= $translator->translate("Ver Extrato") ?></a>
                    <a href="<?= url("sales") ?>" class="btn btn-stellar-blue"><?= $translator->translate("Nova Venda") ?></a>
                </div>
            </div>
        </div>
    </div>
    <form class="modal fade" id="saleInsertNewProduct" tabindex="-1" aria-labelledby="saleInsertNewProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleInsertNewProductLabel"><?= $translator->translate("Criar novo produto") ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="product-name-input" class="form-label"><?= $translator->translate("Nome do Produto") ?></label>
                            <input type="text" class="form-control input-stellar-blue" id="product-name" name="product_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="new-price" class="form-label"><?= $translator->translate("Preço") ?></label>
                            <input type="text" class="form-control moedaReal input-stellar-blue" id="new-price" name="price" value="0,00">
                        </div>
                        <div class="mb-3 col-6">
                            <label for="new-discount" class="form-label"><?= $translator->translate("Desconto") ?></label>
                            <input type="text" class="form-control moedaReal input-stellar-blue" id="new-discount" name="discount" value="0,00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate("Cancelar") ?></button>
                    <button type="button" class="btn btn-nocturne-purple" id="insert-new-product"><?= $translator->translate("Finalizar!") ?></button>
                </div>
            </div>
        </div>
    </form>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
  const produtos = <?php array_walk_recursive($products,function(&$item){$item=strval($item);}); echo json_encode($products); ?>;
  const dictionary = {
    noResults: [
        "<?= $translator->translate("Não encontrou o que procura?") ?>",
        "<?= $translator->translate("Clique aqui para criar rapidamente um novo produto.") ?>"
    ],
    uni: "<?= $translator->translate("uni") ?>",
    remove: "<?= $translator->translate("Remover") ?>",
    quantity: "<?= $translator->translate("Quantidade") ?>",
    discount: "<?= $translator->translate("Desconto") ?>",
    value: "<?= $translator->translate("Valor") ?>",
    alert: '<?= $translator->translate("Adicione pelo menos um produto ao carrinho antes de finalizar a venda.") ?>',
    error: {
        title: "<?= $translator->translate("Erro ao registrar venda") ?>",
        message: "<?= $translator->translate("Ocorreu um erro ao tentar registrar a venda. Por favor, tente novamente.") ?>"
    }
  }
</script>
  <script src="<?= url("assets/js/sales/home.js?t=" . time()) ?>"></script>
<?= $this->stop() ?>