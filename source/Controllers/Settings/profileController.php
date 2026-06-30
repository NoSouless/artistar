<?php

namespace Source\Controllers\Settings;
use Exception;
use Source\Controllers\settingsController;
use Source\Model\Helpers\Storage;
use Source\Model\Settings\Profile;

class profileController extends settingsController {

    public function profile() {
        $this->addTranslator('settings/profile');
        $userId = !empty($this->getUser()['id']) ? (int) $this->getUser()['id'] : 0;
        $model = new Profile();
        $user = $model->getUserData($userId);  
        $this->addLayout($this->getTranslator()->translate('Editar Perfil'));

        echo $this->view->render("settings/profile", [
            'user' => $user,
            'menu' => $this->renderMenu('profile'),
        ]);
    }

    public function updateProfile($post) {
        $this->addTranslator('settings/profile');
        $userId = !empty($this->getUser()['id']) ? (int) $this->getUser()['id'] : 0;
        $model = new Profile();
        $user = $model->getUserData($userId);

        if (empty($userId) || empty($user)) {
            exit($this->renderApiResponse(400, $this->getTranslator()->translate("Usuário não encontrado.")));
        }

        $updateData = [
            'usuario_nome_completo' => trim((string) ($post['nome'] ?? '')),
        ];

        try {
            if (!empty($_FILES['profilePhoto']['tmp_name'])) {
                $storagePhoto = new Storage();
                $folder = 'uploads/users/'.$userId.'/';
                $imageName = $folder.'profile.'. pathinfo($_FILES['profilePhoto']['name'] ?? '', PATHINFO_EXTENSION);
                // $storagePhoto->cleanFolderFromBucket($folder);
                $newLogo = $storagePhoto->sendFileToBucket($_FILES['profilePhoto']['tmp_name'], $imageName, true)['message'];
                $updateData['usuario_foto'] = $newLogo;
            }
            $model->updateUserData($userId, $updateData);
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $this->getTranslator()->translate("Erro ao atualizar o perfil: ") . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, $this->getTranslator()->translate("Perfil atualizado com sucesso.")));
    }

}