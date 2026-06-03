<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\Events;

class eventsController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->getLayout()->setHeader($this->getLogado() ? 'header-logado' : 'header');
        $this->getLayout()->setFooter('footer');
    }

    public function home() {
        // $get = [
        //     's'     => (isset($_GET['s']) ? filter_var($_GET['s'], FILTER_SANITIZE_STRING) : ''),
        //     'r'     => (isset($_GET['r']) ? filter_var($_GET['r'], FILTER_SANITIZE_STRING) : ''),
        //     'c'     => (isset($_GET['c']) ? filter_var($_GET['c'], FILTER_SANITIZE_STRING) : ''),
        //     'sd'    => (isset($_GET['sd']) ? filter_var($_GET['sd'], FILTER_SANITIZE_STRING) : ''),
        //     'fd'    => (isset($_GET['fd']) ? filter_var($_GET['fd'], FILTER_SANITIZE_STRING) : '')
        // ];

        // $paginaAtual = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT) : 1;

        // $dados = new Events(); 

        // $queryString = http_build_query($get);
        // $paginacao = $dados->getEventosPaginacao($paginaAtual, $queryString);

        // echo $this->view->render("events/home", [
        //     'title'         =>  'Eventos - Artistar', 
        //     'logado'        => $this->getLogado(),
        //     'events'        => $dados->getEvents(),
        //     'estados'       => $this->getEstados(),
        //     'get'           => $get,
        //     'queryString'   => $queryString,
        //     'currentPage'   => $paginaAtual,
        //     'pages'         => $paginacao
        // ]);
        // return;
    }

    public function details($data) {
        $this->addTranslator('events/details');

        $dados = new Events();
        $store = !empty($this->getUser()) ? $this->getUser()['loja_id'] : null;
        $event = $dados->getEventBasicInfo(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT), $store);

        if (empty($event) || ($event['evento_privado'] == 1 && ($this->getUser()['id'] != $event['evento_proprietario'] && empty($event['inscricao_id'])))) {
            header("Location: /error/404");
            return;
        }

        $event['endereco_completo'] = $event['endereco_com_complemento'] = $event['evento_endereco_logradouro'];
        if (!empty($event['evento_endereco_numero'])) {
            $event['endereco_completo'] .= ', '.$event['evento_endereco_numero'];
            $event['endereco_com_complemento'] .= ', '.$event['evento_endereco_numero'];
        }
        if (!empty($event['evento_endereco_complemento'])) $event['endereco_com_complemento'] .= ' - '.$event['evento_endereco_complemento'];
        if (!empty($event['evento_endereco_bairro'])) {
            $event['endereco_completo'] .= ' - '.$event['evento_endereco_bairro'];
            $event['endereco_com_complemento'] .= ' - '.$event['evento_endereco_bairro'];
        }
        if (!empty($event['evento_endereco_cidade'])) {
            $event['endereco_completo'] .= ', '.$event['evento_endereco_cidade'];
            $event['endereco_com_complemento'] .= ', '.$event['evento_endereco_cidade'];
        }
        if (!empty($event['evento_endereco_estado'])) {
            $event['endereco_completo'] .= ' - '.$event['evento_endereco_estado'];
            $event['endereco_com_complemento'] .= ' - '.$event['evento_endereco_estado'];
        }
        if (!empty($event['evento_endereco_cep'])) {
            $event['endereco_completo'] .= ', '.$event['evento_endereco_cep'];
            $event['endereco_com_complemento'] .= ', '.$event['evento_endereco_cep'];
        }
        if (!empty($event['evento_pais'])) {
            $event['endereco_completo'] .= ', '.$event['evento_pais'];
            $event['endereco_com_complemento'] .= ', '.$event['evento_pais'];
        }
        if (!empty($event['evento_descricao'])) $this->getLayout()->setDescription($event['evento_descricao']);
        $this->addLayout($event['evento_nome']);

        echo $this->view->render("events/details", [
            'event'         => $event,
            'user'          => empty($this->getUser()) ? null : $this->getUser()['id'],
            'days'          => $dados->getEventDays(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT)),
            'advantages'    => $dados->getEventAdvantages(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT)),
            'prices'        => $dados->getEventPrices(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT)),
            'photos'        => $dados->getEventPhotos(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT))
        ]);
        return;
    }

    public function subscribe($post) {
        if (!$this->getLogado()) exit($this->renderApiResponse(401, "Usuário não autenticado.", [
            'redirect' => '/login?r=' . base64_encode(urlencode('/events/id/'.$post['eventId']))
        ]));
        $eventsModel = new Events();
        $eventId = filter_var($post['eventId'], FILTER_SANITIZE_NUMBER_INT);
        $subscribed = $eventsModel->checkIfUserIsSubscribed($eventId, $this->getUser()['loja_id']);
        $mensagem = '';
        if (($post['status'] == 'true' || $post['status'] === true)) { 
            $status = false;
            if (!empty($subscribed)) {
                try {
                    $eventsModel->unsubscribeFromEvent($subscribed['inscricao_id']);
                    $mensagem = "Inscrição cancelada com sucesso!";
                } catch (\Exception $e) {
                    exit($this->renderApiResponse(500, "Erro ao cancelar inscrição no evento: " . $e->getMessage()));
                }
            }
        } else {
            $status = true;
            if (empty($subscribed)) {
                try {
                    $eventsModel->subscribeToEvent($eventId, $this->getUser()['loja_id']);
                    $mensagem = "Inscrição realizada com sucesso!";
                } catch (\Exception $e) {
                    exit($this->renderApiResponse(500, "Erro ao inscrever no evento: " . $e->getMessage()));
                }
            } else {
                try {
                    $eventsModel->reactivateSubscription($subscribed['inscricao_id']);
                    $mensagem = "Inscrição reativada com sucesso!";
                } catch (\Exception $e) {
                    exit($this->renderApiResponse(500, "Erro ao reativar inscrição no evento: " . $e->getMessage()));
                }
            }
        }
        exit($this->renderApiResponse(200, $mensagem, [
            'subscribed' => $status
        ]));
        return;
    }

    public function updateSubscription($post) {
        $this->validaAcesso(true);
        if (!$this->getLogado()) exit($this->renderApiResponse(401, "Usuário não autenticado.", [
            'redirect' => '/login?r=' . base64_encode(urlencode('/events/id/'.$post['eventId']))
        ]));
        $this->addTranslator('events/details');
        $translator = $this->getTranslator();
        $eventsModel = new Events();
        $eventId = filter_var($post['eventId'], FILTER_SANITIZE_NUMBER_INT);
        try {
            $eventsModel->updateUserSubscription(
                $eventId,
                $this->getUser()['loja_id'],
                $post['inputSubscriptionStatus'],
                !empty($post['inputUserTags']) ? $post['inputUserTags'] : [],
                $post['inputUserObservation'],
                isset($post['inputUserFeedback']) ? $post['inputUserFeedback'] : null
            );
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, $translator->translate("Erro ao atualizar inscrição no evento: ") . $e->getMessage()));
        }
        exit($this->renderApiResponse(200, $translator->translate("Inscrição atualizada com sucesso!")));
        return;
    }

    public function myEvents() {
        $this->addTranslator('events/myEvents');
        $this->addLayout($this->getTranslator()->translate("Meus Eventos"));
        $this->validaAcesso(true);
        $eventsModel = new Events();
        echo $this->view->render("events/myEvents", [
            'events' => $eventsModel->getUserEvents($this->getUser()['id']),
            'totals' => $eventsModel->getUserEventsTotals($this->getUser()['id']),
            'todayEvents' => $eventsModel->getUserEventsToday($this->getUser()['id'])
        ]);
        return;
    }

    public function create() {
        $this->addTranslator('events/details');
        $this->addLayout($this->getTranslator()->translate("Novo Evento"));
        $this->validaAcesso(true);
        $eventsModel = new Events();
        echo $this->view->render("events/create", [
            'advantages' => $eventsModel->getAdvantages()
        ]);
        return;
    }

    public function insert($post) {
        $this->validaAcesso(true);
        $eventsModel = new Events();
        $mensagens = [];
        try {
            $event = $eventsModel->createEvent($post, $this->getUser()['id']);
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, "Erro ao criar evento: " . $e->getMessage()));
        }
        // if (isset($post['eventAdvantages']) && is_array($post['eventAdvantages'])) {
        //     foreach($post['eventAdvantages'] as $advantageId) {
        //         try {
        //             $eventsModel->addEventAdvantage($event, $advantageId);
        //         } catch (\Exception $e) {
        //             $mensagens[] = "Erro ao adicionar vantagem ID {$advantageId}: " . $e->getMessage();
        //         }
        //     }
        // }
        if (isset($post['dates']) && is_array($post['dates'])) {
            foreach($post['dates'] as $date) {
                try {
                    $eventsModel->addEventDate($event, $date);
                } catch (\Exception $e) {
                    $mensagens[] = "Erro ao adicionar data {$date['day']}: " . $e->getMessage();
                }
            }
        }
        if (isset($post['prices']) && is_array($post['prices'])) {
            foreach($post['prices'] as $price) {
                try {
                    $eventsModel->addEventPrice($event, $price);
                } catch (\Exception $e) {
                    $mensagens[] = "Erro ao adicionar preço {$price['type']}: " . $e->getMessage();
                }
            }
        }
        try {
            $eventsModel->updateEventComplementaryInfo($event);
        } catch (\Exception $e) {
            $mensagens[] = "Erro ao atualizar informações complementares do evento: " . $e->getMessage();
        }

        try {
            $eventsModel->subscribeToEvent($event, $this->getUser()['loja_id']);
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, "Erro ao inscrever no evento: " . $e->getMessage()));
        }

        if (!empty($mensagens)) {
            exit($this->renderApiResponse(207, "Evento criado com algumas mensagens: " . implode("; ", $mensagens), [
                'eventId' => $event
            ]));
            return;
        }
        exit($this->renderApiResponse(200, "Evento criado com sucesso!", [
            'eventId' => $event
        ]));
        return;
    }

    public function edit($data) {
        $this->addTranslator('events/details');
        $this->validaAcesso(true);
        $dados = new Events();
        $event = $dados->getEventBasicInfo(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT), $this->getUser()['loja_id']);
        
        if (empty($event) || !$this->getLogado() || $this->getUser()['id'] != $event['evento_proprietario']) {
            header("Location: /error/404");
            return;
        }
        $this->addLayout($event['evento_nome']);
        echo $this->view->render("events/edit", [
            'event'         => $event,
            'user'          => $this->getUser(),
            'days'          => $dados->getEventDays(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT)),
            // 'advantages'    => $dados->getEventAdvantages(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT), false),
            'advantages'    => [],
            'prices'        => $dados->getEventPrices(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT)),
            'photos'        => $dados->getEventPhotos(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT))
        ]);
        return;
    }

    public function update($data) {
        $this->validaAcesso(true);
        $dados = new Events();
        $event = $dados->getEventBasicInfo(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT), $this->getUser()['loja_id']);
        if (empty($event) || !$this->getLogado() || $this->getUser()['id'] != $event['evento_proprietario']) {
            exit($this->renderApiResponse(404, "Evento não encontrado."));
            return;
        }
        $days = $dados->getEventDays(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT));
        $advantages = $dados->getEventAdvantages(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT));
        $prices = $dados->getEventPrices(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT));
        $photos = $dados->getEventPhotos(filter_var($data['eventId'], FILTER_SANITIZE_NUMBER_INT));

        try {
            $dados->updateEvent($data['eventId'], $data);
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, "Erro ao atualizar evento: " . $e->getMessage()));
        }

        $newDays = empty($data['dates']) || !is_array($data['dates']) ? [] : $data['dates'];
        try {
            foreach($days as $day) {
                if (!in_array($day['evento_data_dia'], array_column($newDays, 'day'))) {
                    $dados->removeEventDate($day['evento_data_id']);
                } else {
                    $dados->updateEventDate($day['evento_data_id'], $newDays[$day['evento_data_dia']]);
                }
                unset($newDays[$day['evento_data_dia']]);
            }
            foreach ($newDays as $date) $dados->addEventDate($data['eventId'], $date);
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, "Erro ao atualizar datas do evento: " . $e->getMessage()));
        }

        $newAdvantages = empty($data['eventAdvantages']) || !is_array($data['eventAdvantages']) ? [] : $data['eventAdvantages'];
        try {
            foreach($advantages as $advantage) {
                if (!in_array($advantage['eve_vant_id'], $newAdvantages)) {
                    $dados->removeEventAdvantage($advantage['eve_vant_id']);
                    $index = array_search($advantage['eve_vant_id'], $newAdvantages);
                    if ($index !== false) unset($newAdvantages[$index]);
                }
            }
            foreach ($newAdvantages as $advantageId) $dados->addEventAdvantage($data['eventId'], $advantageId);
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, "Erro ao atualizar informações complementares do evento: " . $e->getMessage()));
        }

        $newPrices = empty($data['prices']) || !is_array($data['prices']) ? [] : $data['prices'];
        try {
            $pricesOrder = [];
            foreach($prices as $key => $price) $pricesOrder[$price['evento_taxa_ordem']] = $key;
            foreach ($newPrices as $price) {
                if (isset($pricesOrder[$price['order']])) {
                    $dados->updateEventPrice($prices[$pricesOrder[$price['order']]]['evento_taxa_id'], $price);
                    unset($pricesOrder[$price['order']]);
                } else {
                    $dados->addEventPrice($data['eventId'], $price);
                }
            }
            foreach ($pricesOrder as $remainingPriceKey) $dados->removeEventPrice($prices[$remainingPriceKey]['evento_taxa_id']);
        } catch (\Exception $e) {
            exit($this->renderApiResponse(500, "Erro ao atualizar preços do evento: " . $e->getMessage()));
        }

        try {
            $dados->updateEventComplementaryInfo($data['eventId']);
        } catch (\Exception $e) {
            $mensagens[] = "Erro ao atualizar informações complementares do evento: " . $e->getMessage();
        }

        exit($this->renderApiResponse(200, "Alterações salvas!"));
        return;
    }

    public function delete() {
        return;
    }

}
