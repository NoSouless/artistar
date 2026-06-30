<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= url("assets/css/stock/productDetails.css") ?>">
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<form id="product-details-form" class="container pt-3 minimum-height" data-success="<?= $translator->translate("Alteração Salva!") ?>" data-error="<?= $translator->translate("Erro ao Salvar!") ?>">
    <div class="row avoid-navbar">
        <div class="col-sm-6 col-12 mb-3 mb-sm-0 px-sm-0">
            <div>
                <input type="text" value="<?= $product['nome'] ?>" class="form-control input-stellar-blue" id="name" name="name" placeholder="<?= $translator->translate("Digite o nome do produto") ?>" data-empty="<?= $translator->translate("Por favor, preencha os campo de nome") ?>">
                <small id="nameHelp" class="form-text text-muted d-flex justify-content-between">
                    <span><?= $translator->translate("Nome oficial do produto") ?></span>
                    <span><span id="nameCount">0</span>/50</span>
                </small>
            </div>
        </div>
        <div class="col-sm-6 col-12 px-sm-0">
            <div class="d-flex justify-content-sm-end justify-content-between">
                <button type="button" class="btn btn-nocturne-purple dropdown-toggle mx-sm-3" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#" id="duplicate-product" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                            <i class="fa fa-clone me-1" aria-hidden="true"></i> <?= $translator->translate("Duplicar") ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="delete-product" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fa fa-trash me-1" aria-hidden="true"></i> <?= $translator->translate("Excluir") ?>
                        </a>
                    </li>
                </ul>
                <button type="submit" class="btn btn-stellar-blue" id="create-product-btn" form="product-details-form"><?= $translator->translate("Salvar Alterações") ?></button>
            </div>
        </div>
    </div>
    <div class="row px-sm-0 p-3">
        <div class="col-lg-4 border rounded col-12 mb-3">
            <div class="row p-3">
                <div class="col-12">
                    <div class="mb-3">
                        <div id="image-drop-area" class="image-drop-area d-flex align-items-center justify-content-center">
                            <?php if ($product['thumbnail']): ?>
                                <img id="image-preview" src="<?= storageURL($product['thumbnail']) ?>" alt="Preview" style="max-width:100%;max-height:100%;border-radius:12px;">
                            <?php else: ?>
                                <span id="image-drop-text"><?= $translator->translate("Clique ou arraste uma imagem aqui") ?></span>
                            <?php endif; ?>
                        </div>
                        <small id="new-nameHelp" class="form-text text-muted">
                            <?= $translator->translate("Tamanho máximo: 5MB") ?>
                        </small>
                        <input type="file" id="image" name="thumbnail" accept="image/*" style="display:none;" data-sizeError="<?= $translator->translate("A imagem deve ter no máximo 5MB") ?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="insideId" class="form-label"><?= $translator->translate("Identificação Interna") ?></label>
                        <input type="text" value="<?= $product['identificacao_interno']?>" class="form-control input-stellar-blue" id="insideId" name="insideId" placeholder="<?= $translator->translate("Digite a identificação interna") ?>">
                        <small id="insideIdHelp" class="form-text text-muted d-flex justify-content-between">
                            <span><?= $translator->translate("Um nome não oficial do produto") ?></span>
                            <span><span id="insideIdCount">0</span>/50</span>
                        </small>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label"><?= $translator->translate("Descrição") ?></label>
                        <textarea class="form-control input-stellar-blue" id="description" name="description" rows="6" placeholder="<?= $translator->translate("Digite a descrição do produto") ?>"><?= $product['descricao'] ?></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3 form-check form-switch form-switch-sm">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="active" value="1" <?= $product['ativo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="flexSwitchCheckDefault"><?= $translator->translate("Produto Ativo") ?></label>
                        <i class="fa-solid fa-circle-info color-gray ms-2" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate("Produtos inativos não aparecerão na hora da venda.") ?>"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12 mb-lg-3">
            <div class="row ps-lg-3">
                <div class="col-12 border rounded p-3 mb-3">
                    <div class="row">
                        <div class="mb-3 col-md-4 col-sm-6 col-12">
                            <label for="price" class="form-label"><?= $translator->translate("Preço") ?></label>
                            <input type="text" class="form-control moedaReal" id="price" name="price" value="<?= moedaReal($product['valor']) ?>">
                        </div>
                        <div class="mb-3 col-md-4 col-sm-6 col-12">
                            <label for="discount" class="form-label"><?= $translator->translate("Desconto") ?> (R$)</label>
                            <input type="text" class="form-control moedaReal" id="discount" name="discount" value="<?= moedaReal($product['valor_desconto']) ?>">
                        </div>
                        <div class="mb-3 col-md-4 col-sm-6 col-12">
                        <label for="cost" class="form-label"><?= $translator->translate("Custo") ?></label>
                            <input type="text" class="form-control moedaReal" id="cost" name="cost" value="<?= moedaReal($product['custo']) ?>">
                        </div>
                        <div class="mb-3 col-md-4 col-sm-6 col-12">
                            <label for="real_price" class="form-label"><?= $translator->translate("Preço Atual") ?></label>
                            <input type="text" disabled class="form-control moedaReal" id="real_price" name="real_price" value="<?= moedaReal($product['valor'] - $product['valor_desconto']) ?>">
                        </div>
                        <div class="mb-3 col-md-4 col-sm-6 col-12">
                            <label for="discount_percentage" class="form-label"><?= $translator->translate("Desconto") ?> (%)</label>
                            <input type="text" disabled class="form-control" id="discount_percentage" name="discount_percentage" value="<?= moedaReal($product['valor'] > 0 ? (($product['valor_desconto'] * 100) / $product['valor']) : 0.00) ?>">
                        </div>
                        <div class="mb-3 col-md-4 col-sm-6 col-12">
                            <label for="margin" class="form-label"><?= $translator->translate("Margem") ?> <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate("A margem de contribuição é calculada subtraindo o custo e o desconto do preço de venda, ajuda a identificar a lucratividade do produto.") ?>"></i></label>
                            <input type="text" disabled class="form-control moedaReal" id="margin" name="margin" value="<?= moedaReal(($product['valor'] - $product['valor_desconto']) - $product['custo']) ?>">
                        </div>
                    </div>
                </div>
                <div id="category-box" class="col-12 border rounded p-3 mb-3">
                    <div class="mb-3">
                        <label for="category" class="form-label"><?= $translator->translate("Categorias") ?></label>
                        <select class="form-select select2" id="category" name="category[]" multiple="multiple" data-placeholder="<?= $translator->translate("Selecione ou adicione uma nova categoria") ?>" data-noResults="<?= $translator->translate("Adicione uma nova categoria") ?>">
                            <?php 
                            $selectedCategories = explode(',', $product['categoriasIds'] ?? '');
                            foreach ($categories as $category): ?>
                                <option value="<?= in_array($category['id'], $selectedCategories) ? '{selected}' : ''?>{existing}<?= $category['id'] ?>" <?= in_array($category['id'], $selectedCategories) ? 'selected' : '' ?>><?= $category['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 border rounded p-3 mb-3">
                    <div class="mb-3">
                        <label for="keywords" class="form-label"><?= $translator->translate("Palavras-Chave") ?> <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate("Palavras-Chave ajudam na busca de produtos, sem serem parte do nome oficial.") ?>"></i></label>
                        <select class="form-select select2" id="keywords" name="keywords[]" multiple="multiple" data-placeholder="<?= $translator->translate("Selecione ou adicione uma nova palavra-chave") ?>" data-noResults="<?= $translator->translate("Adicione uma nova palavra-chave") ?>">
                            <?php 
                            $keywords = explode('|', $product['palavras_chave'] ?? '');
                            if (empty($keywords[0])) $keywords = [];
                            foreach ($keywords as $keyword): ?>
                                <option selected value="<?= $keyword ?>" selected><?= $keyword ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 border rounded p-3 mb-3">
                    <div class="row">
                        <div class="mb-3 col-sm-6 col-12">
                            <label for="stock" class="form-label"><?= $translator->translate("Estoque") ?></label>
                            <input type="number" class="form-control" id="stock" name="stock" value="<?= $product['estoque'] ?>">
                        </div>
                        <div class="mb-3 col-sm-6 col-12">
                            <label for="min_stock" class="form-label"><?= $translator->translate("Estoque Mínimo") ?> <i class="fa-solid fa-circle-info color-gray ms-1" data-toggle="tooltip" data-placement="top" data-bs-custom-class="cor-tooltip" title="<?= $translator->translate("Estoque mínimo é a quantidade mínima que você deseja manter em estoque para evitar falta de produtos. Quando o produto está na vitrine da loja, ele será exibido com um aviso de estoque baixo.") ?>"></i></label>
                            <input type="number" min="0" class="form-control" id="min_stock" name="min_stock" value="<?= $product['estoque_minimo'] ?>">
                        </div>
                    </div>
                </div>
                <div class="col-12 px-0 mb-3">
                    <div class="d-flex justify-content-sm-end justify-content-between">
                        <a class="btn btn-cotton-candy mx-sm-3" id="discard-changes-btn" href="<?=url('stock/product/'.$product['id'])?>"><?= $translator->translate("Descartar Alterações") ?></a>
                        <button type="submit" class="btn btn-stellar-blue" id="create-product-btn-2" form="product-details-form"><?= $translator->translate("Salvar Alterações") ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<section class="modal-form">
    <div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateModalLabel"><?= $translator->translate("Duplicar") ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= $translator->translate("Você tem certeza que deseja duplicar o produto") ?> <strong>"<?= $product['nome'] ?>"</strong> <?= $translator->translate("com o nome") ?> <strong>"<?= $translator->translate("Cópia") ?> - <?= $product['nome'] ?>"</strong>?<br>
                    <?= $translator->translate("Esta ação criará uma cópia do produto com todas as informações, exceto o ID do produto, que será novo.") ?>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate("Cancelar") ?></button>
                    <button type="button" class="btn btn-stellar-blue" id="accept-duplicate"><?= $translator->translate("Duplicar!") ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"><?= $translator->translate("Excluir") ?></h5>
                    <button type="button" class="btn-close input-stellar-blue" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= $translator->translate("Você tem certeza que deseja excluir o produto") ?> <strong>"<?= $product['nome'] ?>"</strong>?<br>
                    <?= $translator->translate("Esta ação não poderá ser desfeita.") ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-fog-gray" data-bs-dismiss="modal"><?= $translator->translate("Cancelar") ?></button>
                    <button type="button" class="btn btn-nocturne-purple" id="accept-delete"><?= $translator->translate("Excluir!") ?></button>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
    const productId = <?= $product['id'] ?>;
    const dictionary = {
        error: "<?= $translator->translate("Ocorreu um erro") ?>",
        saveProductError: "<?= $translator->translate("Ocorreu um erro ao salvar as alterações do produto.") ?>",
        duplicateProductError: {
            title: "<?= $translator->translate("Erro ao Duplicar!") ?>",
            body: "<?= $translator->translate("Ocorreu um erro ao duplicar o produto.") ?>"
        },
        deleteProduct: {
            success: "<?= $translator->translate("Produto excluído com sucesso!") ?>",
            error: {
                title: "<?= $translator->translate("Erro ao Excluir!") ?>",
                body: "<?= $translator->translate("Ocorreu um erro ao excluir o produto.") ?>"
            }
        }
    };
</script>
<script src="<?= url("assets/js/stock/productDetails.js") ?>"></script>
<?= $this->stop() ?>