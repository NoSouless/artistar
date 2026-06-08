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

    public function updateStore($post) {
        $this->addTranslator('store/edit');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $storeModel = new Settings();
        $store = $storeModel->getStoreData($storeId);

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

            $storeModel->updateStoreData($storeId, $updateData);
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $this->getTranslator()->translate("Erro ao atualizar a loja: ") . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, $this->getTranslator()->translate("Loja atualizada com sucesso.")));
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

    private function uploadStoreImage(array $file, int $storeId, string $type, ?string $currentFile = null) {
        $storage = new Storage();
        $currentPath = !empty($currentFile) ? $storage->cleanStorageType($currentFile) : '';

        $folder = 'uploads/stores/' . $storeId . '/';
        $extension = pathinfo($file['name'] ?? '', PATHINFO_EXTENSION);
        $imageName = $folder . $type . (!empty($extension) ? '.' . $extension : '');

        $upload = $storage->sendFileToBucket($file['tmp_name'], $imageName, true);
        if (($upload['code'] ?? 0) !== 200) {
            throw new Exception($upload['message'] ?? 'Erro ao enviar imagem.');
        }

        if (!empty($currentPath) && $currentPath !== $imageName) {
            $storage->deleteFileFromBucket($currentPath, true);
        }

        return $upload['message'];
    }

}