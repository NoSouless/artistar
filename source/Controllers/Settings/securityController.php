<?php

namespace Source\Controllers\Settings;
use Exception;
use Source\Controllers\settingsController;
use Source\Model\Helpers\Storage;
use Source\Model\Settings\Security;

class securityController extends settingsController {

    public function security() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Security();
        $store = $model->getStoreData($storeId);  
        $this->addLayout('Senha e Segurança');

        echo $this->view->render("settings/security", [
            'store' => $store,
            'menu' => $this->renderMenu('security'),
        ]);
    }

}