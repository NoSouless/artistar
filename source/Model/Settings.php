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

}