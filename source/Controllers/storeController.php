<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\Store;

class storeController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->getLayout()->setHeader($this->getLogado() ? 'header-logado' : 'header');
        $this->getLayout()->setFooter('footer');
    }

    public function details($data) {

        $storeModel = new Store();
        $storeId = null;

        if (!empty($data['storeId'])) {
            $storeId = (int) $data['storeId'];
        } elseif (!empty($data['friendlyUrl'])) {
            $friendlyUrl = strtolower(trim((string) $data['friendlyUrl'], '/'));

            if ($friendlyUrl === 'manage') {
                $this->manage();
                return;
            }

            $storeBySlug = $storeModel->getStoreDataByFriendlyUrl((string) $data['friendlyUrl']);
            $storeId = !empty($storeBySlug['codigo']) ? (int) $storeBySlug['codigo'] : null;
        } elseif (!empty($this->getUser()['loja_id'])) {
            $storeId = (int) $this->getUser()['loja_id'];
        }

        $store = $storeModel->getStoreData($storeId);

        if (empty($store)) {
            header("location: /error/404");
            return;
        }

        $isOwner = false;
        if ($this->getLogado() && !empty($this->getUser()['loja_id'])) {
            $isOwner = ((int) $this->getUser()['loja_id']) === ((int) $store['codigo']);
        }

        $this->addLayout(!empty($store['nome']) ? $store['nome'] : null);

        $products = $storeModel->getPublicProducts($storeId, '', 24, true);
        $followersCount = $storeModel->getStoreFollowersCount($storeId);

        echo $this->view->render("store/details", [
            'layout' => [
                'title' =>  $store['nome'] . ' - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'store' => $store,
            'isOwner' => $isOwner,
            'followersCount' => $followersCount,
            'products' => $products ?? []
        ]);
    }

    public function manage() {
        $this->validaAcesso();

        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        if ($storeId < 1) {
            header("location: /error/404");
            return;
        }

        $storeModel = new Store();
        $store = $storeModel->getStoreData($storeId);

        if (empty($store)) {
            header("location: /error/404");
            return;
        }

        $this->addLayout('Minha Loja');

        echo $this->view->render("store/manage", [
            'layout' => [
                'title' =>  'Minha Loja - Artistar',
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'store' => $store,
            'storeName' => !empty($store['nome']) ? $store['nome'] : 'Loja sem nome',
            'storeUsername' => $store['nome_unico'],
            'storeDescription' => !empty($store['descricao']) ? $store['descricao'] : 'Sem descricao cadastrada.',
            'storePhoto' => !empty($store['foto']) ? storageURL($store['foto']) : '',
            'storeId' => !empty($store['codigo']) ? (int) $store['codigo'] : 0,
            'storeInitial' => strtoupper(substr(trim($store['nome']), 0, 1)),
            'bannerPlaceholder' => url('assets/image/800x400.png')
        ]);
    }

    public function editShowcase($data) {
        $this->validaAcesso();

        $storeId = !empty($data['storeId']) ? (int) $data['storeId'] : 0;
        $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

        if ($storeId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
            header("location: /error/404");
            return;
        }

        // Lógica para atualizar a vitrine da loja com os produtos selecionados

        header("location: /store/manage");
        return;
    }

    public function manageProducts($data) {
        $this->validaAcesso();
        try {
            $storeId = $this->getUser()['loja_id'] ?? 0;
            $search = isset($data['search']) ? trim((string) $data['search']) : '';

            $storeModel = new Store();
            $selecteds = $storeModel->getManageProducts($storeId, $search);

            foreach($selecteds as &$products) {
                foreach ($products as &$product) {
                    $price = ((float) $product['valor']) - ((float) $product['valor_desconto']);
                    if ($price < 0) $price = (float) $product['valor'];
                    $product['thumbnail'] = !empty($product['thumbnail']) ? storageURL($product['thumbnail']) : url('assets/image/200x300.png');
                    $product['price'] = moedaReal($price);
                }
            }

            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 200,
                    'data' => $selecteds
                ]
            ]);
        } catch (\Throwable $e) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 500,
                    'message' => 'Erro interno ao carregar produtos para gestao.'
                ]
            ]);
            return;
        }

    }

    public function edit($data) {
        $this->validaAcesso();

        $storeId = !empty($data['storeId']) ? (int) $data['storeId'] : 0;
        $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

        if ($storeId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
            header("location: /error/404");
            return;
        }

        header("location: /store/manage");
        return;
    }
}