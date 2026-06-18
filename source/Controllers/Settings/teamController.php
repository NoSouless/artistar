<?php

namespace Source\Controllers\Settings;
use Exception;
use Source\Controllers\settingsController;
use Source\Model\Helpers\Storage;
use Source\Model\Settings\Team;

class teamController extends settingsController {

    public function team() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Team();
        $store = $model->getStoreData($storeId);  
        $this->addLayout('Equipe');

        echo $this->view->render("settings/team", [
            'store' => $store,
            'menu' => $this->renderMenu('team'),
        ]);
    }

}