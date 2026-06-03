<?php


namespace Source\Model;

use PDO;
use Source\Core\Core;

class Events extends Core
{

    public function getEventBasicInfo($eventId, $storeId) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                eve.*,
                insc.*,
                CASE 
                    WHEN COALESCE(insc.inscricao_realizada, 0) = 0 AND COALESCE(insc.inscricao_aprovada, 0) = 0  THEN "pendente"
                    WHEN insc.inscricao_realizada = 1 AND COALESCE(insc.inscricao_aprovada, 0) = 0 THEN "realizada"
                    WHEN insc.inscricao_aprovada = 1 THEN "aprovada"
                    WHEN insc.inscricao_aprovada = -1 THEN "reprovada"
                    ELSE "desconhecido"
                END AS status,
                (SELECT COUNT(inscricao_id) FROM inscricoes WHERE inscricao_evento = eve.evento_id AND COALESCE(inscricao_cancelada, 0) = 0) AS total_inscritos
            FROM
                eventos eve
            LEFT JOIN
                inscricoes AS insc ON insc.inscricao_evento = eve.evento_id AND insc.inscricao_loja = :storeId
            WHERE
                eve.evento_id = :eventId
        ');

        $qtdStatement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $qtdStatement->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetch(PDO::FETCH_ASSOC);
    }

    public function getEventDays($eventId) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                *
            FROM
                eventos_datas
            WHERE
                evento_data_evento = :eventId
            ORDER BY
                evento_data_dia ASC, evento_data_hora_inicial ASC
        ');

        $qtdStatement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventAdvantages($eventId, $eventOnly = true, $activeOnly = true) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                vant.vantagem_id AS id,
                vant.vantagem_nome AS nome,
                eve_vant.evento_vantagem_id AS eve_vant_id
            FROM
                vantagens AS vant
            ' . ($eventOnly ? 'INNER' : 'LEFT') . ' JOIN
                eventos_vantagens AS eve_vant ON vant.vantagem_id = eve_vant.evento_vantagem_vantagem AND eve_vant.evento_vantagem_evento = :eventId
            ' . ($activeOnly ? 'WHERE vant.vantagem_ativa = 1' : '') . '
            ORDER BY
                vant.vantagem_nome ASC
        ');

        $qtdStatement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventPrices($eventId) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                *
            FROM
                eventos_taxas
            WHERE
                evento_taxa_evento = :eventId
            ORDER BY
                evento_taxa_ordem ASC
        ');

        $qtdStatement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventPhotos($eventId) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                *
            FROM
                eventos_midias
            WHERE
                evento_midia_evento = :eventId
            ORDER BY
                evento_midia_id ASC
        ');

        $qtdStatement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserEvents($user) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                eve.*,
                insc.*,
                CASE 
                    WHEN eve.evento_data_final < CURDATE() THEN "finalizado"
                    WHEN eve.evento_data_final >= CURDATE() AND COALESCE(insc.inscricao_realizada, 0) = 0 AND COALESCE(insc.inscricao_aprovada, 0) = 0  THEN "pendente"
                    WHEN eve.evento_data_final >= CURDATE() AND insc.inscricao_realizada = 1 AND COALESCE(insc.inscricao_aprovada, 0) = 0 THEN "realizada"
                    WHEN eve.evento_data_final >= CURDATE() AND insc.inscricao_aprovada = 1 THEN "aprovada"
                    WHEN eve.evento_data_final >= CURDATE() AND insc.inscricao_aprovada = -1 THEN "reprovada"
                    WHEN eve.evento_proprietario = :user THEN "criado"
                    ELSE "desconhecido"
                END AS status,
                midia.evento_midia_url AS thumbnail
            FROM
                eventos AS eve
            LEFT JOIN
                eventos_midias AS midia ON midia.evento_midia_id = eve.evento_midia_thumbnail
            LEFT JOIN
                inscricoes AS insc ON insc.inscricao_evento = eve.evento_id
            WHERE
                (
                        eve.evento_proprietario = :user
                    OR
                        insc.inscricao_loja IN(SELECT loja_id FROM lojas WHERE loja_proprietario = :user)
                )
            AND
                COALESCE(insc.inscricao_cancelada, 0) = 0
            GROUP BY
                eve.evento_id
            ORDER BY
                eve.evento_id DESC 
        ');

        $qtdStatement->bindParam(':user', $user, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserEventsTotals($user) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                COUNT(DISTINCT eve.evento_id) AS total,
                COUNT(DISTINCT(IF(eve.evento_proprietario = :user, eve.evento_id, NULL))) AS total_criados,
                COUNT(DISTINCT(IF(eve.evento_data_final < CURDATE(), eve.evento_id, NULL))) AS total_finalizados,
                COUNT(DISTINCT(IF(eve.evento_data_final >= CURDATE() AND COALESCE(insc.inscricao_realizada, 0) = 0 AND COALESCE(insc.inscricao_aprovada, 0) = 0, eve.evento_id, NULL))) AS total_pendente,
                COUNT(DISTINCT(IF(eve.evento_data_final >= CURDATE() AND insc.inscricao_realizada = 1 AND COALESCE(insc.inscricao_aprovada, 0) = 0, eve.evento_id, NULL))) AS total_realizada,
                COUNT(DISTINCT(IF(eve.evento_data_final >= CURDATE() AND insc.inscricao_aprovada = 1, eve.evento_id, NULL))) AS total_aprovada,
                COUNT(DISTINCT(IF(eve.evento_data_final >= CURDATE() AND insc.inscricao_aprovada = -1, eve.evento_id, NULL))) AS total_reprovada
            FROM
                eventos AS eve
            LEFT JOIN
                eventos_midias AS midia ON midia.evento_midia_id = eve.evento_midia_thumbnail
            LEFT JOIN
                inscricoes AS insc ON insc.inscricao_evento = eve.evento_id
            WHERE
                (
                    eve.evento_proprietario = :user
                OR
                    insc.inscricao_loja IN(SELECT loja_id FROM lojas WHERE loja_proprietario = :user)
                )
            AND
                COALESCE(insc.inscricao_cancelada, 0) = 0
        ');

        $qtdStatement->bindParam(':user', $user, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserEventsToday($user) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                eve.*,
                insc.*,
                edat.*,
                IF(eve.evento_proprietario = :user, 1, 0) AS usuario_proprietario,
                midia.evento_midia_url AS thumbnail,
                DATE_FORMAT(eve.evento_data_inicial, "%d/%m/%Y") AS data_inicial,
                DATE_FORMAT(eve.evento_data_final, "%d/%m/%Y") AS data_final,
                DATE_FORMAT(edat.evento_data_hora_inicial, "%H:%i") AS hora_inicial,
                DATE_FORMAT(edat.evento_data_hora_final, "%H:%i") AS hora_final
            FROM
                eventos AS eve
            LEFT JOIN
                eventos_midias AS midia ON midia.evento_midia_id = eve.evento_midia_thumbnail
            LEFT JOIN
                inscricoes AS insc ON insc.inscricao_evento = eve.evento_id
            LEFT JOIN
                eventos_datas AS edat ON edat.evento_data_evento = eve.evento_id
            WHERE
                (
                        eve.evento_proprietario = :user
                    OR
                        insc.inscricao_loja IN(SELECT loja_id FROM lojas WHERE loja_proprietario = :user)
                )
            AND
                (
                        CURDATE() BETWEEN eve.evento_data_inicial AND eve.evento_data_final
                    OR
                        edat.evento_data_dia = CURDATE()
                )   
            AND
                COALESCE(insc.inscricao_cancelada, 0) = 0
            GROUP BY
                eve.evento_id
            ORDER BY
                eve.evento_id DESC 
        ');

        $qtdStatement->bindParam(':user', $user, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkIfUserIsSubscribed($eventId, $storeId) {
        $qtdStatement = $this->SQL->prepare('
            SELECT
                *
            FROM
                inscricoes
            WHERE
                inscricao_evento = :eventId
            AND
                inscricao_loja = :storeId
        ');

        $qtdStatement->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $qtdStatement->bindParam(':storeId', $storeId, PDO::PARAM_INT);
        $qtdStatement->execute();
        return $qtdStatement->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdvantages($active = true) {
        $active = $active ? 1 : 0;
        $qtdStatement = $this->SQL->prepare('
            SELECT
                vantagem_id id,
                vantagem_nome nome
            FROM
                vantagens
            WHERE
                vantagem_ativa = :active
            ORDER BY
                vantagem_nome ASC
        ');
        $qtdStatement->bindParam(':active', $active, PDO::PARAM_INT); 
        $qtdStatement->execute();
        return $qtdStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEvent($postData, $userId) {
        $eventStatement = $this->SQL->prepare('
            INSERT INTO eventos
                (
                    evento_proprietario, 
                    evento_data_criacao,
                    evento_nome,
                    evento_descricao,
                    evento_endereco_logradouro,
                    evento_endereco_numero,
                    evento_endereco_complemento,
                    evento_endereco_bairro,
                    evento_endereco_cidade,
                    evento_endereco_estado,
                    evento_pais,
                    evento_moeda,
                    evento_produtor,
                    evento_privado
                )
            VALUES 
                (
                    :user, 
                    NOW(), 
                    :name,
                    :description,
                    :address,
                    :number,
                    :complement,
                    :neighborhood,
                    :city,
                    :state,
                    :country,
                    :currency,
                    :producer,
                    :private
                )
        ');
        $eventStatement->bindParam(':user', $userId, PDO::PARAM_INT);
        $eventStatement->bindParam(':name', $postData['eventTitle'], PDO::PARAM_STR);
        $eventStatement->bindParam(':description', $postData['eventDescription'], PDO::PARAM_STR);
        $eventStatement->bindParam(':address', $postData['eventAddress'], PDO::PARAM_STR);
        $eventStatement->bindParam(':number', $postData['eventNumber'], PDO::PARAM_STR);
        $eventStatement->bindParam(':complement', $postData['eventComplement'], PDO::PARAM_STR);
        $eventStatement->bindParam(':neighborhood', $postData['eventNeighborhood'], PDO::PARAM_STR);
        $eventStatement->bindParam(':city', $postData['eventCity'], PDO::PARAM_STR);
        $eventStatement->bindParam(':state', $postData['eventState'], PDO::PARAM_STR);
        // $eventStatement->bindParam(':zip', $postData['eventCep'], PDO::PARAM_STR);
        $eventStatement->bindParam(':country', $postData['eventCountry'], PDO::PARAM_STR);
        $eventStatement->bindParam(':currency', $postData['eventCurrency'], PDO::PARAM_STR);
        $eventStatement->bindParam(':producer', $postData['eventProducer'], PDO::PARAM_STR);
        $private = !empty($postData['private']) ? 1 : 0;
        $eventStatement->bindParam(':private', $private, PDO::PARAM_INT);

        $eventStatement->execute();
        return $this->SQL->lastInsertId();
    }

    public function updateEvent($eventId, $postData) {
        $eventStatement = $this->SQL->prepare('
            UPDATE 
                eventos 
            SET
                evento_nome = :name,
                evento_descricao = :description,
                evento_endereco_logradouro = :address,
                evento_endereco_numero = :number,
                evento_endereco_complemento = :complement,
                evento_endereco_bairro = :neighborhood,
                evento_endereco_cidade = :city,
                evento_endereco_estado = :state,
                evento_pais = :country,
                evento_moeda = :currency,
                evento_produtor = :producer,
                evento_privado = :private
            WHERE
                evento_id = :event
        ');
        $eventStatement->bindParam(':name', $postData['eventTitle'], PDO::PARAM_STR);
        $eventStatement->bindParam(':description', $postData['eventDescription'], PDO::PARAM_STR);
        $eventStatement->bindParam(':address', $postData['eventAddress'], PDO::PARAM_STR);
        $eventStatement->bindParam(':number', $postData['eventNumber'], PDO::PARAM_STR);
        $eventStatement->bindParam(':complement', $postData['eventComplement'], PDO::PARAM_STR);
        $eventStatement->bindParam(':neighborhood', $postData['eventNeighborhood'], PDO::PARAM_STR);
        $eventStatement->bindParam(':city', $postData['eventCity'], PDO::PARAM_STR);
        $eventStatement->bindParam(':state', $postData['eventState'], PDO::PARAM_STR);
        // $eventStatement->bindParam(':zip', $postData['eventCep'], PDO::PARAM_STR);
        $eventStatement->bindParam(':country', $postData['eventCountry'], PDO::PARAM_STR);
        $eventStatement->bindParam(':currency', $postData['eventCurrency'], PDO::PARAM_STR);
        $eventStatement->bindParam(':producer', $postData['eventProducer'], PDO::PARAM_STR);
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $private = !empty($postData['private']) ? 1 : 0;
        $eventStatement->bindParam(':private', $private, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function addEventAdvantage($eventId, $advantageId) {
        $eventStatement = $this->SQL->prepare('
            INSERT INTO eventos_vantagens
                (
                    evento_vantagem_evento,
                    evento_vantagem_vantagem
                )
            VALUES 
                (
                    :event,
                    :advantage
                )
        ');
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->bindParam(':advantage', $advantageId, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function removeEventAdvantage($id) {
        $eventStatement = $this->SQL->prepare('
            DELETE FROM 
                eventos_vantagens
            WHERE
                evento_vantagem_id = :id
        ');
        $eventStatement->bindParam(':id', $id, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function addEventDate($eventId, $dateData) {
        $eventStatement = $this->SQL->prepare('
            INSERT INTO eventos_datas
                (
                    evento_data_evento,
                    evento_data_dia,
                    evento_data_hora_inicial,
                    evento_data_hora_final,
                    evento_data_observacao
                )
            VALUES 
                (
                    :event,
                    :day,
                    :start_time,
                    :end_time,
                    :observation
                )
        ');
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->bindParam(':day', $dateData['day'], PDO::PARAM_STR);
        $eventStatement->bindParam(':start_time', $dateData['time'], PDO::PARAM_STR);
        $eventStatement->bindParam(':end_time', $dateData['endTime'], PDO::PARAM_STR);
        $eventStatement->bindParam(':observation', $dateData['observation'], PDO::PARAM_STR);
        return $eventStatement->execute();
    }

    public function updateEventDate($dateId, $dateData) {
        $eventStatement = $this->SQL->prepare('
            UPDATE 
                eventos_datas 
            SET
                evento_data_dia = :day,
                evento_data_hora_inicial = :start_time,
                evento_data_hora_final = :end_time,
                evento_data_observacao = :observation
            WHERE
                evento_data_id = :id
        ');
        $eventStatement->bindParam(':day', $dateData['day'], PDO::PARAM_STR);
        $eventStatement->bindParam(':start_time', $dateData['time'], PDO::PARAM_STR);
        $eventStatement->bindParam(':end_time', $dateData['endTime'], PDO::PARAM_STR);
        $eventStatement->bindParam(':observation', $dateData['observation'], PDO::PARAM_STR);
        $eventStatement->bindParam(':id', $dateId, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function removeEventDate($id) {
        $eventStatement = $this->SQL->prepare('
            DELETE FROM 
                eventos_datas
            WHERE
                evento_data_id = :id
        ');
        $eventStatement->bindParam(':id', $id, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function addEventPrice($eventId, $priceData) {
        $eventStatement = $this->SQL->prepare('
            INSERT INTO eventos_taxas
                (
                    evento_taxa_evento,
                    evento_taxa_ordem,
                    evento_taxa_titulo,
                    evento_taxa_valor,
                    evento_taxa_observacao
                )
            VALUES 
                (
                    :event,
                    :order,
                    :title,
                    :value,
                    :observation
                )
        ');
        $price = str_replace(',', '.', str_replace('.', '', $priceData['amount']));
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->bindParam(':order', $priceData['order'], PDO::PARAM_INT);
        $eventStatement->bindParam(':title', $priceData['name'], PDO::PARAM_STR);
        $eventStatement->bindParam(':value', $price, PDO::PARAM_STR);
        $eventStatement->bindParam(':observation', $priceData['observation'], PDO::PARAM_STR);
        return $eventStatement->execute();
    }

    public function updateEventPrice($priceId, $priceData) {
        $eventStatement = $this->SQL->prepare('
            UPDATE 
                eventos_taxas 
            SET
                evento_taxa_ordem = :order,
                evento_taxa_titulo = :title,
                evento_taxa_valor = :value,
                evento_taxa_observacao = :observation
            WHERE
                evento_taxa_id = :id
        ');
        $price = str_replace(',', '.', str_replace('.', '', $priceData['amount']));
        $eventStatement->bindParam(':order', $priceData['order'], PDO::PARAM_INT);
        $eventStatement->bindParam(':title', $priceData['name'], PDO::PARAM_STR);
        $eventStatement->bindParam(':value', $price, PDO::PARAM_STR);
        $eventStatement->bindParam(':observation', $priceData['observation'], PDO::PARAM_STR);
        $eventStatement->bindParam(':id', $priceId, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function removeEventPrice($id) {
        $eventStatement = $this->SQL->prepare('
            DELETE FROM 
                eventos_taxas
            WHERE
                evento_taxa_id = :id
        ');
        $eventStatement->bindParam(':id', $id, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function updateEventComplementaryInfo($eventId) {
        //Data_inicial = A menor data e a hora inicial desta data
        //Data_final = A maior data e a hora final desta data
        // Iniciar transação
        $this->SQL->beginTransaction();
        $eventStatement = $this->SQL->prepare('
            UPDATE eventos SET
                evento_data_inicial = (SELECT MIN(evento_data_dia) FROM eventos_datas WHERE evento_data_evento = :event),
                evento_data_final = (SELECT MAX(evento_data_dia) FROM eventos_datas WHERE evento_data_evento = :event)
            WHERE
                evento_id = :event
        ');
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->execute();
        //Agora atualizar as datas com base na data inicial e final
        $eventStatement = $this->SQL->prepare('
            UPDATE eventos e1 SET
                evento_data_inicial = (SELECT CONCAT(evento_data_dia, " ", evento_data_hora_inicial) FROM eventos_datas WHERE evento_data_evento = :event AND evento_data_dia = e1.evento_data_inicial LIMIT 1),
                evento_data_final = (SELECT CONCAT(evento_data_dia, " ", evento_data_hora_final) FROM eventos_datas WHERE evento_data_evento = :event AND evento_data_dia = e1.evento_data_final LIMIT 1)
            WHERE
                evento_id = :event
        ');
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->execute();
        $this->SQL->commit();
    }

    public function subscribeToEvent($eventId, $storeId) {
        $eventStatement = $this->SQL->prepare('
            INSERT INTO inscricoes
                (
                    inscricao_evento,
                    inscricao_loja,
                    inscricao_data_cadastro
                )
            VALUES 
                (
                    :event,
                    :store,
                    NOW()
                )
        ');
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->bindParam(':store', $storeId, PDO::PARAM_INT);
        $eventStatement->execute();
        return $this->SQL->lastInsertId();
    }

    public function unsubscribeFromEvent($inscricaoId) {
        $eventStatement = $this->SQL->prepare('
            UPDATE 
                inscricoes 
            SET
                inscricao_cancelada = 1
            WHERE
                inscricao_id = :inscricao
        ');
        $eventStatement->bindParam(':inscricao', $inscricaoId, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function reactivateSubscription($inscricaoId) {
        $eventStatement = $this->SQL->prepare('
            UPDATE 
                inscricoes 
            SET
                inscricao_cancelada = 0
            WHERE
                inscricao_id = :inscricao
        ');
        $eventStatement->bindParam(':inscricao', $inscricaoId, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

    public function updateUserSubscription($eventId, $storeId, $status = null, $tags = null, $observation = null, $feedback = null) {
        $status = filter_var($status, FILTER_SANITIZE_STRING);
        $observation = filter_var($observation, FILTER_SANITIZE_STRING);
        $feedback = filter_var($feedback, FILTER_SANITIZE_STRING);
        $eventStatement = $this->SQL->prepare('
            UPDATE 
                inscricoes 
            SET
                inscricao_realizada = :realizada,
                inscricao_aprovada = :aprovada,
                inscricao_tags_evento = :tags,
                inscricao_observacao_loja = :observation,
                inscricao_feedback_loja = :feedback
            WHERE
                inscricao_evento = :event
            AND
                inscricao_loja = :store
        ');
        $tagsString = $tags;
        if (is_array($tags))$tagsString = implode('|', $tags);
        $tagsString = filter_var($tagsString, FILTER_SANITIZE_STRING);
        switch ($status) {
            case 'pendente':
            default:
                $realizada = 0;
                $aprovada = 0;
                break;
            case 'realizada':
                $realizada = 1;
                $aprovada = 0;
                break;
            case 'aprovada':
                $realizada = 1;
                $aprovada = 1;
                break;
            case 'reprovada':
                $realizada = 1;
                $aprovada = -1;
                break;
        }
        $eventStatement->bindParam(':realizada', $realizada, PDO::PARAM_INT);
        $eventStatement->bindParam(':aprovada', $aprovada, PDO::PARAM_INT);
        $eventStatement->bindParam(':tags', $tagsString, PDO::PARAM_STR);
        $eventStatement->bindParam(':observation', $observation, PDO::PARAM_STR);
        $eventStatement->bindParam(':feedback', $feedback, PDO::PARAM_STR);
        $eventStatement->bindParam(':event', $eventId, PDO::PARAM_INT);
        $eventStatement->bindParam(':store', $storeId, PDO::PARAM_INT);
        return $eventStatement->execute();
    }

}