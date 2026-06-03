<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\API;
use Source\Model\Store;

class apiController extends Core {

    private function renderError($errCode = 404, $forceLogin = false) {
        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => $errCode,
                'forceLogin' => $forceLogin
            ],
            
        ]);
        return;
    }

    public function events() {

        $dados = new API();
        $eventos = $dados->listEvents();

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => 200,
                'data' => [
                    'eventos' => $eventos
                ]
            ]
        ]);
        return;
    }

    public function eventFavorite($data) {

        // if (!$this->getLogado()) {
        //     $this->renderError(401, true);
        //     return;
        // }

        $dados = new API();

        $favorite = $dados->setFavorite(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT));

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => 200,
                'data' => [
                    'favorite' => $favorite
                ]
            ]
        ]);

        return;
    }

    public function states() {

        $dados = new API();
        $states = $dados->listStates();

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => 200,
                'data' => [
                    'states' => $states
                ]
            ]
        ]);
    
        return;
    }

    public function cities($data) {

        $dados = new API();
        $cities = $dados->listCities(filter_var($data['uf'], FILTER_SANITIZE_STRING));

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => 200,
                'data' => [
                    'cities' => $cities
                ]
            ]
        ]);
    
        return;
    }

    public function storeProducts($data) {
        try {
            $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
            $search = isset($data['search']) ? trim((string) $data['search']) : '';

            if ($storeId < 1) {
                echo $this->view->render("apiResponse", [
                    'result' => [
                        'code' => 400,
                        'message' => 'Loja invalida.'
                    ]
                ]);
                return;
            }

            $storeModel = new Store();
            $products = $storeModel->getPublicProducts($storeId, $search, 24, true);

            foreach ($products as &$product) {
                $price = ((float) $product['valor']) - ((float) $product['valor_desconto']);
                if ($price < 0) $price = (float) $product['valor'];

                $product['thumbnail'] = !empty($product['thumbnail'])
                    ? storageURL($product['thumbnail'])
                    : url('assets/image/200x300.png');
                $product['price'] = moedaReal($price);
            }

            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 200,
                    'data' => [
                        'products' => $products
                    ]
                ]
            ]);

            return;
        } catch (\Throwable $e) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 500,
                    'message' => 'Erro interno ao carregar produtos da loja.'
                ]
            ]);
            return;
        }
    }

    public function manageStoreProducts($data) {
        try {
            if (!$this->getLogado()) {
                $this->renderError(401, true);
                return;
            }

            $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
            $search = isset($data['search']) ? trim((string) $data['search']) : '';
            $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

            if ($storeId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
                echo $this->view->render("apiResponse", [
                    'result' => [
                        'code' => 403,
                        'message' => 'Acesso negado.'
                    ]
                ]);
                return;
            }

            $storeModel = new Store();
            $products = $storeModel->getManageProducts($storeId, $search);

            foreach ($products as &$product) {
                $price = ((float) $product['valor']) - ((float) $product['valor_desconto']);
                if ($price < 0) $price = (float) $product['valor'];

                $product['thumbnail'] = !empty($product['thumbnail'])
                    ? storageURL($product['thumbnail'])
                    : url('assets/image/200x300.png');
                $product['price'] = moedaReal($price);
                $product['selected'] = ((int) ($product['selecionado'] ?? 0)) === 1;
            }

            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 200,
                    'data' => [
                        'products' => $products
                    ]
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

    public function toggleStoreProductOrder($data) {
        if (!$this->getLogado()) {
            $this->renderError(401, true);
            return;
        }

        $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
        $productId = isset($data['productId']) ? (int) $data['productId'] : 0;
        $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

        if ($storeId < 1 || $productId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 403,
                    'message' => 'Acesso negado.'
                ]
            ]);
            return;
        }

        $storeModel = new Store();
        $toggle = $storeModel->toggleProductOrder($storeId, $productId);

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => $toggle['success'] ? 200 : 400,
                'message' => $toggle['message'],
                'data' => [
                    'selected' => (bool) ($toggle['selected'] ?? false)
                ]
            ]
        ]);
    }

    public function reorderStoreProductOrder($data) {
        if (!$this->getLogado()) {
            $this->renderError(401, true);
            return;
        }

        $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
        $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $productIds = $data['productIds'] ?? [];

        if ($storeId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 403,
                    'message' => 'Acesso negado.'
                ]
            ]);
            return;
        }

        $storeModel = new Store();
        $reorder = $storeModel->reorderProductOrders($storeId, $productIds);

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => $reorder['success'] ? 200 : 400,
                'message' => $reorder['message']
            ]
        ]);
    }

    public function manageCollectionProducts($data) {
        try {
            if (!$this->getLogado()) {
                $this->renderError(401, true);
                return;
            }

            $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
            $collectionId = isset($data['collectionId']) ? (int) $data['collectionId'] : 0;
            $search = isset($data['search']) ? trim((string) $data['search']) : '';
            $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

            if ($storeId < 1 || $collectionId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
                echo $this->view->render("apiResponse", [
                    'result' => [
                        'code' => 403,
                        'message' => 'Acesso negado.'
                    ]
                ]);
                return;
            }

            $storeModel = new Store();
            $products = $storeModel->getCollectionManageProducts($storeId, $collectionId, $search);

            foreach ($products as &$product) {
                $price = ((float) $product['valor']) - ((float) $product['valor_desconto']);
                if ($price < 0) $price = (float) $product['valor'];

                $product['thumbnail'] = !empty($product['thumbnail'])
                    ? storageURL($product['thumbnail'])
                    : url('assets/image/200x300.png');
                $product['price'] = moedaReal($price);
                $product['selected'] = ((int) ($product['selecionado'] ?? 0)) === 1;
            }

            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 200,
                    'data' => [
                        'products' => $products
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 500,
                    'message' => 'Erro interno ao carregar produtos da colecao.'
                ]
            ]);
            return;
        }
    }

    public function toggleCollectionProductOrder($data) {
        if (!$this->getLogado()) {
            $this->renderError(401, true);
            return;
        }

        $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
        $collectionId = isset($data['collectionId']) ? (int) $data['collectionId'] : 0;
        $productId = isset($data['productId']) ? (int) $data['productId'] : 0;
        $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;

        if ($storeId < 1 || $collectionId < 1 || $productId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 403,
                    'message' => 'Acesso negado.'
                ]
            ]);
            return;
        }

        $storeModel = new Store();
        $toggle = $storeModel->toggleCollectionProduct($storeId, $collectionId, $productId);

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => $toggle['success'] ? 200 : 400,
                'message' => $toggle['message'],
                'data' => [
                    'selected' => (bool) ($toggle['selected'] ?? false)
                ]
            ]
        ]);
    }

    public function reorderCollectionProductOrder($data) {
        if (!$this->getLogado()) {
            $this->renderError(401, true);
            return;
        }

        $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
        $collectionId = isset($data['collectionId']) ? (int) $data['collectionId'] : 0;
        $loggedStoreId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $productIds = $data['productIds'] ?? [];

        if ($storeId < 1 || $collectionId < 1 || $loggedStoreId < 1 || $storeId !== $loggedStoreId) {
            echo $this->view->render("apiResponse", [
                'result' => [
                    'code' => 403,
                    'message' => 'Acesso negado.'
                ]
            ]);
            return;
        }

        $storeModel = new Store();
        $reorder = $storeModel->reorderCollectionProductOrders($storeId, $collectionId, $productIds);

        echo $this->view->render("apiResponse", [
            'result' => [
                'code' => $reorder['success'] ? 200 : 400,
                'message' => $reorder['message']
            ]
        ]);
    }


    public function followStore($data) {
        if (!$this->getLogado()) {
            $returnUrl = isset($data['returnUrl']) ? (string) $data['returnUrl'] : base64_encode(urlencode('/'));
            exit($this->renderApiResponse(401, 'Usuário não autenticado.', [
                'redirect' => '/login?r=' . $returnUrl
            ]));
        }

        $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;
        $userId = !empty($this->getUser()['id']) ? (int) $this->getUser()['id'] : 0;

        if ($storeId < 1 || $userId < 1) {
            exit($this->renderApiResponse(400, 'Dados invalidos.'));
        }

        if (!empty($this->getUser()['loja_id']) && ((int) $this->getUser()['loja_id']) === $storeId) {
            exit($this->renderApiResponse(403, 'Voce nao pode seguir a propria loja.'));
        }

        $storeModel = new Store();
        $store = $storeModel->getStoreData($storeId);

        if (empty($store)) {
            exit($this->renderApiResponse(404, 'Loja nao encontrada.'));
        }

        $existingFollow = $storeModel->checkIfUserFollowsStore($storeId, $userId);
        if (!empty($existingFollow)) {
            exit($this->renderApiResponse(200, 'Voce ja segue esta loja.', [
                'followed' => true,
                'followers' => $storeModel->getStoreFollowersCount($storeId)
            ]));
        }

        try {
            $followId = $storeModel->followStore($storeId, $userId);
            if (!$followId) {
                exit($this->renderApiResponse(500, 'Erro ao seguir a loja.'));
            }
        } catch (\Throwable $e) {
            exit($this->renderApiResponse(500, 'Erro ao seguir a loja: ' . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, 'Loja seguida com sucesso.', [
            'followed' => true,
            'followers' => $storeModel->getStoreFollowersCount($storeId)
        ]));
    }
}