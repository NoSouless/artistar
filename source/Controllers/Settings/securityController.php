<?php

namespace Source\Controllers\Settings;
use Source\Controllers\settingsController;
use Source\Model\Settings\Security;

class securityController extends settingsController {

    public function security() {
        $this->addTranslator('settings/security');
        $model = new Security();
        $this->addLayout($this->getTranslator()->translate('Senha e Segurança'));

        echo $this->view->render("settings/security", [
            'menu' => $this->renderMenu('security'),
            'devices' => $model->getDevices((int) ($this->getUser()['id'] ?? 0)),
        ]);
    }

    public function revokeDevice($post) {
        $this->addTranslator('settings/security');
        $translator = $this->getTranslator();
        $userId = (int) ($this->getUser()['id'] ?? 0);
        $model = new Security();
        if (!empty($post['all'])) {
            $model->revokeOtherDevices($userId);
        } elseif (!$model->revokeDevice($userId, (int) ($post['id'] ?? 0))) {
            exit($this->renderApiResponse(404, $translator->translate("Dispositivo não encontrado.")));
        }
        exit($this->renderApiResponse(200, $translator->translate("Dispositivo desconectado.")));
    }

    public function updateSecurity($post) {
        $this->addTranslator('settings/security');
        $translator = $this->getTranslator();
        $userId = !empty($this->getUser()['id']) ? (int) $this->getUser()['id'] : 0;
        $current = (string) ($post['senha-atual'] ?? '');
        $new = (string) ($post['nova-senha'] ?? '');
        $confirm = (string) ($post['confirmacao-senha'] ?? '');

        if (empty($userId)) exit($this->renderApiResponse(400, $translator->translate("Usuário não encontrado.")));
        if ($current === '' || $new === '') exit($this->renderApiResponse(400, $translator->translate("Preencha todos os campos.")));
        if (strlen($new) < 8) exit($this->renderApiResponse(400, $translator->translate("A nova senha deve ter no mínimo 8 caracteres.")));
        if ($new !== $confirm) exit($this->renderApiResponse(400, $translator->translate("A confirmação não corresponde à nova senha.")));

        $model = new Security();
        if (!$model->checkPassword($userId, $current)) exit($this->renderApiResponse(400, $translator->translate("Senha atual incorreta.")));
        if ($current === $new) exit($this->renderApiResponse(400, $translator->translate("A nova senha deve ser diferente da atual.")));
        if (!$model->updatePassword($userId, $new)) exit($this->renderApiResponse(500, $translator->translate("Erro ao alterar a senha.")));

        session_regenerate_id(true);
        exit($this->renderApiResponse(200, $translator->translate("Senha alterada com sucesso.")));
    }

}
