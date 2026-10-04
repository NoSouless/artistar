<?php

namespace Source\Controllers;
use Source\Core\Core;
use Source\Model\SalesStatement;
use Source\Model\Helpers\PaymentMethods;

class salesStatementController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->validaAcesso();
        $this->getLayout()->setHeader('header-logado');
        $this->getLayout()->setFooter('footer');
    }

    public function home() {
        $this->addTranslator('sales-statement/home');
        $this->addLayout($this->getTranslator()->translate("Extrato de Vendas"));
        $salesStatementModel = new SalesStatement();
        $salesStatementModel->setTranslator($this->getTranslator());
        $store = $this->getUser()['loja_id'] ?? 0;

        $pagination = $_GET['pagination'] ?? [
            'offset' => 0
        ];
        $pagination['limit'] = 25;

        $filter = [];
        if (isset($_GET['event']) && $_GET['event'] !== 'all') {
            $filter['event'] = $_GET['event'] === 'none' ? 0 : $_GET['event'];
        }
        $whereSales = $salesStatementModel->buildWhereSales($filter);

        $sort = $_GET['sort'] ?? 'name_asc';

        $orderList = $salesStatementModel->getOrderList($sort);
        $order = $salesStatementModel->buildOrderBy($orderList, $sort);

        $sales = $salesStatementModel->getSales($store, $pagination, $whereSales, $order);

        $totalSales = $salesStatementModel->getTotalSales($store, $pagination, $whereSales);
        $totalSales = array_merge($salesStatementModel->getInfoBanner($store, $whereSales), $totalSales);

        $pages = [
            'current' => ($pagination['offset'] / $pagination['limit']) + 1,
            'total' => ceil($totalSales['total_sales'] / $pagination['limit'])
        ]; 

        $salesIds = array_column($sales, 'id');
        $items = $salesStatementModel->getItems($salesIds);

        $salesItems = [];

        foreach ($items as $item) {
            $item['valor_unitario'] = moedaReal($item['valor'] / $item['qtd']);
            $item['desconto_unitario'] = moedaReal($item['desconto'] / $item['qtd']);
            $item['valor'] = moedaReal($item['valor']);
            $item['desconto'] = moedaReal($item['desconto']);
            $item['thumbnail'] = empty($item['thumbnail']) ? url('assets/image/200x300.png') : storageURL($item['thumbnail']);
            if (!isset($salesItems[$item['id_venda']])) {
                $salesItems[$item['id_venda']] = [];
            }
            $salesItems[$item['id_venda']][] = $item;
        }
         
        echo $this->view->render("sales-statement/home", [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'items' => $salesItems,
            'pagination' => $pagination,
            'pages' => $pages,
            'sort' => $sort,
            'orderList' => $orderList,
            'events' => $salesStatementModel->getStoreSubscriptions($store),
            'currency' => ($this->getUser()['loja_moeda'] ?? '') ?: 'R$',
        ]);
        return;
    }

    public function saleDetails($get) {
        $this->addTranslator('sales-statement/saleDetails');
        $salesStatementModel = new SalesStatement();

        $store = $this->getUser()['loja_id'] ?? 0;
        $saleId = $get['saleId'] ?? 0;
        if (!$store) exit($this->renderApiResponse(404, $this->getTranslator()->translate("Loja não encontrada.")));

        $saleInfo = $salesStatementModel->getSaleById($saleId, $store);

        if (!$saleInfo) exit($this->renderApiResponse(404, $this->getTranslator()->translate("Venda não encontrada.")));

        $products = $saleId ? $salesStatementModel->getProductsWithSales($saleId) : [];

        foreach ($products as &$item) {
            $item['total'] = moedaReal($item['preco_produto']-$item['desconto_produto']);
            $item['preco'] = moedaReal($item['preco_produto']);
            $item['desconto'] = moedaReal($item['desconto_produto']);
            $item['estoque'] = (int)$item['estoque_produto']+ ($saleInfo['cancelada'] == '1' ? 0 : (int)$item['qtd_vendida']);
            $item['desconto_venda'] = moedaReal($item['desconto_venda']);
            $item['valor_venda'] = moedaReal($item['valor_venda']);
            $item['imagem'] = empty($item['imagem']) ? url('assets/image/200x300.png') : storageURL($item['imagem']);
        }

        $paymentMethods = new PaymentMethods();

        $this->addLayout($this->getTranslator()->translate("Venda")." #".$saleInfo['numero']);

        echo $this->view->render("sales-statement/saleDetails", [
            'products' => $products,
            'saleInfo' => $saleInfo,
            'paymentMethods' => $paymentMethods->getMethods(),
            'events' => $salesStatementModel->getStoreSubscriptions($store),
            'currency' => ($this->getUser()['loja_moeda'] ?? '') ?: 'R$',
        ]);
        return;
    }

    public function editSale($post) {
        $this->addTranslator('sales-statement/saleDetails');
        $tradutor = $this->getTranslator();
        try {
            $salesStatementModel = new SalesStatement();
            $paymentMethods = new PaymentMethods();
            $salesStatementModel->updateSaleInfo($post['sale_id'], [
                'payment_method' => $paymentMethods->getMethodByKey($post['payment_method']),
                'sale_datetime' => empty($post['sale_datetime']) ? null : $post['sale_datetime'],
                'paid' => isset($post['paid']) ? 1 : null,
                'payment_date' => isset($post['paid']) ? $post['payment_date'] : null,
                'delivered' => isset($post['delivered']) ? 1 : null,
                'delivery_date' => isset($post['delivered']) ? $post['delivery_date'] : null,
                'canceled' => isset($post['canceled']) ? 1 : null,
                'cancellation_date' => isset($post['canceled']) ?$post['cancellation_date'] : null,
                'sale_id' => $post['sale_id'],
                'event' => $post['event'] ?? null,
            ]);
            $existingItems = $salesStatementModel->getSalesProducts($post['sale_id']);
            $items = $post['items'] ?? [];
            foreach ($items as $item) {
                if (isset($existingItems[$item['id']])) {
                    $salesStatementModel->updateItem($existingItems[$item['id']]['id'], $item);
                    unset($existingItems[$item['id']]);
                } else {
                    $salesStatementModel->insertItem($post['sale_id'], $item);
                }
            }
            foreach ($existingItems as $item) {
                $salesStatementModel->deleteItem($item['id']);
            }
            exit($this->renderApiResponse(200, $tradutor->translate("Venda atualizada com sucesso.")));
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao atualizar a venda: ").$e->getMessage()));
        }

        return;
    }

}