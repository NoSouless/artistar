<?php


namespace Source\Model\Settings;

use PDO;
use Source\Model\Settings;

class Profile extends Settings {

    public function getUserData($userId) {
        $select = $this->SQL->prepare(
            'SELECT
                user.usuario_id,
                user.usuario_nome_completo,
                user.usuario_email,
                user.usuario_foto
            FROM
                usuarios user
            WHERE
                user.usuario_id = :userId
        ');

        $select->bindParam(':userId', $userId, PDO::PARAM_INT);

        $select->execute();

        return $select->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserData($userId, array $data) {
        if (empty($data)) {
            return false;
        }

        $fields = [];
        $params = [':userId' => $userId];

        foreach ($data as $column => $value) {
            $fields[] = $column . ' = :' . $column;
            $params[':' . $column] = $value;
        }

        $update = $this->SQL->prepare(
            'UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE usuario_id = :userId'
        );

        return $update->execute($params);
    }


}