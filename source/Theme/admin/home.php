<?= $this->layout("base", $layout); ?>

<?= $this->start("css") ?>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height avoid-navbar">
  <div class="row g-4">

     <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/users") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-3 color-nocturne-purple"></i>
                    <h6 class="card-title">Usuários</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/stores") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-store fa-2x mb-3 color-stellar-blue"></i>
                    <h6 class="card-title">Lojas</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/products") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-box fa-2x mb-3 color-lavanda"></i>
                    <h6 class="card-title">Produtos</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/sales") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-2x mb-3 color-cotton-candy"></i>
                    <h6 class="card-title">Vendas</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/events") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-tags fa-2x mb-3 color-nocturne-purple"></i>
                    <h6 class="card-title">Eventos</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/subscriptions") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-th-list fa-2x mb-3 color-stellar-blue"></i>
                    <h6 class="card-title">Inscrições</h6>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <a href="<?= url("admin/tokens") ?>" class="text-decoration-none">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <i class="fas fa-key fa-2x mb-3 color-nocturne-purple"></i>
                    <h6 class="card-title">Tokens</h6>
                </div>
            </div>
        </a>
    </div>

  </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<?= $this->stop() ?>