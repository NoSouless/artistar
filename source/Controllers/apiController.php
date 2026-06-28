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
        // try {
            $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;

            if (empty($storeId) || $storeId < 1) exit($this->renderApiResponse(400, 'Loja invalida.'));

            $storeModel = new Store();
            $filters = [];
            if (isset($data['categoryId']) && !empty($data['categoryId'])) {
                $filters['category_id'] = filter_var($data['categoryId'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (isset($data['collectionId']) && !empty($data['collectionId'])) {
                $filters['collection_id'] = filter_var($data['collectionId'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (empty($filters)) {
                $filters['only_in_showcase'] = true;
            }
            $products = $storeModel->getShowcaseProductOrder($storeId, $filters);

            foreach ($products as &$product) {
                $product['thumbnail'] = !empty($product['thumbnail']) ? storageURL($product['thumbnail']) : url('assets/image/200x300.png');
                $product['discount_percentage'] = ($product['valor_desconto'] > 0 && $product['valor_original'] > 0) ?  100 - round((($product['valor_original'] - $product['valor_desconto']) / $product['valor_original']) * 100) : 0;
            }

            exit($this->renderApiResponse(200, null, [
                'products' => $products
            ]));

            return;
        // } catch (\Throwable $e) {
        //     exit($this->renderApiResponse(500, 'Erro interno ao carregar produtos da loja.'));
        //     return;
        // }
    }

    public function storeCategories($data) {
        try {
            $storeId = isset($data['storeId']) ? (int) $data['storeId'] : 0;

            if (empty($storeId) || $storeId < 1) exit($this->renderApiResponse(400, 'Loja invalida.'));

            $storeModel = new Store();
            $categories = $storeModel->getStorePublicCategories($storeId);

            $categories = array_map(function($category) {
                $category['thumbnail'] = !empty($category['foto']) ? storageURL($category['foto']) : url('assets/image/200x300.png');
                return $category;
            }, $categories);

            exit($this->renderApiResponse(200, null, [
                'categories' => $categories
            ]));

            return;
        } catch (\Throwable $e) {
            exit($this->renderApiResponse(500, 'Erro interno ao carregar categorias da loja.'));
            return;
        }
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