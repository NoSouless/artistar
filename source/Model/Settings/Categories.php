<?php


namespace Source\Model\Settings;

use PDO;
use Source\Model\Settings;

class Categories extends Settings {

    public function getStoreCategories($storeId) {
        $select = $this->SQL->prepare(
            'SELECT
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
                ordem ASC,
                nome ASC
        ');

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);

        $select->execute();

        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($storeId, $categoryId) {
        $select = $this->SQL->prepare(
            'SELECT
                categoria_id AS id,
                categoria_nome AS nome,
                categoria_ativa AS ativa,
                categoria_publica AS publica,
                categoria_foto AS foto
            FROM
                categoria_loja
            WHERE
                categoria_loja = :storeId 
            AND
                categoria_id = :categoryId
            LIMIT 1'
        );

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $select->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $select->execute();
        return $select->fetch(PDO::FETCH_ASSOC);
    }

    public function searchCategoryByName($storeId, $name, $excludeId = null) {
        $select = $this->SQL->prepare(
            'SELECT
                categoria_id AS id,
                categoria_nome AS nome,
                categoria_ativa AS ativa,
                categoria_publica AS publica
            FROM
                categoria_loja
            WHERE
                categoria_loja = :storeId 
            AND
                LOWER(categoria_nome) = LOWER(:name)
            ' . ($excludeId ? 'AND categoria_id != :excludeId' : '') . '
            LIMIT 1'
        );

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $select->bindParam(':name', $name, PDO::PARAM_STR);
        if ($excludeId) {
            $select->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }

        $select->execute();

        return $select->fetch(PDO::FETCH_ASSOC);
    }

    public function insertCategory($storeId, $data) {
        $insert = $this->SQL->prepare(
            'INSERT INTO categoria_loja (categoria_loja, categoria_nome, categoria_ativa, categoria_publica, categoria_ordem)
             VALUES (:storeId, :nome, :ativa, :publica, (SELECT COALESCE(MAX(c.categoria_ordem), 0) + 1 FROM categoria_loja AS c WHERE c.categoria_loja = :storeId))'
        );

        $insert->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $insert->bindParam(':nome', $data['name'], PDO::PARAM_STR);
        $insert->bindParam(':ativa', $data['active'], PDO::PARAM_BOOL);
        $insert->bindParam(':publica', $data['public'], PDO::PARAM_BOOL);

        return $insert->execute();
    }

    public function updateCategory($storeId, $data) {
        $update = $this->SQL->prepare(
            'UPDATE 
                categoria_loja
             SET 
                categoria_nome = :nome,
                categoria_ativa = :ativa,
                categoria_publica = :publica
                ' . (!empty($data['foto']) ? ', categoria_foto = :foto' : '') . '
             WHERE 
                categoria_loja = :storeId 
            AND 
                categoria_id = :categoryId'
        );

        $update->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $update->bindParam(':categoryId', $data['id'], PDO::PARAM_INT);
        $update->bindParam(':nome', $data['name'], PDO::PARAM_STR);
        $update->bindParam(':ativa', $data['active'], PDO::PARAM_BOOL);
        $update->bindParam(':publica', $data['public'], PDO::PARAM_BOOL);
        if (!empty($data['foto'])) $update->bindParam(':foto', $data['foto'], PDO::PARAM_STR);

        return $update->execute();
    }

    public function reorderCategories($storeId, $order) {
        $this->SQL->beginTransaction();
        try {
            foreach ($order as $index => $categoryId) {
                $update = $this->SQL->prepare(
                    'UPDATE categoria_loja
                     SET categoria_ordem = :ordem
                     WHERE categoria_loja = :storeId AND categoria_id = :categoryId'
                );
                $update->bindParam(':storeId', $storeId, PDO::PARAM_INT);
                $update->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
                $update->bindParam(':ordem', $index, PDO::PARAM_INT);
                $update->execute();
            }
            $this->SQL->commit();
            return true;
        } catch (\Exception $e) {
            $this->SQL->rollBack();
        }
    }
}