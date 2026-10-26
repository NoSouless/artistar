<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\Home;
use Source\Model\Stock;
use Source\Model\SalesStatement;
use Source\Model\Statistics;
use Source\Model\Events;
use Source\Model\Helpers\BuildLayout;

class homeController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        if($this->getLogado() && $this->getUser()['email_validado'] != 1) $this->checkIfEmailIsValidated();
        $this->getLayout()->setFooter('footer');
    }

    public function home() {
        if ($this->getLogado()) {
            $this->homeLogged();
        } else {
            $this->homeUnlogged();
        }
        return;
    }

    public function homeUnlogged() {
        $this->getLayout()->setHeader('header');
        $this->addLayout();
        $this->addTranslator('home');
        echo $this->view->render("home");
    }

    public function homeLogged() {
        $this->getLayout()->setHeader('header-logado');
        $this->addLayout();
        $this->addTranslator('home-logged');
        $translator = $this->getTranslator();
        $user = $this->getUser();
        $store = $user['loja_id'] ?? 0;

        $stockModel = new Stock();
        $stockModel->setTranslator($translator);
        $stocks = $stockModel->getStocks($store);

        $salesModel = new SalesStatement();
        $salesModel->setTranslator($translator);
        $totalSales = array_merge(
            ['total_value' => 0, 'total_products' => 0, 'total_items' => 0, 'total_sales' => 0, 'total_canceled' => 0],
            array_filter($salesModel->getInfoBanner($store) ?: [], fn($v) => $v !== null),
            array_filter($salesModel->getTotalSales($store) ?: [], fn($v) => $v !== null)
        );

        $statisticsModel = new Statistics();
        $statisticsModel->setTranslator($translator);
        $periodos = $statisticsModel->getPeriodos();
        $period = 'month';
        $referenceDate = date('Y-m-d');
        $totais = $statisticsModel->getTotals($store, $periodos[$period], $referenceDate);
        $graphData = $statisticsModel->arrangeGraphData($totais, $period, $referenceDate);

        $eventsModel = new Events();
        $events = $eventsModel->getUserEvents($user['id']);
        $totals = $eventsModel->getUserEventsTotals($user['id']);
        $todayEvents = $eventsModel->getUserEventsToday($user['id']);

        echo $this->view->render("home-logged", [
            'stocks' => $stocks,
            'totalSales' => $totalSales,
            'graphData' => $graphData,
            'events' => $events,
            'totals' => $totals,
            'todayEvents' => $todayEvents,
            'notifications' => $this->buildNotifications($translator, $stocks, $totals, $todayEvents, $store),
            'isAdmin' => !empty($this->getPermissions()['admin']),
            'hasStore' => !empty($store),
            'currency' => ($user['loja_moeda'] ?? '') ?: 'R$'
        ]);
    }

    private function buildNotifications($translator, $stocks, $totals, $todayEvents, $store) {
        $notifications = [];
        $add = function ($icon, $color, $message, $link) use (&$notifications) {
            $notifications[] = ['icon' => $icon, 'color' => $color, 'message' => $message, 'link' => $link];
        };
        if (empty($store)) {
            $add('fa-store', 'text-warning', $translator->translate('Você ainda não possui uma loja cadastrada.'), url('settings/store'));
        }
        if (!empty($stocks['outOfStock'])) {
            $add('fa-circle-exclamation', 'text-danger', $stocks['outOfStock'] . ' ' . $translator->translate('produto(s) sem estoque.'), url('stock') . '?filter%5Bstock_status%5D=out');
        }
        if (!empty($stocks['lowStock'])) {
            $add('fa-triangle-exclamation', 'text-warning', $stocks['lowStock'] . ' ' . $translator->translate('produto(s) com estoque baixo.'), url('stock') . '?filter%5Bstock_status%5D=low');
        }
        if (!empty($todayEvents)) {
            $add('fa-calendar-day', 'color-stellar-blue', count($todayEvents) . ' ' . $translator->translate('evento(s) acontecendo hoje.'), url('events/my-events'));
        }
        if (!empty($totals['total_pendente'])) {
            $add('fa-hourglass-half', 'color-cotton-candy', $totals['total_pendente'] . ' ' . $translator->translate('inscrição(ões) pendente(s) de realização.'), url('events/my-events'));
        }
        if (!empty($totals['total_aprovada'])) {
            $add('fa-circle-check', 'text-success', $totals['total_aprovada'] . ' ' . $translator->translate('inscrição(ões) aprovada(s).'), url('events/my-events'));
        }
        if (!empty($totals['total_reprovada'])) {
            $add('fa-circle-xmark', 'text-danger', $totals['total_reprovada'] . ' ' . $translator->translate('inscrição(ões) reprovada(s).'), url('events/my-events'));
        }
        return $notifications;
    }

    public function login() {
        $this->addTranslator('login');
        $this->addLayout($this->getTranslator()->translate("Login"));
        $this->addLayout();
        if ($this->getLogado()) {
            header("location: /");
            return;
        }
        echo $this->view->render("login");
        return;
    }

    public function logout(){
        session_destroy();
        header("location: /");
    }


}
