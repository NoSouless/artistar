<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\Legal;

class legalController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->getLayout()->setHeader($this->getLogado() ? 'header-logado' : 'header');
        $this->getLayout()->setFooter('footer');
    }

    public function terms() {
        $this->addTranslator('legal/terms');
        $this->addLayout($this->getTranslator()->translate("Termos de Uso"));

        echo $this->view->render("legal/{$this->getTranslator()->getLang()}/terms", [

        ]);
        return;
    }

    public function privacy() {
        $this->addTranslator('legal/privacy');
        $this->addLayout($this->getTranslator()->translate("Política de Privacidade"));

        echo $this->view->render("legal/{$this->getTranslator()->getLang()}/privacy", [
        ]);
        return;
    }

}
