<?php

namespace Source\Controllers\Settings;
use Exception;
use Source\Controllers\settingsController;
use Source\Model\Helpers\Storage;
use Source\Model\Settings\Store;

class storeController extends settingsController {

    public function store() {
        $this->addTranslator('settings/store');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Store();
        $store = $model->getStoreData($storeId);  
        $this->addLayout('Editar Loja');

        echo $this->view->render("settings/store", [
            'store' => $store,
            'menu' => $this->renderMenu('store'),
        ]);
    }

    public function updateStore($post) {
        $this->addTranslator('settings/store');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Store();
        $store = $model->getStoreData($storeId);

        if (empty($storeId) || empty($store)) {
            exit($this->renderApiResponse(400, $this->getTranslator()->translate("Loja não encontrada.")));
        }

        $updateData = [
            'loja_nome' => trim((string) ($post['nome'] ?? '')),
            'loja_descricao' => trim((string) ($post['descricao'] ?? '')),
            'loja_pais' => $post['storeCountry'] ?? null,
            'loja_moeda' => $post['storeCurrency'] ?? null,
        ];

        try {
            if (!empty($_FILES['storePhoto']['tmp_name'])) {
                $storagePhoto = new Storage();
                $folder = 'uploads/stores/'.$storeId.'/';
                $imageName = $folder.'logo.'. pathinfo($_FILES['storePhoto']['name'] ?? '', PATHINFO_EXTENSION);
                // $storagePhoto->cleanFolderFromBucket($folder);
                $newLogo = $storagePhoto->sendFileToBucket($_FILES['storePhoto']['tmp_name'], $imageName, true)['message'];
                $updateData['loja_foto'] = $newLogo;
            }

            if (!empty($_FILES['storeBanner']['tmp_name'])) {
                $storageBanner = new Storage();
                $folder = 'uploads/stores/'.$storeId.'/';
                $imageName = $folder.'banner.'. pathinfo($_FILES['storeBanner']['name'] ?? '', PATHINFO_EXTENSION);
                // $storageBanner->cleanFolderFromBucket($folder);
                $newBanner = $storageBanner->sendFileToBucket($_FILES['storeBanner']['tmp_name'], $imageName, true)['message'];
                $updateData['loja_banner'] = $newBanner;
            }

            $model->updateStoreData($storeId, $updateData);
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $this->getTranslator()->translate("Erro ao atualizar a loja: ") . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, $this->getTranslator()->translate("Loja atualizada com sucesso.")));
    }

}