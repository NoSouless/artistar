<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="<?= url("assets/css/settings/categories/home.css") ?>" rel="stylesheet" />
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
                                    <th width="55%" scope="col"><?= $translator->translate("Categoria") ?></th>
                                    <th width="5%" class="text-center" scope="col"><?= $translator->translate("Ativa") ?></th>
                                    <th width="5%" class="text-center" scope="col"><?= $translator->translate("Pública") ?></th>
                                    <th width="5%" class="text-center" scope="col"><?= $translator->translate("Produtos") ?></th>
                                    <th width="25%" class="text-end" scope="col">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="categories-table-body">
                                <tr class="unsortable" id="new-category-row">
                                    <td scope="row"></td>
                                    <td><input type="text" class="form-control" id="new-category-name" value=""></td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckActive" name="active" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckPublic" name="public" value="1">
                                        </div>
                                    </td>
                                    <td class="text-end" colspan="2">
                                        <button type="button" class="btn btn-sm btn-lavanda" id="add-category-btn"><?= $translator->translate("Adicionar") ?></button>
                                    </td>
                                </tr>
                                <?php foreach($categories as $category): ?>
                                <tr class="category-row" data-category-id="<?= $category['id'] ?>">
                                    <td scope="row"><i class="fas fa-grip-lines category-handle"></i></td>
                                    <td><input type="text" id="category-name-<?= $category['id'] ?>" class="form-control" data-info="name" name="category[<?= $category['id'] ?>][name]" value="<?= htmlspecialchars($category['nome']) ?>"></td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="category-active-<?= $category['id'] ?>" role="switch" name="active" value="1" <?= $category['ativa'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                    <td class="text-center">                  
                                        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="category-public-<?= $category['id'] ?>" role="switch" name="public" value="1" <?= $category['publica'] ? 'checked' : '' ?>>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $category['total_produtos'] ?></td>
                                    <td class="text-end">
                                        <a href="<?= url("settings/categories/category/{$category['id']}") ?>" class="btn btn-sm btn-gray edit-category-btn"><?= $translator->translate("Editar") ?></a>
                                        <button type="button" class="btn btn-sm btn-stellar-blue save-category-btn" data-category-id="<?= $category['id'] ?>"><?= $translator->translate("Salvar") ?></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
<script>
    const translator = {
        success: "<?= $translator->translate("Sucesso") ?>",
        error: "<?= $translator->translate("Erro") ?>"
    };
</script>
<script src="<?= url("assets/js/settings/categories/home.js") ?>"></script>
<?= $this->stop() ?>