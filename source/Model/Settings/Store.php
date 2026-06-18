<?php


namespace Source\Model\Settings;

use PDO;
use Source\Model\Settings;

class Store extends Settings {

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


}