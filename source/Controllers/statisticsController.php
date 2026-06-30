<?php

namespace Source\Controllers;
use Source\Core\Core;
use Source\Model\Statistics;

class statisticsController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->validaAcesso();
        $this->getLayout()->setHeader('header-logado');
        $this->getLayout()->setFooter('footer');
    }

    public function home() {
        $this->addTranslator('statistics/home');
        $this->addLayout($this->getTranslator()->translate("Estatísticas"));
        $statisticsModel = new Statistics();
        $statisticsModel->setTranslator($this->getTranslator());
        $store = $this->getUser()['loja_id'] ?? 0;
        $periodoSelecionado = $_GET['period'] ?? 'day';
        $dataReferencia = $_GET['date'] ?? date('Y-m-d');
        $periodos = $statisticsModel->getPeriodos();
        $totais = $statisticsModel->getTotals($store, $periodos[$periodoSelecionado], $dataReferencia);
        $bestSellers = $statisticsModel->getBestSellers($store, $periodos[$periodoSelecionado], $dataReferencia);
        $graphData = $statisticsModel->arrangeGraphData($totais, $periodoSelecionado, $dataReferencia);
        $productRanking = $statisticsModel->getProductRanking($store, $periodos[$periodoSelecionado], $dataReferencia);
        $allProducts = $statisticsModel->getAllProducts($store);
        $allCategories = $statisticsModel->getAllCategories($store);
        $graphs = $statisticsModel->getGraphTypes($store);
        $customGraphs = $statisticsModel->getGraphData($store, $graphs, $periodos[$periodoSelecionado], $dataReferencia);

        echo $this->view->render("statistics/home", [
            'periodos' => $periodos,
            'periodoSelecionado' => $periodoSelecionado,
            'dataReferencia' => $dataReferencia,
            'bestSellers' => $bestSellers,
            'graphData' => $graphData,
            'productRanking' => $productRanking,
            'allProducts' => $allProducts,
            'allCategories' => $allCategories,
            'customGraphs' => $customGraphs
        ]);
        return;
    }

    public function editGraph($post) {
        $this->addTranslator('statistics/home');
        $tradutor = $this->getTranslator();
        $statisticsModel = new Statistics();
        if (empty($post['graph-id']) || $post['graph-id'] < 1 || $post['graph-id'] > 6) exit($this->renderApiResponse(400, $tradutor->translate("Gráfico não encontrado.")));
        $graphId = $post['graph-id'];

        $store = $this->getUser()['loja_id'] ?? 0;
        if (empty($store)) exit($this->renderApiResponse(400, $tradutor->translate("Loja não encontrada.")));

        $info = [
            'type' => $post['graph-type'] ?? 'pie',
            'counter' => $post['graph-counter'] ?? 'sold_units',
            'target' => $post['graph-target'] ?? 'product',
            'filter' => $post['graph-filter'] ?? 'all',
            'list' => NULL
        ];

        if ($info['filter'] == 'custom') {
            $info['list'] = ($info['target'] == 'category') ? $post['category'] : $post['products'];
            $info['list'] = implode(",", $info['list']);
        }

        try {
            if (empty($statisticsModel->getGraphTypeByPosition($store, $graphId))) {
                $statisticsModel->createGraphType($store, $graphId, $info);
            } else {
                $statisticsModel->updateGraphType($store, $graphId, $info);
            }
            exit($this->renderApiResponse(200, $tradutor->translate("Gráfico atualizado com sucesso.")));
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao atualizar gráfico: ") . $e->getMessage()));
        }

        return;
    }

}