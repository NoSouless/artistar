<?php

namespace Source\Controllers\Settings;
use Exception;
use Source\Controllers\settingsController;
use Source\Model\Helpers\Storage;
use Source\Model\Settings\Partner;

class partnerController extends settingsController {

    public function partner() {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Partner();
        $store = $model->getStoreData($storeId);
        $this->addLayout('Filiações');

        echo $this->view->render("settings/partner", [
            'store' => $store,
            'menu' => $this->renderMenu('partner'),
        ]);
    }

}