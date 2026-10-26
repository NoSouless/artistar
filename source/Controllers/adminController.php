<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\Admin;

class adminController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->validaAcesso();
        if ($this->getPermissions()['admin'] == false) header("Location: /");
        $this->getLayout()->setHeader($this->getLogado() ? 'header-logado' : 'header');
        $this->getLayout()->setFooter('footer');
        $this->addLayout();
    }

    public function home() {
        echo $this->view->render("admin/home", [
            'layout' => [
                'title' =>  'Painel de Administrador - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
        ]);
    }

    public function users() {
        $admin = new Admin();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/users", [
            'layout' => [
                'title' =>  'Usuários (Painel de Administrador) - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'users' => $admin->getUsers($limit, $offset),
            'total' => $admin->getUserCount(),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function stores() {
        $admin = new Admin();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/stores", [
            'layout' => [
                'title' =>  'Lojas (Painel de Administrador) - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'stores' => $admin->getStores($limit, $offset),
            'total' => $admin->getStoreCount(),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function products() {
        $admin = new Admin();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/products", [
            'layout' => [
                'title' =>  'Produtos (Painel de Administrador) - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'products' => $admin->getProducts($limit, $offset),
            'total' => $admin->getProductCount(),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function sales() {
        $admin = new Admin();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/sales", [
            'layout' => [
                'title' =>  'Vendas (Painel de Administrador) - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'sales' => $admin->getSales($limit, $offset),
            'total' => $admin->getSalesCount(),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function graphs() {
        // Implement graphs management view
    }

    public function events() {
        $admin = new Admin();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/events", [
            'layout' => [
                'title' =>  'Eventos (Painel de Administrador) - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'events' => $admin->getEvents($limit, $offset),
            'total' => $admin->getEventCount(),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function tokens() {
        $admin = new Admin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/tokens", [
            'layout' => [
                'title' =>  'Tokens (Painel de Administrador) - Artistar',
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'tokens' => $admin->getTokens($limit, $offset),
            'total' => $admin->getTokenCount(),
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function subscriptions() {
        $admin = new Admin();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        echo $this->view->render("admin/subscriptions", [
            'layout' => [
                'title' =>  'Inscrições (Painel de Administrador) - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'subscriptions' => $admin->getSubscriptions($limit, $offset),
            'total' => $admin->getSubscriptionCount(),
            'page' => $page,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

}