<?php

namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Exception;
use Source\Core\Core;
use Source\Model\Auth;
use Source\Model\Helpers\GoogleRecaptcha;
use Source\Model\Helpers\RememberMe;
use Source\Model\Helpers\ValidationCode;

class authController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->getLayout()->setFooter('footer');
    }

    public function login($post) {
        $this->addTranslator('login');
        $translator = $this->getTranslator();
        if (!GoogleRecaptcha::verify($post['g-recaptcha-response'] ?? '', 'login')) 
            exit($this->renderApiResponse(400, $translator->translate('Verificação reCAPTCHA inválida. Confirme que você não é um robô.')));
        $auth = new Auth();
        $user = $auth->searchUser($post['email'], $post['password']);
        if (!$user) exit($this->renderApiResponse(404, $translator->translate('Usuário não encontrado!')));
        session_regenerate_id(true);
        $this->setUserLogonStatus($user);
        if (!empty($post['remember'])) (new RememberMe($this->getSQL()))->issue($user);
        exit($this->renderApiResponse(200, $translator->translate('Usuário encontrado!')));
    }

    public function logout(){
        (new RememberMe($this->getSQL()))->revoke();
        session_destroy();
        header("location: /");
    }

    public function newPassword() {
        $this->validaAcesso();
        echo $this->view->render("new-password", [
            'title' =>  'Redefinir senha - Artistar', 
            'redirect' => $this->getRedirect()
        ]);
        return;
    }

    public function changePassword($post) {
        $userId = $this->getUser()['id'];
        if (empty($userId)) exit($this->renderApiResponse(404, 'User not found'));
        try {
            $model = new Auth();
            if($model->changePassword($userId, $post['password'])) exit($this->renderApiResponse(200, 'Password changed successfully'));
            exit($this->renderApiResponse(404, 'Error changing password'));
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $e->getMessage()));
        }
    }

    public function resendCode($post) {
        try {
            $helper = new ValidationCode();
            if (isset($_SESSION['artistar']['password_change'])) {
                $userEmail = $helper->searchUser($_SESSION['artistar']['password_change']);
            } else {
                $userEmail = $this->getUser();
            }
            
            $validationCode = $helper->generateValidationCode(8);
            if ($helper->sendValidationEmail($userEmail['email'], $validationCode)) {
                $helper->updateValidationCode($userEmail['id'], $validationCode);
                exit($this->renderApiResponse(200, 'Passou'));
            } else {
                exit($this->renderApiResponse(404, 'Error sending email'));
            }
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $e->getMessage()));
        }
        
    }

}
