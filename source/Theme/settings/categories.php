<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="<?= url("assets/css/settings/categories.css") ?>" rel="stylesheet" />
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>

<?php 
function buildCategoryTree($category, $translator = null, $firstLevel = true) {
    global $categories;
    ob_start();

 ?>
<div class="row category-row border rounded p-3 mb-3" data-category-id="<?= $category['id'] ?>">
    <div class="col-1"><i class="fas fa-grip-lines category-handle"></i></div>
    <div class="col-4"><input type="text" class="form-control" name="category[<?= $category['id'] ?>][nome]" value="<?= htmlspecialchars($category['nome']) ?>"></div>
    <div class="col-1 text-center">                  
        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="active" value="1" <?= $category['ativa'] ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="col-1 text-center">                  
        <div class="mb-3 form-check form-switch form-switch-sm d-flex justify-content-center">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="public" value="1" <?= $category['publica'] ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="col-2 text-center"><?= $category['total_produtos'] ?></div>
    <div class="col-3 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger delete-category-btn" data-category-id="<?= $category['id'] ?>"><?= $translator->translate("Excluir") ?></button>
    </div>
    <?php if ((isset($categories[$category['id']]) || $category['total_produtos'] == 0) && $firstLevel): ?>
    <div class="col-12 category-children mt-3" data-accept-children="1">
        <?php
            if (isset($categories[$category['id']])) {
                foreach ($categories[$category['id']] as $child) {
                    buildCategoryTree($child, $translator, false);
                }
            }
        ?>
    </div>
    <?php endif; ?>
</div>
    <?php 
    $html = ob_get_clean();
    echo $html;



}

?>


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
                    <div class="row p-3">
                        <div class="col-1">&nbsp;</div>
                        <div class="col-4"><strong><?= $translator->translate("Categoria") ?></strong></div>
                        <div class="col-1 text-center"><strong><?= $translator->translate("Ativa") ?></strong></div>
                        <div class="col-1 text-center"><strong><?= $translator->translate("Pública") ?></strong></div>
                        <div class="col-2 text-center"><strong><?= $translator->translate("Produtos") ?></strong></div>
                        <div class="col-3 text-end"><a href="#" class="btn btn-sm btn-outline-primary"><?= $translator->translate("Adicionar Categoria") ?></a></div>
                    </div>
                    <div class="container-categories">
                        <div id="categories-tree" class="categories-sortable">
                            <?php foreach($categories[0] as $category): ?>
                                <?php buildCategoryTree($category, $translator); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-gray" id="discard-categories-btn"><?= $translator->translate("Descartar Alterações") ?></button>
                    <button type="button" class="btn btn-nocturne-purple ms-2" id="save-categories-btn"><?= $translator->translate("Salvar Alterações") ?></button>
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