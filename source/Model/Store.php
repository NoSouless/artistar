<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;

class Store extends Core {

    public function getStoreData($data) {
        $where = [];
        if (isset($data['storeId'])) $where[] = 'loja.loja_id = :storeId';
        if (isset($data['friendlyUrl'])) $where[] = 'loja.loja_nome_unico = :friendlyUrl';
        if (empty($where)) return null;

        $select = $this->SQL->prepare(
            'SELECT
                loja.loja_id codigo,
                loja.loja_nome nome,
                loja.loja_nome_unico nome_unico,
                loja.loja_descricao descricao,
                loja.loja_foto foto,
                loja.loja_banner banner,
                COALESCE(loja.loja_moeda, "R$") moeda,
                (SELECT COUNT(*) FROM produtos WHERE produto_loja = loja.loja_id AND produto_ativo = 1) produtos
            FROM
                lojas loja
            WHERE
                ' . implode(' AND ', $where)
        );

        if (isset($data['storeId'])) $select->bindParam(':storeId', $data['storeId'], PDO::PARAM_INT);
        if (isset($data['friendlyUrl'])) $select->bindParam(':friendlyUrl', $data['friendlyUrl'], PDO::PARAM_STR);

        $select->execute();

        return $select->fetch(PDO::FETCH_ASSOC);
    }

    public function getStorePublicCategories($storeId) {
        $select = $this->SQL->prepare(
            'SELECT
                c.categoria_id AS id,
                c.categoria_nome AS nome,
                c.categoria_foto AS foto
            FROM
                categoria_loja c
            WHERE
                c.categoria_loja = :storeId
            AND
                c.categoria_publica = 1
            AND
                c.categoria_ativa = 1
            GROUP BY
                id
            ORDER BY
                c.categoria_ordem ASC,
                nome ASC
        ');

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $select->execute();

        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoreFollowersCount($storeId) {
        $storeId = (int) $storeId;
        if ($storeId < 1) return 0;

        $select = $this->SQL->prepare('
            SELECT
                COUNT(*) total
            FROM
                lojas_seguidores
            WHERE
                loja_id = :storeId
            LIMIT 1
        ');
        
        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $select->execute();

        return (int) ($select->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function checkIfUserFollowsStore($storeId, $userId) {
        $storeId = (int) $storeId;
        $userId = (int) $userId;

        if ($storeId < 1 || $userId < 1) return null;

        $select = $this->SQL->prepare('
            SELECT
                *
            FROM
                lojas_seguidores
            WHERE
                loja_id = :storeId
            AND
                usuario_id = :userId
            LIMIT 1
        ');

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $select->bindParam(':userId', $userId, PDO::PARAM_INT);
        $select->execute();


        return $select->fetch(PDO::FETCH_ASSOC);
    }

    public function followStore($storeId, $userId) {
        $storeId = (int) $storeId;
        $userId = (int) $userId;

        if ($storeId < 1 || $userId < 1) return false;

        $insert = $this->SQL->prepare('
            INSERT INTO lojas_seguidores
                (
                    loja_id,
                    usuario_id,
                    loja_seguidor_dt
                )
            VALUES
                (
                    :storeId,
                    :userId,
                    NOW()
                )
        ');

        $insert->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $insert->bindParam(':userId', $userId, PDO::PARAM_INT);

        if (!$insert->execute()) return false;

        return $this->SQL->lastInsertId();
    }

    public function getShowcaseProductOrder($storeId, $filters = [], $groupBySelected = false) {
        $where = [];
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = trim($filters['search']);
            $search = str_replace("'", "\'", $search);
            $searchTerms = explode(' ', $search);
            $search = '';
            foreach ($searchTerms as $term) {
                $term = trim($term);
                if (!empty($term)) $search .= "+{$term}* ";
            }
            if (!empty($search)) $where[] = "(MATCH (produto_nome, produto_palavras_chave, produto_descricao, produto_codigo_interno) AGAINST ('{$search}' IN BOOLEAN MODE))";
        }
        if (isset($filters['only_in_showcase']) && !empty($filters['only_in_showcase'])) $where[] = "ordenacao.produto_id IS NOT NULL";
        if (isset($filters['only_out_showcase']) && !empty($filters['only_out_showcase'])) $where[] = "ordenacao.produto_id IS NULL";
        if (isset($filters['collection_id'])) $where[] = "COALESCE(ordenacao.colecao_id, 0) = {$filters['collection_id']}";
        if (isset($filters['category_id']) && !empty($filters['category_id'])) $where[] = "cp.categoria_produto_categoria = {$filters['category_id']}";

        $where = !empty($where) ? ' AND ' . implode(' AND ', $where) : '';

        $query = '
            SELECT
                '.($groupBySelected ? 'IF(ordenacao.produto_id IS NULL, "unselected", "selected") selecionado,' : '').'
                p.produto_id id,
                p.produto_nome nome,
                p.produto_thumbnail thumbnail,
                p.produto_valor valor_original,
                p.produto_valor - p.produto_valor_desconto valor,
                p.produto_valor_desconto valor_desconto,
                ordenacao.produto_ordenacao_id ordenacao_id,
                COALESCE(ordenacao.produto_ordenacao_ordem, 0) ordem,
                p.produto_palavras_chave palavras_chave
            FROM
                produtos p
            LEFT JOIN 
                produtos_ordenacao ordenacao ON ordenacao.produto_id = p.produto_id
            LEFT JOIN
                categoria_produtos cp ON cp.categoria_produto_produto = p.produto_id
            WHERE
                p.produto_loja = :storeId
            AND
                p.produto_ativo = 1
            '.$where.'
            GROUP BY
                p.produto_id
            ORDER BY
                ordem ASC, nome ASC
        ';

        $select = $this->SQL->prepare($query);
        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $select->execute();

        if ($groupBySelected) {
            return $select->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
        } else {
            return $select->fetchAll(PDO::FETCH_ASSOC);
        }
        
    }

    public function updateShowcaseProductOrder($orderId, $newOrder) {
        $update = $this->SQL->prepare('
            UPDATE 
                produtos_ordenacao
            SET 
                produto_ordenacao_ordem = :newOrder
            WHERE 
                produto_ordenacao_id = :orderId
        ');

        $update->bindParam(':newOrder', $newOrder, PDO::PARAM_INT);
        $update->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        return $update->execute();
    }

    public function insertShowcaseProductOrder($productId, $order, $collectionId = 0) {
        $insert = $this->SQL->prepare('
            INSERT INTO 
                produtos_ordenacao (
                    produto_id, 
                    produto_ordenacao_ordem, 
                    colecao_id
                )
            VALUES (
                :productId, 
                :order, 
                :collectionId
            )
        ');
        $insert->bindParam(':productId', $productId, PDO::PARAM_INT);
        $insert->bindParam(':order', $order, PDO::PARAM_INT);
        $insert->bindParam(':collectionId', $collectionId, empty($collectionId) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $insert->execute();
        return $this->SQL->lastInsertId();
    }

    public function deleteShowcaseProductOrder($orderId) {
        $delete = $this->SQL->prepare('
            DELETE FROM 
                produtos_ordenacao
            WHERE 
                produto_ordenacao_id = :orderId
        ');
        $delete->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        return $delete->execute();
    }
}