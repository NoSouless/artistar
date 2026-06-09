<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="<?= url("assets/css/settings/categories.css") ?>" rel="stylesheet" />
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <?= $menu ?>
        <div class="col-md-9 col-12 ps-md-5 p-3">
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <h2 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Categorias") ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table" style="margin-top: 20px;">
                            <thead>
                                <tr>
                                    <th width="5%" scope="col">&nbsp;</th>
                                    <th width="55%" scope="col">Categoria</th>
                                    <th width="5%" class="text-center" scope="col">Ativa</th>
                                    <th width="5%" class="text-center" scope="col">Pública</th>
                                    <th width="5%" class="text-center" scope="col">Produtos</th>
                                    <th width="25%" class="text-end" scope="col">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="categories-table-body">
                                <?php foreach($categories as $category): ?>
                                <tr class="category-row" data-category-id="<?= $category['id'] ?>">
                                    <td scope="row"><i class="fas fa-grip-lines category-handle"></i></td>
                                    <td><input type="text" class="form-control" name="category[<?= $category['id'] ?>][nome]" value="<?= htmlspecialchars($category['nome']) ?>"></td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="active" value="1" <?= $category['ativa'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="public" value="1" <?= $category['publica'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $category['total_produtos'] ?></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-category-btn" data-category-id="<?= $category['id'] ?>"><?= $translator->translate("Excluir") ?></button>
                                        <button type="button" class="btn btn-sm btn-outline-success save-category-btn" data-category-id="<?= $category['id'] ?>"><?= $translator->translate("Salvar") ?></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="unsortable">
                                    <td scope="row"></td>
                                    <td><input type="text" class="form-control"  value=""></td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="active" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="public" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">0</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-success" id="add-category-btn"><?= $translator->translate("Adicionar") ?></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>


<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="<?= url("assets/js/settings/categories.js") ?>"></script>
<?= $this->stop() ?>