<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;

class Stock extends Core {

    public function buildWhereStock($search = '', $filter = []) {
        $where = [];
        $search = trim($search);
        $search = str_replace("'", "\'", $search);
        //Separar cada palavra da busca por espaço e adicionar o operador booleano
        $searchTerms = explode(' ', $search);
        $search = '';
        foreach ($searchTerms as $term) {
            $term = trim($term);
            if (!empty($term)) {
                $search .= "+{$term}* ";
            }
        }
        if (!empty($search)) $where[] = "(MATCH (produto_nome, produto_palavras_chave, produto_descricao, produto_codigo_interno) AGAINST ('{$search}' IN BOOLEAN MODE))";
        if (!empty($filter['status'])) {
            if ($filter['status'] == 'active') {
                $where[] = "produto_ativo = '1'";
            } elseif ($filter['status'] == 'inactive') {
                $where[] = "produto_ativo = '0'";
            }
        }
        if (!empty($filter['category'])) {
            $categories = array_map('intval', $filter['category']);
            if (!empty($categories)) {
                $placeholders = implode(',', $categories);
                $where[] = "produto_id IN (SELECT categoria_produto_produto FROM categoria_produtos WHERE categoria_produto_categoria IN ({$placeholders}))";
            }
        }
        if (!empty($filter['price'])) {
            $price = desconverteMoedaReal($filter['price']);
            $where[] = "produto_valor = {$price}";
        }
        if (!empty($filter['cost'])) {
            $cost = desconverteMoedaReal($filter['cost']);
            $where[] = "produto_custo = {$cost}";
        }
        if (!empty($filter['discount'])) {
            $discount = desconverteMoedaReal($filter['discount']);
            $where[] = "produto_valor_desconto = {$discount}";
        }
        if (!empty($filter['real_price'])) {
            $realPrice = desconverteMoedaReal($filter['real_price']);
            $where[] = "(produto_valor - produto_valor_desconto) = {$realPrice}";
        }
        if (!empty($filter['stock']) && $filter['stock'] !== '') {
            $stock = (int)$filter['stock'];
            $where[] = "produto_estoque = {$stock}";
        }
        if (!empty($filter['min_stock']) && $filter['min_stock'] !== '') {
            $minStock = (int)$filter['min_stock'];
            $where[] = "produto_estoque_minimo = {$minStock}";
        }

        if (!empty($filter['stock_status'])) {
            switch ($filter['stock_status']) {
                case 'good':
                    $where[] = "produto_estoque >= produto_estoque_minimo AND produto_estoque > 0 AND produto_ativo = 1";
                    break;
                case 'low':
                    $where[] = "produto_estoque < produto_estoque_minimo AND produto_estoque > 0 AND produto_ativo = 1";
                    break;
                case 'out':
                    $where[] = "produto_estoque <= 0 AND produto_ativo = 1";
                    break;
                case 'dead':
                    $where[] = "COALESCE(produto_ativo, 0) = 0";
                    break;
            }
        }

        return !empty($where) ? 'AND ' . implode(' AND ', $where) : '';
    }

