<header class="navbar navbar-expand-lg navbar bg-white mb-4 fixed-top border-bottom py-3" id="navbar">
    <div class="container-fluid px-3 bg-white">
        <div class="d-flex">
            <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                <img src="<?= url("assets/image/logo.png") ?>" alt="mdo" class="bi me-2 logo-artistar" width="100">
            </a>

            <ul class="nav col-12 me-md-auto mb-2 mb-md-0 d-none d-md-flex">
                <li>
                    <a href="<?= url("sales") ?>" class="btn btn-nocturne-purple mx-2">
                        <i class="fa-solid fa-plus bi" style="width:24px; text-align: center;"></i> <?= $translator->translate('Nova Venda') ?>
                    </a>
                </li>
                <li><a href="<?= url("stock") ?>" class="nav-link px-2 link-nocturne-purple link-hover"><?= $translator->translate('Inventário') ?></a></li>
            </ul>
        </div>
        <div id="barra-direita" class="d-flex">
            <!-- <form class="col-12 col-md-auto me-md-3 d-none d-md-flex" method="GET" action="<?= url('events') ?>">
                <input name="search" type="search" class="form-control pesquisa-superior input-kiklit-2" placeholder="Pesquisar..." aria-label="Search" value="<?= $search ?>">
            </form> -->
            <!-- <div class="dropdown">
                <button  style="border:none; background:none;" type="button" class="link-nocturne-purple link-hover" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-bell" style="width:24px; text-align: center;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="px-2"><i class="fa-solid fa-bell" style="width:24px; text-align: center;"></i>Notificações</h6></li>
                    <li><div class="dropdown-divider"></div></li>
                    <div style="max-height: 300px; overflow-y: scroll;">
                        <li><h6 class="px-2">Importantes</h6></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start" href="#">
                                <img src="/assets/image/logo.svg" alt="Notification Image" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-1">Novo Pedido Recebido</h6>
                                    <p class="mb-0 text-muted">Você recebeu um novo pedido de João Silva.</p>
                                    <small class="text-muted">12/04/2025 14:30</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start" href="#">
                                <img src="/assets/image/logo.svg" alt="Notification Image" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-1">Novo Pedido Recebido</h6>
                                    <p class="mb-0 text-muted">Você recebeu um novo pedido de João Silva.</p>
                                    <small class="text-muted">12/04/2025 14:30</small>
                                </div>
                            </a>
                        </li>
                        <li><div class="dropdown-divider"></div></li>
                        <li><h6 class="px-2">Outras notificações</h6></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start" href="#">
                                <img src="/assets/image/logo.svg" alt="Notification Image" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-1">Novo Pedido Recebido</h6>
                                    <p class="mb-0 text-muted">Você recebeu um novo pedido de João Silva.</p>
                                    <small class="text-muted">12/04/2025 14:30</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start" href="#">
                                <img src="/assets/image/logo.svg" alt="Notification Image" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-1">Novo Pedido Recebido</h6>
                                    <p class="mb-0 text-muted">Você recebeu um novo pedido de João Silva.</p>
                                    <small class="text-muted">12/04/2025 14:30</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-start" href="#">
                                <img src="/assets/image/logo.svg" alt="Notification Image" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-1">Novo Pedido Recebido</h6>
                                    <p class="mb-0 text-muted">Você recebeu um novo pedido de João Silva.</p>
                                    <small class="text-muted">12/04/2025 14:30</small>
                                </div>
                            </a>
                        </li>
                    </div>
                </ul>
            </div> -->

            <?php if (true): ?>
            <div class="dropdown language-selector me-2">
                <button class="btn language-selector-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Selecionar idioma">
                    <img src="<?= url('assets/image/flags/' . $languageOptions[$activeLanguage]['flag']) ?>" alt="" class="language-flag me-2">
                    <span class="d-none d-sm-inline me-2"><?= $languageOptions[$activeLanguage]['label'] ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end language-selector-menu shadow-sm">
                    <?php foreach ($languageOptions as $langCode => $langData): 
                        $newUrl = $urlWithoutLang .'&lang=' . $langCode;
                    ?>
                    <li>
                        <a class="dropdown-item d-flex align-items-center<?= $activeLanguage === $langCode ? ' active' : '' ?>" href="<?= htmlspecialchars($newUrl, ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?= url('assets/image/flags/' . $langData['flag']) ?>" alt="" class="language-flag me-2">
                            <span><?= $langData['label'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <button data-bs-toggle="offcanvas" style="border:none; background:none;" type="button" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" class="link-nocturne-purple link-hover">
                <i class="fa-solid fa-bars" style="width:24px; text-align: center;"></i>
            </button>
        </div>
    </div>
</header>

<nav class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header px-4 d-flex justify-content-between align-items-center">
        <a href="/" class="d-flex align-items-center me-md-auto link-dark text-decoration-none">
            <img src="<?= url("assets/image/logo.png") ?>" alt="mdo" class="bi me-2 logo-artistar" width="100">
        </a>
        <button type="button" class="btn-close text-reset input-stellar-blue" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        
    </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between">
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- <li class="d-md-none">
                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                    <input type="search" class="form-control" placeholder="Pesquisar..." aria-label="Search">
                </form>
            </li> -->
            <li class="d-md-none">
                <a href="<?= url("sales") ?>" class="nav-link btn btn-nocturne-purple ">
                    <div class="d-flex align-items-center">
                        <span><?= $translator->translate('Nova Venda') ?></span>
                    </div>
                </a>
            </li>
            <li class="d-md-none">
                <a href="<?= url('stock')?>" class="nav-link link-nocturne-purple">
                    <div class="d-flex align-items-center">
                        <span><?= $translator->translate('Inventário') ?></span>
                    </div>
                </a>
            </li>
            <li class="d-md-none border-top my-3"></li>
            <li>
                <a href="<?= url('sales-statement') ?>" class="nav-link link-nocturne-purple link-hover">
                    <div class="d-flex align-items-center">
                        <i class="icone-sidebar icone-extrato bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Extrato de Vendas') ?></span>
                    </div>
                </a>
            </li>
            <li>
               <a href="<?= url('statistics') ?>" class="nav-link link-nocturne-purple link-hover">
                    <div class="d-flex align-items-center">
                        <i class="icone-sidebar icone-estatisticas bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Estatísticas') ?></span>
                    </div>
                </a>
            </li>
            
            <li class="border-top my-3"></li>
            <li>
               <a href="<?= url('events/my-events') ?>" class="nav-link link-nocturne-purple link-hover">
                    <div class="d-flex align-items-center">
                        <i class="icone-sidebar icone-meus-eventos me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Meus Eventos') ?></span>
                    </div>
                </a>
            </li>
            <!-- <li class="border-top my-3"></li>  -->
            <li>
                <a href="<?= url('store/id/' . $_SESSION['artistar']['user']['loja_id']) ?>" class="nav-link link-nocturne-purple link-hover">
                    <div class="d-flex align-items-center">
                        <i class="icone-sidebar icone-loja me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Minha Loja') ?></span>
                    </div>
                </a>
            </li>
            <?php if($_SESSION['artistar']['permissions']['prototype']): ?>
            <li>
               <a href="<?= url('events') ?>" class="nav-link link-nocturne-purple link-hover">
                    <div class="d-flex align-items-center">
                        <i class="icone-sidebar icone-procurar-eventos me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Procurar Eventos') ?></span>
                    </div>
                </a>
            </li>
            <li class="border-top my-3"></li>

            <li>
                <a href="#" class="nav-link link-dark link-hover">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-store bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Loja') ?></span>
                    </div>
                </a>
            </li>
            <li>
                <a href="#" class="nav-link link-dark link-hover">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-people-group bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Equipe') ?></span>
                    </div>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="nav-link link-dark link-hover item-parcerias" data-bs-toggle="collapse" data-bs-target="#orders-collapse" aria-expanded="false">
                    <div class="d-flex align-items-center">    
                        <i class="fa-solid fa-handshake-angle bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Parcerias') ?> <i class="fa-solid fa-chevron-down ms-2"></i></span>
                        
                    </div>
                </a>
                <div class="collapse ms-4" id="orders-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li>
                            <a href="#" class="link-dark link-hover text-decoration-none">
                                Loja 1
                            </a>
                        </li>
                        <li><a href="#" class="link-dark link-hover text-decoration-none">Loja 2</a></li>
                        <li><a href="#" class="link-dark link-hover text-decoration-none">Loja 3</a></li>
                        <li class="border-top my-3"></li> 
                        <li><a href="#" class="link-dark link-hover text-decoration-none">Gerenciar Parcerias</a></li>
                    </ul>
                </div>
            </li>
            <li class="border-top my-3"></li>  
            <li>
                <a href="<?= url('settings') ?>" class="nav-link link-dark link-hover">
                    <div class="d-flex align-items-center">
                        <i class="icone-sidebar icone-config bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Configurações') ?></span>
                    </div>
                </a>
            </li>
            <?php endif; ?>

            <?php if($_SESSION['artistar']['permissions']['admin']): ?>
            <li class="border-top my-3"></li>
            <li>
               <a href="<?= url('admin') ?>" class="nav-link link-nocturne-purple link-hover">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-user-tie bi me-4" style="width:24px; text-align: center;"></i>
                        <span><?= $translator->translate('Painel de Administrador') ?></span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <a class="d-flex justify-content-between align-items-center link-stellar-blue link-hover text-decoration-none" href="https://discord.gg/Qnk27RKWhs" target="_blank" rel="noopener noreferrer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center px-2">
                    <i class="fa-brands fa-discord bi me-4" style="width:24px; text-align: center;"></i>
                    <span><strong><?= $translator->translate('Discord Oficial!') ?></strong></span>
                </div>
            </div>
        </a>
        <div class="border-top my-3"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div href="#" class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center px-2">
                    <img src="<?= $_SESSION['artistar']['user']['foto_perfil'] ?>" class="rounded-circle me-4 store-logo">
                    <span><strong><?= $_SESSION['artistar']['user']['nome_completo'] ?></strong></span>
                </div>
            </div>
            <button style="border:none; background:none;" onclick="window.location.href='/auth/logout';" class="nav-link link-hover"><i class="icone-sidebar icone-sair px-2"></i></button>
        </div>
    </div>
</nav>