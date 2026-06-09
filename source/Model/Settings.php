<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;

class Settings extends Core {
    public function getStoreData($storeId) {
        $select = $this->SQL->prepare(
            'SELECT
                *
            FROM
                lojas loja
            WHERE
                loja.loja_id = :storeId
        ');

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);

        $select->execute();

        return $select->fetch(PDO::FETCH_ASSOC);
    }

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

    public function getStoreCategories($storeId) {
        $select = $this->SQL->prepare(
            'SELECT
                COALESCE(c.categoria_pai, 0) AS pai,
                c.categoria_id AS id,
                c.categoria_nome AS nome,
                c.categoria_ordem AS ordem,
                COALESCE(c.categoria_ativa, 0) AS ativa,
                COALESCE(c.categoria_publica, 0) AS publica,
                COUNT(p.produto_id) AS total_produtos
            FROM
                categoria_loja c
            LEFT JOIN
                categoria_produtos cp ON cp.categoria_produto_categoria = c.categoria_id
            LEFT JOIN
                produtos p ON cp.categoria_produto_produto = p.produto_id
            WHERE
                c.categoria_loja = :storeId
            GROUP BY
                id
            ORDER BY
                pai ASC,
                ordem ASC,
                nome ASC
        ');

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);

        $select->execute();

        return $select->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
    }

    public function updateStoreData($storeId, array $data) {
        if (empty($data)) {
            return false;
        }

        $fields = [];
        $params = [':storeId' => $storeId];

        foreach ($data as $column => $value) {
            $fields[] = $column . ' = :' . $column;
            $params[':' . $column] = $value;
        }

        $update = $this->SQL->prepare(
            'UPDATE lojas SET ' . implode(', ', $fields) . ' WHERE loja_id = :storeId'
        );

        return $update->execute($params);
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