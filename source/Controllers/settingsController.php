<?php

namespace Source\Controllers;
use Source\Core\Core;
use Source\Model\Settings;

class settingsController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->validaAcesso();
        $this->getLayout()->setHeader($this->getLogado() ? 'header-logado' : 'header');
        $this->getLayout()->setFooter('footer');
        $this->addLayout();
    }

    public function renderMenu($selected = '') {
        return $this->view->render("settings/menu", ['selected' => $selected]);
    }

    public function home() {
        echo $this->view->render("settings/home", [
            'layout' => [
                'title' =>  'Configurações - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
        ]);
        return;
    }

    public function profile() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);  
        $this->addLayout('Editar Perfil');

        echo $this->view->render("settings/profile", [
            'store' => $store,
            'menu' => $this->renderMenu('profile'),
        ]);
    }

    public function security() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);  
        $this->addLayout('Senha e Segurança');

        echo $this->view->render("settings/security", [
            'store' => $store,
            'menu' => $this->renderMenu('security'),
        ]);
    }

    public function partner() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);  
        $this->addLayout('Filiações');

        echo $this->view->render("settings/partner", [
            'store' => $store,
            'menu' => $this->renderMenu('partner'),
        ]);
    }

    public function store() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);  
        $this->addLayout('Editar Loja');

        echo $this->view->render("settings/store", [
            'store' => $store,
            'menu' => $this->renderMenu('store'),
        ]);
    }

    public function categories() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);  
        $this->addLayout('Categorias');

        echo $this->view->render("settings/categories", [
            'store' => $store,
            'menu' => $this->renderMenu('categories'),
        ]);
    }

    public function team() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);  
        $this->addLayout('Equipe');

        echo $this->view->render("settings/team", [
            'store' => $store,
            'menu' => $this->renderMenu('team'),
        ]);
    }

}