    public function getStocks($store, $where = '', $match = '') {
        try {
            $stmt = $this->SQL->prepare("
                SELECT 
                    COUNT(IF(produto_estoque >= produto_estoque_minimo AND produto_estoque > 0 AND produto_ativo = 1, 1, NULL)) AS goodStock,
                    COUNT(IF(produto_estoque < produto_estoque_minimo AND produto_estoque > 0 AND produto_ativo = 1, 1, NULL)) AS lowStock,
                    COUNT(IF(produto_estoque <= 0 AND produto_ativo = 1, 1, NULL)) AS outOfStock,
                    COUNT(IF(produto_ativo = 0, 1, NULL)) AS deadStock,
                    COUNT(produto_id) AS totalProducts
                FROM
                    produtos
                WHERE
                    produto_loja = :store
                {$where}
            ");
            $stmt->bindValue(":store", $store);
            // exit($stmt->queryString);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return [
                    'goodStock' => 0,
                    'lowStock' => 0,
                    'outOfStock' => 0,
                    'deadStock' => 0
                ];
            }
            return $result;
        } catch (\PDOException $e) {
            return [
                'goodStock' => 0,
                'lowStock' => 0,
                'outOfStock' => 0,
                'deadStock' => 0,
                'totalProducts' => 0
            ];
        }
    }

    public function getCategories($store) {
        $stmt = $this->SQL->prepare("
            SELECT 
                categoria_id id,
                categoria_nome nome,
                categoria_cor cor 
            FROM 
                categoria_loja 
            WHERE 
                categoria_ativa = 1
            AND
                categoria_loja = :store
            ORDER BY 
                nome ASC
        ");
        $stmt->bindValue(":store", $store);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getOrderList($sort) {
        $tradutor = $this->getTranslator();
        $orderList = [
            'name_asc' => [
                'label' => $tradutor->translate('Nome (A-Z)'),
                'value' => 'produto_nome ASC, produto_id ASC'
            ],
            'name_desc' => [
                'label' => $tradutor->translate('Nome (Z-A)'),
                'value' => 'produto_nome DESC, produto_id DESC'
            ],
            'price_asc' => [
                'label' => $tradutor->translate('Preço (Menor para Maior)'),
                'value' => 'produto_valor ASC, produto_id ASC'
            ],
            'price_desc' => [
                'label' => $tradutor->translate('Preço (Maior para Menor)'),
                'value' => 'produto_valor DESC, produto_id DESC'
            ],
            'discount_asc' => [
                'label' => $tradutor->translate('Desconto (Menor para Maior)'),
                'value' => 'produto_valor_desconto ASC, produto_id ASC'
            ],
            'discount_desc' => [
                'label' => $tradutor->translate('Desconto (Maior para Menor)'),
                'value' => 'produto_valor_desconto DESC, produto_id DESC'
            ],
            'final_price_asc' => [
                'label' => $tradutor->translate('Preço Atual (Menor para Maior)'),
                'value' => '(produto_valor - produto_valor_desconto) ASC, produto_id ASC'
            ],
            'final_price_desc' => [
                'label' => $tradutor->translate('Preço Atual (Maior para Menor)'),
                'value' => '(produto_valor - produto_valor_desconto) DESC, produto_id DESC'
            ],
            'date_asc' => [
                'label' => $tradutor->translate('Data de Cadastro (Mais Antigo)'),
                'value' => 'produto_data_cadastro ASC, produto_id ASC'
            ],
            'date_desc' => [
                'label' => $tradutor->translate('Data de Cadastro (Mais Recente)'),
                'value' => 'produto_data_cadastro DESC, produto_id DESC'
            ],
            'last_sale_asc' => [
                'label' => $tradutor->translate('Data da Última Venda (Mais Antigo)'),
                'value' => 'produto_ultima_venda ASC, produto_id ASC'
            ],
            'last_sale_desc' => [
                'label' => $tradutor->translate('Data da Última Venda (Mais Recente)'),
                'value' => 'produto_ultima_venda DESC, produto_id DESC'
            ],
            'stock_asc' => [
                'label' => $tradutor->translate('Estoque (Menor para Maior)'),
                'value' => 'produto_estoque ASC, produto_id ASC'
            ],
            'stock_desc' => [
                'label' => $tradutor->translate('Estoque (Maior para Menor)'),
                'value' => 'produto_estoque DESC, produto_id DESC'
            ]
        ];
            
        foreach($orderList as $key => $value) {
            if ($key === $sort) {
                $orderList[$key]['selected'] = true;
            } else {
                $orderList[$key]['selected'] = false;
            }
        }
        return $orderList;
    }
        
    public function buildOrderBy($orderList, $order = 'name_asc') {
        $orderBy = 'produto_nome ASC'; // Default order
        if (isset($orderList[$order])) {
            $orderBy = $orderList[$order]['value'];
        }
        return $orderBy;
    }

    public function getProducts($store, $pagination = [], $where = '', $order = '') {
        try {
            $stmt = $this->SQL->prepare("
                SELECT 
                    produto_id id,
                    produto_nome nome,
                    produto_thumbnail thumbnail,
                    produto_valor valor,
                    produto_valor_desconto valor_desconto,
                    produto_estoque estoque,
                    produto_estoque_minimo estoque_minimo,
                    produto_palavras_chave palavras_chave,
                    produto_ativo ativo
                FROM 
                    produtos 
                WHERE 
                    produto_loja = :store
                {$where}
                ORDER BY
                    {$order}
                LIMIT :offset, :limit
            ");
            $stmt->bindValue(":store", $store);
            $stmt->bindValue(":offset", (int) $pagination['offset'], PDO::PARAM_INT);
            $stmt->bindValue(":limit", (int) $pagination['limit'], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function insertProduct($data) {
        $stmt = $this->SQL->prepare("
            INSERT INTO produtos (
                produto_data_cadastro,
                produto_nome, 
                produto_codigo_interno,
                produto_descricao, 
                produto_valor, 
                produto_valor_desconto, 
                produto_custo, 
                produto_estoque, 
                produto_estoque_minimo, 
                produto_loja, 
                produto_ativo, 
                produto_palavras_chave
            ) VALUES ( 
                NOW(),
                :produto_nome, 
                :produto_codigo_interno,
                :produto_descricao, 
                :produto_valor, 
                :produto_valor_desconto, 
                :produto_custo, 
                :produto_estoque, 
                :produto_estoque_minimo, 
                :produto_loja, 
                :produto_ativo, 
                :produto_palavras_chave
            )
        ");
        $stmt->execute($data);
        return $this->SQL->lastInsertId();
    }

    public function updateThumbnail($thumbnail, $productId) {
        $stmt = $this->SQL->prepare("
            UPDATE produtos SET
                produto_thumbnail = :produto_thumbnail
            WHERE
                produto_id = :produto_id
        ");
        $stmt->bindValue(":produto_thumbnail", $thumbnail);
        $stmt->bindValue(":produto_id", $productId);
        $stmt->execute();
    }

    public function insertNewStoreCategory($category, $store) {
        if (empty($category)) return NULL;
        $stmt = $this->SQL->prepare("
            INSERT INTO categoria_loja (categoria_loja, categoria_nome, categoria_ativa)
            VALUES (:store, :category, 1)
        ");
        $stmt->execute([
            ':store' => $store,
            ':category' => $category
        ]);
        return $this->SQL->lastInsertId();
    }

    public function insertProductCategories($categories, $store, $productId) {
        if (empty($categories)) return [];
        $trueCategories = [];
        foreach ($categories as $key => $category) {
            if (strpos($category, '{existing}') === 0) {
                $trueCategories[] = str_replace('{existing}', '', $category);
            } else {
                $trueCategories[] = $this->insertNewStoreCategory($category, $store);
            }
        }
        unset($categories);  

        $trueCategories = array_map('intval', $trueCategories);
        $trueCategories = array_filter($trueCategories);

        $placeholders = [0];
        $params = [':store' => $store];
        foreach ($trueCategories as $i => $cat) {
            $ph = ":cat{$i}";
            $placeholders[] = $ph;
            $params[$ph] = $cat;
        }

        $sql = "
            SELECT 
                categoria_id 
            FROM 
                categoria_loja
            WHERE 
                categoria_loja = :store
            AND 
                categoria_id IN (" . implode(',', $placeholders) . ")
        ";

        $existingCategories = $this->SQL->prepare($sql);
        $existingCategories->execute($params);
        $existingCategories = $existingCategories->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($existingCategories))  {
            $stmt = $this->SQL->prepare("
                INSERT INTO categoria_produtos (categoria_produto_categoria, categoria_produto_produto)
                VALUES (:category, :productId)
            ");
            foreach ($existingCategories as $category) {
                $stmt->execute([
                    ':category' => $category,
                    ':productId' => $productId,
                ]);
            }
        }
    
        return true;     
    }

    public function getProductById($productId, $store) {
        $stmt = $this->SQL->prepare("
            SELECT 
                produto.produto_id id,
                produto.produto_nome nome,
                produto.produto_descricao descricao,
                produto.produto_valor valor,
                produto.produto_valor_desconto valor_desconto,
                produto.produto_custo custo,
                produto.produto_estoque estoque,
                produto.produto_estoque_minimo estoque_minimo,
                produto.produto_loja loja,
                produto.produto_ativo ativo,
                produto.produto_thumbnail thumbnail,
                produto.produto_codigo_interno identificacao_interno,
                produto.produto_palavras_chave palavras_chave,
                GROUP_CONCAT(categorias.categoria_produto_categoria) AS categoriasIds
            FROM 
                produtos produto
            LEFT JOIN
                categoria_produtos categorias ON produto.produto_id = categorias.categoria_produto_produto
            WHERE 
                produto.produto_id = :productId
            AND
                produto.produto_loja = :store
        ");
        $stmt->bindValue(":productId", $productId);
        $stmt->bindValue(":store", $store);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProduct($productId, $store, $data) {
        $stmt = $this->SQL->prepare("
            UPDATE produtos SET
                produto_nome = :produto_nome,
                produto_descricao = :produto_descricao,
                produto_valor = :produto_valor,
                produto_valor_desconto = :produto_valor_desconto,
                produto_custo = :produto_custo,
                produto_estoque = :produto_estoque,
                produto_estoque_minimo = :produto_estoque_minimo,
                produto_ativo = :produto_ativo,
                produto_palavras_chave = :produto_palavras_chave,
                produto_codigo_interno = :produto_codigo_interno
            WHERE
                produto_id = :produto_id
            AND
                produto_loja = :produto_loja
        ");
        $data[':produto_id'] = $productId;
        $data[':produto_loja'] = $store;
        return $stmt->execute($data);
    }

    public function addNewProductCategories($categories, $store, $productId) {
        if (empty($categories)) return [];
        $trueCategories = [];
        $insertCategories = [];
        foreach ($categories as $category) {
            
            if (strpos($category, '{selected}') === 0) {
                $categoryId = str_replace('{selected}', '', $category);
                $categoryId = str_replace('{existing}', '', $categoryId);
                $trueCategories[] = $categoryId;
                continue;
            }
            $categoryId = NULL;
            if (strpos($category, '{existing}') === 0) {
                $categoryId = str_replace('{existing}', '', $category);
            } else {
                $categoryId = $this->insertNewStoreCategory($category, $store);
                $insertCategories[] = $categoryId;
            }
            $insertCategories[] = $categoryId;
            $trueCategories[] = $categoryId;
        }
        
        unset($categories);  

        $trueCategories = array_map('intval', $trueCategories);
        $trueCategories = array_filter($trueCategories);

        $stmt = $this->SQL->prepare("
            INSERT INTO categoria_produtos (categoria_produto_categoria, categoria_produto_produto)
            VALUES (:category, :productId)
        ");
        foreach ($insertCategories as $category) {
            $stmt->execute([
                ':category' => $category,
                ':productId' => $productId,
            ]);
        }

        if (empty($trueCategories)) $trueCategories[] = 0;

        $stmt = $this->SQL->prepare("
            DELETE FROM 
                categoria_produtos 
            WHERE 
                categoria_produto_produto = :productId 
            AND 
                categoria_produto_categoria NOT IN (" . implode(',', $trueCategories) . ")
        ");
        $stmt->execute([':productId' => $productId]);

        return true;
    }

    public function duplicateProduct($productId, $store, $copy = 'Cópia') {
        $stmt = $this->SQL->prepare("
            INSERT INTO produtos (
                produto_nome, 
                produto_descricao, 
                produto_valor, 
                produto_valor_desconto, 
                produto_custo, produto_estoque, 
                produto_estoque_minimo, produto_ativo, 
                produto_palavras_chave, 
                produto_codigo_interno, 
                produto_loja
            )
            SELECT 
                CONCAT(:copy, ' - ', produto_nome), 
                produto_descricao, 
                produto_valor, 
                produto_valor_desconto, 
                produto_custo, 
                produto_estoque, 
                produto_estoque_minimo, 
                produto_ativo, 
                produto_palavras_chave, 
                produto_codigo_interno, 
                :store
            FROM 
                produtos
            WHERE 
                produto_id = :productId 
            AND 
                produto_loja = :store
        ");
        $stmt->bindValue(":productId", $productId);
        $stmt->bindValue(":store", $store);
        $stmt->bindValue(":copy", $copy);
        $stmt->execute();

        return $this->SQL->lastInsertId();
    }

    public function duplicateProductCategories($productId, $newProductId, $store) {
        $stmt = $this->SQL->prepare("
            INSERT INTO 
                categoria_produtos (categoria_produto_categoria, categoria_produto_produto)
            SELECT 
                categoria_produto_categoria, 
                :newProductId
            FROM 
                categoria_produtos
            WHERE 
                categoria_produto_produto = :productId
        ");
        $stmt->bindValue(":productId", $productId);
        $stmt->bindValue(":newProductId", $newProductId);
        return $stmt->execute();
    }

    public function deleteProduct($productId, $store) {
        $stmt = $this->SQL->prepare("
            DELETE FROM 
                produtos 
            WHERE 
                produto_id = :productId 
            AND 
                produto_loja = :store
        ");
        $stmt->bindValue(":productId", $productId);
        $stmt->bindValue(":store", $store);
        return $stmt->execute();
    }

    public function deleteProductCategories($productId) {
        $stmt = $this->SQL->prepare("
            DELETE FROM 
                categoria_produtos 
            WHERE 
                categoria_produto_produto = :productId 
        ");
        $stmt->bindValue(":productId", $productId);
        return $stmt->execute();
    }

}