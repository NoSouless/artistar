<?php

namespace Source\Controllers;

use Exception;
use CoffeeCode\Router\Router;
use Source\Core\Core;
use Source\Model\Register;
use Source\Model\Helpers\GoogleRecaptcha;
use Source\Model\Helpers\ValidationCode;

class registerController extends Core {

    public function __construct($router = ROOT) {
        $this->setEnableRedirect(false);
        parent::__construct($router);
        $this->getLayout()->setFooter('footer');
        $this->addLayout();
    }

    public function home() {
        $this->addTranslator('register/home');
        $this->addLayout($this->getTranslator()->translate("Cadastro"));

        echo $this->view->render("register/home");
        return;
    }

    public function insertStore($post) {
        $this->addTranslator('register/home');
        $translator = $this->getTranslator();
        if (!GoogleRecaptcha::verify($post['g-recaptcha-response'] ?? '', 'register'))
            exit($this->renderApiResponse(400, $translator->translate('Verificação reCAPTCHA inválida. Confirme que você não é um robô.')));
        try { 
            $model = new Register();
            $qtd = $model->verifyIfEmailAndUsernameAreValid($post['email'], $post['user']);
            $errors = [];
            if ($qtd['qtd_storeUsername'] > 0) $errors[] = [
                'field' => 'username', 
                'message' => $translator->translate('Nome de usuário já em uso por uma loja')
            ];

            if ($qtd['qtd_userUsername'] > 0) $errors[] = [
                'field' => 'username', 
                'message' => $translator->translate('Nome de usuário já em uso por um usuário')
            ];

            if ($qtd['qtd_userEmail'] > 0) $errors[] = [
                'field' => 'email', 
                'message' => $translator->translate('Email já em uso por um usuário')
            ];

            if (strlen($post['user']) < 3) $errors[] = [
                'field' => 'username', 
                'message' => $translator->translate('O nome de usuário deve ter pelo menos 3 caracteres')
            ];

            if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) $errors[] = [
                'field' => 'email', 
                'message' => $translator->translate('O email não tem um formato válido')
            ];

            if (!empty($errors)) exit($this->renderApiResponse(403, 'Validation failed', $errors));

            $userId = $model->insertUser($post['user'], $post['complete_user'], $post['email'], $post['password']);
            if (!$userId) exit($this->renderApiResponse(500, 'Failed to insert user'));
            $storeId = $model->insertStore($post['user'], $post['complete_user'], $userId);
            if (!$storeId) {
                $model->deleteUser($userId);
                exit($this->renderApiResponse(500, $translator->translate('Falha ao criar loja')));
            }
            
            $this->setUserLogonStatus($userId);

            exit($this->renderApiResponse(200, $translator->translate('Loja criada com sucesso')));
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $e->getMessage()));
        }
    }

    public function validate() {
        $this->addTranslator('register/home');
        $translator = $this->getTranslator();
        $this->validaAcesso(false);
        if ($this->getUser()['email_validado'] == 1) {
            header("location: /");
            return;
        } else {
            $createdAt = strtotime($this->getUser()['envio_validacao']);
            $now = strtotime(date("Y-m-d H:i:s"));
            $diff = ($now - $createdAt) / 3600;
            if ($diff > 1) {
                $helper = new ValidationCode();
                $helper->setTranslator($translator);
                $validationCode = $helper->generateValidationCode(8);
                if ($helper->sendValidationEmail($this->getUser()['email'], $validationCode))
                    $helper->updateValidationCode($this->getUser()['id'], $validationCode);
            }
        }
        $this->addLayout($this->getTranslator()->translate("Confirmação de Email"));
        echo $this->view->render("register/validate-email");
        return;
    }

    public function validateCode($post) {
        $this->addTranslator('register/home');
        $translator = $this->getTranslator();
        $model = new Register();
        if ($model->validateCode($this->getUser()['id'], $post['code'])) {
            $model->updateEmailValidationStatus($this->getUser()['id']);
            exit($this->renderApiResponse(200, $translator->translate('Email validated successfully')));
        } else {
            exit($this->renderApiResponse(404, $translator->translate('Invalid code')));
        }
    }

    public function validateUser($post) {

        $this->addTranslator('register/home');
        $translator = $this->getTranslator();
        
        if (empty($post['user'])) exit($this->renderApiResponse(403, $translator->translate('Sem nome de usuário inserido')));
        if (strlen($post['user']) < 3) exit($this->renderApiResponse(403, $translator->translate('O nome de usuário deve ter pelo menos 3 caracteres')));
        $model = new Register();

        $username = $model->verifyIfEmailAndUsernameAreValid('', $post['user']);
        if ($username['qtd_storeUsername'] < 1 && $username['qtd_userUsername'] < 1) exit($this->renderApiResponse(200, $translator->translate('Nome de usuário está disponível')));
        exit($this->renderApiResponse(404, $translator->translate('Nome de usuário está indisponível')));

        return;
    }

    public function validateEmail($post) {

        $this->addTranslator('register/home');
        $translator = $this->getTranslator();

        if (empty($post['email'])) exit($this->renderApiResponse(403, $translator->translate('Sem email inserido')));
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) exit($this->renderApiResponse(403, $translator->translate('O email não tem um formato válido')));
        $model = new Register();

        $email = $model->verifyIfEmailAndUsernameAreValid($post['email'], '');
        if ($email['qtd_userEmail'] < 1) exit($this->renderApiResponse(200, $translator->translate('Email está disponível')));
        exit($this->renderApiResponse(404, $translator->translate('Email está indisponível')));

        return;
    }

}
