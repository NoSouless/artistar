<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;

class Sales extends Core {

    public function getProducts($storeId) {
        $stmt = $this->SQL->prepare("
            SELECT 
                produto_id id,
                produto_nome nome,
                produto_palavras_chave palavra_chave,
                produto_descricao descricao,
                produto_codigo_interno subtitulo,
                produto_valor preco,
                produto_valor_desconto desconto,
                produto_estoque estoque,
                produto_sob_demanda sob_demanda,
                produto_thumbnail imagem
            FROM 
                produtos
            WHERE 
                produto_loja = :loja_id
            AND
                produto_ativo = 1
            ORDER BY 
                nome ASC
        ");
        $stmt->bindValue(":loja_id", $storeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoresCurrentEvents($storeId) {
        $stmt = $this->SQL->prepare("
            SELECT 
                eve.evento_id id,
                eve.evento_nome nome,
                insc.inscricao_id inscricao_id
            FROM 
                eventos eve
            INNER JOIN 
                inscricoes insc ON insc.inscricao_evento = eve.evento_id AND insc.inscricao_loja = :loja_id
            WHERE 
                NOW() BETWEEN eve.evento_data_inicial AND eve.evento_data_final
            ORDER BY 
                eve.evento_nome ASC
        ");
        $stmt->bindValue(":loja_id", $storeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertSale($saleData, $storeId) {
        //Start transaction
        $this->SQL->beginTransaction();
        try {
            //The transaction will: insert the sale and its items
            $stmt = $this->SQL->prepare("
                INSERT INTO vendas (
                    venda_loja_id,
                    venda_pagamento,
                    venda_data_criacao, 
                    venda_pago,
                    venda_entregue,
                    venda_data_venda,
                    venda_evento_id
                ) VALUES (
                    :loja_id, 
                    :pagamento, 
                    NOW(), 
                    :pago, 
                    :entregue,
                    :data_venda,
                    :evento_id
                )
            ");
            $stmt->bindValue(":loja_id", $storeId, PDO::PARAM_INT);
            switch($saleData['payment_method']) {
                case 'pix':
                    $saleData['payment_method'] = 'Pix';
                    break;
                case 'dinheiro':
                default:
                    $saleData['payment_method'] = 'Dinheiro';
                    break;
                case 'cartao-debito':
                    $saleData['payment_method'] = 'Cartão de Débito';
                    break;
                case 'cartao-credito':
                    $saleData['payment_method'] = 'Cartão de Crédito';
                    break;
                case 'boleto':
                    $saleData['payment_method'] = 'Boleto';
                    break;
                case 'transferencia':
                    $saleData['payment_method'] = 'Transferência';
                    break;
                case 'outro':
                    $saleData['payment_method'] = 'Outro';
                    break;
            }
            $stmt->bindValue(":pagamento", $saleData['payment_method'], PDO::PARAM_STR);
            $stmt->bindValue(":pago", (isset($saleData['paid']) && $saleData['paid'] == '1' ? 1 : 0), PDO::PARAM_BOOL);
            $stmt->bindValue(":entregue", (isset($saleData['delivered']) && $saleData['delivered'] == '1' ? 1 : 0), PDO::PARAM_BOOL);
            $stmt->bindValue(":data_venda", date('Y-m-d H:i:s', strtotime($saleData['sale_datetime'])), PDO::PARAM_STR);
            $stmt->bindValue(":evento_id", empty($saleData['event_id']) ? NULL : $saleData['event_id'], PDO::PARAM_INT);
            $stmt->execute();
            $saleId = $this->SQL->lastInsertId();
            foreach ($saleData['items'] as $item) {
                $item['total_price'] = str_replace(',', '.', str_replace('.', '', $item['total_price']));
                $item['base_price'] = str_replace(',', '.', str_replace('.', '', $item['base_price']));
                $item['discount'] = str_replace(',', '.', str_replace('.', '', $item['discount']));
                if (isset($item['new_product']) && $item['new_product'] == '1') {
                    //Insert new product
                    $stmt = $this->SQL->prepare("
                        INSERT INTO produtos (
                            produto_loja,
                            produto_nome,
                            produto_descricao,
                            produto_valor,
                            produto_valor_desconto,
                            produto_estoque,
                            produto_sob_demanda,
                            produto_ativo,
                            produto_data_cadastro
                        ) VALUES (
                            :loja_id,
                            :nome,
                            :descricao,
                            :valor,
                            :valor_desconto,
                            0,
                            :sob_demanda,
                            1,
                            NOW()
                        )
                    ");
                    $stmt->bindValue(":loja_id", $storeId, PDO::PARAM_INT);
                    $stmt->bindValue(":nome", $item['name'], PDO::PARAM_STR);
                    $stmt->bindValue(":descricao", $item['subtitle'], PDO::PARAM_STR);
                    $stmt->bindValue(":valor", $item['base_price'], PDO::PARAM_STR);
                    $stmt->bindValue(":valor_desconto", $item['discount'], PDO::PARAM_STR);
                    $stmt->bindValue(":sob_demanda", ($item['sob_demanda'] ?? '0') == '1' ? 1 : 0, PDO::PARAM_BOOL);
                    $stmt->execute();   
                    $item['id'] = $this->SQL->lastInsertId();
                }
                $stmt = $this->SQL->prepare("
                    INSERT INTO vendas_itens (
                        venda_item_produto, 
                        venda_item_venda,
                        venda_item_unidades,
                        venda_item_desconto,
                        venda_item_valor
                    ) VALUES (
                        :produto_id,
                        :venda_id,
                        :quantidade,
                        :desconto,
                        :preco  
                    )
                ");
                $stmt->bindValue(":produto_id", $item['id'], PDO::PARAM_INT);
                $stmt->bindValue(":venda_id", $saleId, PDO::PARAM_INT);
                $stmt->bindValue(":quantidade", $item['qtd'], PDO::PARAM_INT);
                $stmt->bindValue(":preco", $item['total_price'], PDO::PARAM_STR);
                $stmt->bindValue(":desconto", $item['discount'], PDO::PARAM_STR);
                $stmt->execute();
            }

            $stmt = $this->SQL->prepare("SELECT venda_numero FROM vendas WHERE venda_id = :id");
            $stmt->bindValue(":id", $saleId, PDO::PARAM_INT);
            $stmt->execute();
            $numero = $stmt->fetchColumn();


            //Commit transaction
            $this->SQL->commit();
            return [
                'id' => $saleId,
                'numero' => $numero
            ];
        } catch (\Exception $e) {
            //Rollback transaction in case of error
            $this->SQL->rollBack();
            throw new \Exception("Erro ao inserir venda: " . $e->getMessage());
        }
    }

}