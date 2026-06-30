<div class="d-flex flex-column flex-shrink-0 p-3 col-md-3 col-12">
    <div class="d-flex justify-content-center align-items-center text-center mb-3 mb-md-0 w-100">
        <span class="fs-4 color-nocturne-purple"><?= $translator->translate("Configurações") ?></span>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="mt-2 mb-1 px-3">
            <small class="text-uppercase fw-semibold color-stellar-blue"><?= $translator->translate("Meu Usuário") ?></small>
        </li>
        <li>
            <a href="<?= url("settings/profile") ?>" class="nav-link link-graphite-gray <?= $selected === 'profile' ? 'bg-stellar-blue link-light' : '' ?>">
                <i class="fa fa-user bi me-4" style="width:24px; text-align: center;"></i>
                <?= $translator->translate("Editar Perfil") ?>
            </a>
        </li>
        <!-- <li>
            <a href="<?= url("settings/security") ?>" class="nav-link link-graphite-gray <?= $selected === 'security' ? 'bg-stellar-blue link-light' : '' ?>">
                <i class="fa fa-lock bi me-4" style="width:24px; text-align: center;"></i>
                <?= $translator->translate("Senha e Segurança") ?>
            </a>
        </li> -->
        <!-- <li>
            <a href="<?= url("settings/partner") ?>" class="nav-link link-graphite-gray <?= $selected === 'partner' ? 'bg-stellar-blue link-light' : '' ?>">
                <i class="fa fa-handshake bi me-4" style="width:24px; text-align: center;"></i>
                <?= $translator->translate("Parcerias") ?>
            </a>
        </li> -->
        <li class="mt-2 mb-2 px-3">
            <small class="text-uppercase fw-semibold color-stellar-blue"><?= $translator->translate("Minha Loja") ?></small>
        </li>
        <li>
            <a href="<?= url("settings/store") ?>" class="nav-link link-graphite-gray <?= $selected === 'store' ? 'bg-stellar-blue link-light' : '' ?>" aria-current="page">
                <i class="fa fa-edit bi me-4" style="width:24px; text-align: center;"></i>
                <?= $translator->translate("Editar Loja") ?>
            </a>
        </li>
        <li>
            <a href="<?= url("settings/categories") ?>" class="nav-link link-graphite-gray <?= $selected === 'categories' ? 'bg-stellar-blue link-light' : '' ?>">
                <i class="fa fa-list bi me-4" style="width:24px; text-align: center;"></i>
                <?= $translator->translate("Categorias") ?>
            </a>
        </li>
        <!-- <li>
            <a href="<?= url("settings/team") ?>" class="nav-link link-graphite-gray <?= $selected === 'team' ? 'bg-stellar-blue link-light' : '' ?>">
                <i class="fa fa-users bi me-4" style="width:24px; text-align: center;"></i>
                <?= $translator->translate("Equipe") ?>
            </a>
        </li> -->
    </ul>
</div>