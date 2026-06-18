<?php

namespace Source\Controllers;
use Exception;
use Source\Core\Core;
use Source\Model\Helpers\Storage;
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

}