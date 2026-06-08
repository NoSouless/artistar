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
        $store = $storeModel->getStoreData($data);   

        if (empty($store)) {
            header("location: /error/404");
            return;
        }

        $storeId = $store['codigo'];

        $isOwner = false;
        if ($this->getLogado() && !empty($this->getUser()['loja_id'])) 
            $isOwner = ((int) $this->getUser()['loja_id']) === ((int) $store['codigo']);

        $this->addLayout(!empty($store['nome']) ? $store['nome'] : null);

        // $followersCount = $storeModel->getStoreFollowersCount($storeId);

        echo $this->view->render("store/details", [
            'layout' => [
                'title' =>  $store['nome'] . ' - Artistar', 
                'logado' => $this->getLogado(),
                'header' => true,
                'footer' => true
            ],
            'store' => $store,
            'storeName' => $store['nome'],
            'storeUsername' => '@' . $store['nome_unico'],
            'storeDescription' => $store['descricao'],
            'storePhoto' => !empty($store['foto']) ? storageURL($store['foto']) : '',
            'storeId' => $storeId,
            // 'followersCount' => $followersCount,
            'followersCount' => 0,
            'storeInitial' => strtoupper(substr(trim($store['nome']), 0, 1)),
            'bannerPlaceholder' => url('assets/image/800x400.png'),
            'isOwner' => $isOwner,
            'loginRedirect' => base64_encode(urlencode($_SERVER['REQUEST_URI'] ?? '/')),
            'products' => $store['produtos']
        ]);
    }

    public function manage() {
        $this->validaAcesso();

        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

        $storeModel = new Store();
        $store = $storeModel->getStoreData([
            'storeId' => $storeId
        ]);

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

    public function manageProducts($data) {
        $this->validaAcesso();
        try {
            $storeId = $this->getUser()['loja_id'] ?? 0;
            $search = isset($data['search']) ? trim((string) $data['search']) : '';

            $storeModel = new Store();
            $selecteds = $storeModel->getShowcaseProductOrder($storeId, [
                'search' => $search,
                'collection_id' => 0
            ], true);

            foreach($selecteds as &$products) {
                foreach ($products as &$product) {
                    $product['thumbnail'] = !empty($product['thumbnail']) ? storageURL($product['thumbnail']) : url('assets/image/200x300.png');
                }
            }
            exit($this->renderApiResponse(200, null, $selecteds));
        } catch (\Throwable $e) {
            exit($this->renderApiResponse(500, 'Erro interno ao carregar produtos para gestao.'));
        }
    }

    public function editShowcase($data) {
        $this->validaAcesso();

        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $order = $data['selected_products_order'] ?? [];

        $storeModel = new Store();

        $products = $storeModel->getShowcaseProductOrder($storeId, [
            'only_in_showcase' => true,
            'collection_id' => 0
        ]);

        $existingOrder = [];
        array_walk($products, function($product) use (&$existingOrder) {
            $existingOrder[$product['id']] = [
                'ordenacao_id' => $product['ordenacao_id'],
                'ordem' => $product['ordem']
            ];
        });

        foreach($order as $productId => $position) {
            if (filter_var($productId, FILTER_VALIDATE_INT) !== false && filter_var($position, FILTER_VALIDATE_INT) !== false) {
                if ($position < 0) continue;
                if (isset($existingOrder[$productId])) {
                    if ($existingOrder[$productId]['ordem'] != $position) 
                        $storeModel->updateShowcaseProductOrder($existingOrder[$productId]['ordenacao_id'], $position);
                } else {
                    $storeModel->insertShowcaseProductOrder($productId, $position);
                }
            }
            unset($existingOrder[$productId]);
        }

        foreach($existingOrder as $productId => $productData) $storeModel->deleteShowcaseProductOrder($productData['ordenacao_id']);
        exit($this->renderApiResponse(200, 'Vitrine atualizada com sucesso.'));

    }
}