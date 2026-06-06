<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= url("assets/css/statistics/home.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<section class="container pt-3 mb-3">
    <div class="row avoid-navbar mb-3 d-flex align-items-center">
        <div class="col-lg-4 col-12 px-sm-0 mt-2">
            <h1 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Estatísticas") ?></h1>
        </div>
        <div class="col-lg-8 col-12 px-sm-0 text-sm-end text-center mt-2">
            <input 
                type="date" 
                class="form-control d-inline-block w-auto mb-md-0 mb-3" 
                value="<?= $dataReferencia ?>" 
                id="referenceDate" 
                onchange="window.location.href = '<?= url('statistics/?period='.$periodoSelecionado) ?>' + '&date=' + this.value;"
            >
            <div class="btn-group p-2" role="group" aria-label="<?= $translator->translate("Período") ?>">
                <?php foreach ($periodos as $key => $periodo) {
                    $periodoSelecionado = $_GET['period'] ?? 'day';
                    if ($periodoSelecionado == $key) {
                        echo '<a href="#" class="link-light link-underline-opacity-0 px-2 bg-nocturne-purple rounded-3 text-center align-middle">' . $periodo['name'] . '</a>';
                    } else if (($periodoSelecionado == $periodo['following']) || empty($periodo['following'])) {
                        echo '<a href="'.url('statistics/?period='.$key.'&date='.$dataReferencia).'" class="link-nocturne-purple px-2 text-center align-middle">' . $periodo['name'] . '</a>';
                    } else {
                        echo '<a href="'.url('statistics/?period='.$key.'&date='.$dataReferencia).'" class="link-nocturne-purple px-2 border-dark-subtle border-0 border-end text-center align-middle">' . $periodo['name'] . '</a>';
                    }

                } ?>
            </div>
        </div>
    </div>
    <div class="row p-4 border rounded mb-3">
        <div class="col-12">
            <div class="row mb-3 d-flex justify-content-between align-items-center">
                    <div class="col-md-6 col-12">
                        <h2 class="color-nocturne-purple"><?= $translator->translate("Resumo") ?></h2>
                    </div>
                    <div class="col-md-6 col-12 d-flex justify-content-end align-items-center">
                        <div class="d-flex text-center color-nocturne-purple me-2"><?= $translator->translate("Mostrar:") ?></div>
                        <div class="toggle-container shadow-sm" id="toggle">
                            <div class="slider left" id="slider"></div>
                            <div class="toggle-btn active text-dark" data-side="left" id="graphByValue"><?= $translator->translate("Valor") ?></div>
                            <div class="toggle-btn text-dark" data-side="right" id="graphByQuantity"><?= $translator->translate("Vendas") ?></div>
                        </div>
                    </div>
            </div>
            <div class="row mb-5">
                <div class="col-xl-4 col-12">
                    <div class="row h-100 d-flex align-items-center">
                        <div class="col-12">
                            <h2 class="color-nocturne-purple">R$<?= moedaReal($graphData['totals']['vendas'])?></h2>
                            <h3 class="color-stellar-blue"><?= $translator->translate("Faturamento") ?></h3>
                        </div>
                        <div class="col-12">
                            <h2 class="color-nocturne-purple"><?= $graphData['totals']['transacoes'] ?></h2>
                            <h3 class="color-stellar-blue"><?= $translator->translate("Total de Vendas") ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-12 text-end">
                    <canvas id="myChart" class="w-100 chartjs-render-monitor"></canvas>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-12 text-center d-flex align-items-center justify-content-xl-start">
                    <div class="rounded-circle bg-nocturne-purple d-flex align-items-center justify-content-center me-4" style="width: 80px; height: 80px; aspect-ratio: 1;">
                        <i class="fa-solid fa-dollar-sign fa-2x text-white"></i>
                    </div>
                    <div class="text-start">
                        <?= $translator->translate("Mais vendido por") ?> <span class="fs-5 color-stellar-blue"><?= $translator->translate("VALOR") ?></span><br>
                        <span class="fs-4 color-nocturne-purple">R$<?= moedaReal($bestSellers['valor_maior_venda']) ?></span><br>
                        <span class="fs-5 color-lavanda"><?= $bestSellers['produto_maior_valor'] ?></span>
                    </div>
                </div>
                <div class="col-xl-4 col-12 text-center d-flex align-items-center justify-content-xl-center">
                    <div class="rounded-circle bg-lavanda d-flex align-items-center justify-content-center me-4" style="width: 80px; height: 80px; aspect-ratio: 1;">
                        <i class="fa-solid fa-chart-line fa-2x text-white"></i>
                    </div>
                    <div class="text-start">
                        <span class="fs-5 color-stellar-blue"><?= $translator->translate("Ticket Médio") ?></span><br>
                        <span class="fs-4 color-nocturne-purple">R$<?= moedaReal($graphData['totals']['vendas']/($graphData['totals']['transacoes'] > 0 ? $graphData['totals']['transacoes'] : 1)) ?></span><br>
                    </div>
                </div>
                <div class="col-xl-4 col-12 text-center d-flex align-items-center justify-content-xl-end">
                    <div class="rounded-circle bg-stellar-blue d-flex align-items-center justify-content-center me-4" style="width: 80px; height: 80px; aspect-ratio: 1;">
                        <i class="fa-solid fa-box fa-2x text-white"></i>
                    </div>
                    <div class="text-start">
                        <?= $translator->translate("Mais vendido por") ?> <span class="fs-5 color-stellar-blue"><?= $translator->translate("QUANTIDADE") ?></span><br>
                        <span class="fs-4 color-nocturne-purple"><?= empty($bestSellers['quantidade_mais_vendida']) ? 0 : $bestSellers['quantidade_mais_vendida'] ?> <?= $translator->translate("Unidades") ?></span><br>
                        <span class="fs-5 color-lavanda"><?= $bestSellers['produto_mais_vendido'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="graficos-tab" data-bs-toggle="tab" href="#graficos" role="tab" aria-controls="graficos" aria-selected="true"><?= $translator->translate("Dashboard") ?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="produtos-tab" data-bs-toggle="tab" href="#produtos" role="tab" aria-controls="produtos" aria-selected="true"><?= $translator->translate("Produtos") ?></a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="graficos" role="tabpanel" aria-labelledby="graficos-tab">
                    <div class="row my-3">
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="color-nocturne-purple w-75" id="graph-label-1"></span>
                                    <div>
                                        <a 
                                            class="btn btn-outline-stellar-blue btn-md rounded-circle border-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGraphModal" 
                                            data-modal-label="<?= $translator->translate("Gráfico 01") ?>"
                                            data-graph-id="1"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <canvas id="grafico-01" class="w-100 chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded mt-3 p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="color-nocturne-purple w-75" id="graph-label-2"></span>
                                    <div>
                                        <a 
                                            class="btn btn-outline-stellar-blue btn-md rounded-circle border-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGraphModal" 
                                            data-modal-label="<?= $translator->translate("Gráfico 02") ?>"
                                            data-graph-id="2"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <canvas id="grafico-02" class="w-100 chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                        <div class="col-12">
                           <div class="border rounded mt-3 p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="color-nocturne-purple w-75" id="graph-label-3"></span>
                                    <div>
                                        <a 
                                            class="btn btn-outline-stellar-blue btn-md rounded-circle border-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGraphModal" 
                                            data-modal-label="<?= $translator->translate("Gráfico 03") ?>"
                                            data-graph-id="3"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <canvas id="grafico-03" class="w-100 chartjs-render-monitor"></canvas>
                           </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded mt-3 p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="color-nocturne-purple w-75" id="graph-label-4"></span>
                                    <div>
                                        <a 
                                            class="btn btn-outline-stellar-blue btn-md rounded-circle border-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGraphModal" 
                                            data-modal-label="<?= $translator->translate("Gráfico 04") ?>"
                                            data-graph-id="4"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <canvas id="grafico-04" class="w-100 chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded mt-3 p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="color-nocturne-purple w-75" id="graph-label-5"></span>
                                    <div>
                                        <a 
                                            class="btn btn-outline-stellar-blue btn-md rounded-circle border-0"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGraphModal" 
                                            data-modal-label="<?= $translator->translate("Gráfico 05") ?>"
                                            data-graph-id="5"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <canvas id="grafico-05" class="w-100 chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded mt-3 p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="color-nocturne-purple w-75" id="graph-label-6"></span>
                                    <div>
                                        <a 
                                            class="btn btn-outline-stellar-blue btn-md rounded-circle border-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGraphModal" 
                                            data-modal-label="<?= $translator->translate("Gráfico 06") ?>"
                                            data-graph-id="6"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="responsive-chart">
                                    <canvas id="grafico-06" class="w-100 chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="produtos" role="tabpanel" aria-labelledby="produtos-tab">
                    <div class="d-flex justify-content-end">
                        <!-- vertical align center -->
                        <div class="d-flex text-center align-items-center color-nocturne-purple me-2"><?= $translator->translate("Ordenar por:") ?></div>
                        <div class="toggle-container shadow-sm my-3" id="toggle-product-order" style="width:250px;">
                            <div class="slider left" id="slider-product-order"></div>
                            <div class="toggle-btn active text-dark" data-side="left" id="productsByRevenue"><?= $translator->translate("Faturamento") ?></div>
                            <div class="toggle-btn text-dark" data-side="right" id="productsByQuantity"><?= $translator->translate("Quantidade") ?></div>
                        </div>
                    </div>
                    <div class="table-responsive-lg">
                        <table class="table table-striped" id="productsTable">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" scope="col"><span class="color-nocturne-purple"><?= $translator->translate("Posição") ?></span></th>
                                    <th class="text-center align-middle" scope="col"><span class="color-nocturne-purple"><?= $translator->translate("Produto") ?></span></th>
                                    <th class="text-center align-middle" scope="col"><span class="color-nocturne-purple"><?= $translator->translate("Quantidade") ?></span></th>
                                    <th class="text-center align-middle" scope="col"><span class="color-nocturne-purple"><?= $translator->translate("Faturamento") ?></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productRanking as $key => $produto): ?>
                                <tr data-quantity="<?= $produto['total_vendido'] ?>" data-revenue="<?= $produto['total_faturado'] ?>" data-product="<?= $produto['produto_id'] ?>">
                                    <th class="text-center align-middle" scope="row" id="position-<?= $produto['produto_id']?>">
                                        <span class="color-stellar-blue">#<?= $key+1 ?></span>
                                    </th>
                                    <td class="text-center align-middle">
                                        <?= $produto['produto_nome'] ?><br>
                                        <small class="color-gray"><?= $produto['produto_codigo_interno'] ?></small>
                                    </td>
                                    <td class="text-center align-middle"><?= $produto['total_vendido'] ?></td>
                                    <td class="text-center align-middle">
                                        <span class="color-nocturne-purple">R$<?= moedaReal($produto['total_faturado']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">Profile content</div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">Contact content</div> -->
            </div>
        </div>
    </div>
</section>
<section id="section_modals">
    <div class="modal fade" id="editGraphModal" tabindex="-1" aria-labelledby="editGraphModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGraphModalLabel"></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-graph" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">

                        <div class="mb-3">
                            <label for="graph-type" class="form-label"><?= $translator->translate("Gráfico de:") ?></label>
                            <select name="graph-type" id="graph-type" class="form-control">
                                <option value="pie"><?= $translator->translate("Distribuição (Pizza)") ?></option>
                                <option value="bar"><?= $translator->translate("Total (Barras)") ?></option>
                                <option value="line"><?= $translator->translate("Crescimento (Linhas)") ?></option>
                            </select>
                            <!-- <small class="form-text text-muted">Tipo de gráfico a ser demonstrado</small> -->
                        </div>

                        <div class="mb-3">
                            <label for="graph-counter" class="form-label"><?= $translator->translate("De:") ?></label>
                            <select name="graph-counter" id="graph-counter" class="form-control">
                                <option value="sold_units"><?= $translator->translate("Unidades Vendidas") ?></option>
                                <option value="revenue"><?= $translator->translate("Faturamento") ?></option>
                                <option value="discount"><?= $translator->translate("Desconto Aplicado") ?></option>
                                <option value="refunds"><?= $translator->translate("Reembolsos (Vendas Canceladas)") ?></option>
                                <option value="average_value"><?= $translator->translate("Valor Médio") ?></option>
                                <option value="transactions"><?= $translator->translate("Transações") ?></option>
                                <option value="contribution_margin"><?= $translator->translate("Margem de Contribuição") ?></option>
                            </select>
                            <!-- <small class="form-text text-muted">Alvo do gráfico </small> -->
                        </div>

                        <div class="mb-3">
                            <label for="graph-target" class="form-label"><?= $translator->translate("Por:") ?></label>
                            <select name="graph-target" id="graph-target" class="form-control">
                                <option value="product"><?= $translator->translate("Produtos") ?></option>
                                <option value="category"><?= $translator->translate("Categoria") ?></option>
                                <option value="payment_method"><?= $translator->translate("Meio de Pagamento") ?></option>
                            </select>
                            <!-- <small class="form-text text-muted">Alvo do gráfico</small> -->
                        </div>

                        <div class="mb-3">
                            <label for="graph-filter" class="form-label"><?= $translator->translate("Filtrado por:") ?></label>
                            <select name="graph-filter" id="graph-filter" class="form-control mb-3">
                                <option value="all" id="all_option"><?= $translator->translate("Todos") ?></option>
                                <option value="top_10" id="top_10_option"><?= $translator->translate("Top 10") ?></option>
                                <option value="custom" id="custom_option"><?= $translator->translate("Lista personalizada") ?></option>
                            </select>
                        </div> 

                        <div class="mb-3" id="graph-products-container" style="display:none;">
                            <label for="graph-products" class="form-label"><?= $translator->translate("Lista:") ?></label>
                            <select class="form-select select2" id="graph-products" name="products[]" multiple="multiple" data-placeholder="<?= $translator->translate("Selecione um ou mais produtos") ?>" data-noResults="<?= $translator->translate("Nenhum produto encontrado") ?>">
                                <?php foreach ($allProducts as $product): ?>
                                    <option value="<?= $product['id'] ?>"><?= $product['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3" id="graph-category-container" style="display:none;">
                            <label for="graph-category" class="form-label"><?= $translator->translate("Lista:") ?></label>
                            <select class="form-select select2"  id="graph-category" name="category[]" multiple="multiple" data-placeholder="<?= $translator->translate("Selecione uma ou mais categorias") ?>" data-noResults="<?= $translator->translate("Nenhuma categoria encontrada") ?>">
                                <?php foreach ($allCategories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="hidden" name="graph-id" id="graph-id" value="">

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate("Fechar") ?></button>
                    <button type="submit" class="btn btn-stellar-blue" id="edit-graph-btn" form="edit-graph-form"><?= $translator->translate("Salvar") ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/vendors/chart.js/Chart.min.js") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const labels = <?= json_encode(array_values($graphData['labels'])) ?>;
    const totalTransacoes = <?= json_encode(array_values($graphData['values']['total_transacoes'])) ?>;
    const totalVendas = <?= json_encode(array_values($graphData['values']['total_vendas'])) ?>;
    const customGraphs = <?= json_encode($customGraphs) ?>;
    const dictionary = {
        error: "<?= $translator->translate("Erro ao editar o gráfico:") ?>"
    };
</script>
<script src="<?= url("assets/js/statistics/home.js") ?>"></script>
<?= $this->stop() ?>