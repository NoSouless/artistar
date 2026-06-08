<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;

class Settings extends Core {
    public function getStoreData($storeId) {
        $select = $this->SQL->prepare(
            'SELECT
                loja.loja_id codigo,
                loja.loja_nome nome,
                loja.loja_nome_unico nome_unico,
                loja.loja_descricao descricao,
                loja.loja_foto foto,
                COALESCE(loja.loja_moeda, "R$") moeda
            FROM
                lojas loja
            WHERE
                loja.loja_id = :storeId
        ');

        $select->bindParam(':storeId', $storeId, PDO::PARAM_INT);

        $select->execute();

        return $select->fetch(PDO::FETCH_ASSOC);
    }
}