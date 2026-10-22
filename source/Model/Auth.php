<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;
use Source\Model\Helpers\PasswordChangedNotifier;
use Source\Model\Helpers\RememberMe;

class Auth extends Core {

    public function searchUser($email, $password) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $password = md5($password);
        $userStatement = $this->SQL->prepare('
            SELECT
                usuario_id id
            FROM
                usuarios
            WHERE
                usuario_email = :sentEmail 
            AND 
                usuario_senha = :sentPassword
        ');
        $userStatement->bindParam(':sentEmail', $email, PDO::PARAM_STR);
        $userStatement->bindParam(':sentPassword', $password, PDO::PARAM_STR);
        $userStatement->execute();
        $result = $userStatement->fetch();
        return $result['id'] ?? null;
    }

    public function changePassword($id, $password) {
        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $password = md5($password);
        $updateStatement = $this->SQL->prepare('
            UPDATE 
                usuarios 
            SET 
                usuario_senha = :password
            WHERE 
                usuario_id = :id
        ');
        $updateStatement->bindParam(':password', $password, PDO::PARAM_STR);
        $updateStatement->bindParam(':id', $id, PDO::PARAM_INT);
        $updated = $updateStatement->execute();
        if ($updated) {
            (new RememberMe($this->SQL))->revokeAll($id);
            PasswordChangedNotifier::notify($this->SQL, $id);
        }
        return $updated;
    }

}