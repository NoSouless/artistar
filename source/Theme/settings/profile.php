<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <?= $menu ?>
        <div class="col-9 ps-5 p-3">
            <div class="row">
                <div class="col-sm-6 col-12 mb-3 mb-sm-0 px-sm-0">
                    <div>
                        <h2 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Editar Perfil") ?></h2>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<?= $this->stop() ?>