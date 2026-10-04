-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando estrutura para tabela artistar.categoria_loja
CREATE TABLE IF NOT EXISTS `categoria_loja` (
  `categoria_id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_loja` int(11) NOT NULL DEFAULT 0,
  `categoria_nome` varchar(50) DEFAULT NULL,
  `categoria_ativa` tinyint(4) NOT NULL DEFAULT 0,
  `categoria_cor` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`categoria_id`),
  KEY `categoria_loja` (`categoria_loja`),
  CONSTRAINT `FK_CATEGORIA_LOJA` FOREIGN KEY (`categoria_loja`) REFERENCES `lojas` (`loja_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.categoria_produtos
CREATE TABLE IF NOT EXISTS `categoria_produtos` (
  `categoria_produto_id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_produto_produto` int(11) NOT NULL DEFAULT 0,
  `categoria_produto_categoria` int(11) NOT NULL DEFAULT 0,
  `categoria_produto_ordem` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`categoria_produto_id`),
  KEY `categoria_produto_produto` (`categoria_produto_produto`),
  KEY `categoria_produto_categoria` (`categoria_produto_categoria`),
  CONSTRAINT `FK_CATEGORIA_PRODUTO` FOREIGN KEY (`categoria_produto_produto`) REFERENCES `produtos` (`produto_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_PRODUTO_CATEGORIA` FOREIGN KEY (`categoria_produto_categoria`) REFERENCES `categoria_loja` (`categoria_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.eventos
CREATE TABLE IF NOT EXISTS `eventos` (
  `evento_id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_proprietario` int(11) NOT NULL,
  `evento_data_criacao` datetime DEFAULT NULL,
  `evento_nome` varchar(255) DEFAULT NULL,
  `evento_descricao` text DEFAULT NULL,
  `evento_endereco_logradouro` varchar(255) DEFAULT NULL,
  `evento_endereco_numero` varchar(20) DEFAULT NULL,
  `evento_endereco_complemento` varchar(255) DEFAULT NULL,
  `evento_endereco_bairro` varchar(100) DEFAULT NULL,
  `evento_endereco_cidade` varchar(100) DEFAULT NULL,
  `evento_endereco_estado` varchar(5) DEFAULT NULL,
  `evento_endereco_cep` varchar(30) DEFAULT NULL,
  `evento_data_inicial` datetime DEFAULT NULL,
  `evento_data_final` datetime DEFAULT NULL,
  `evento_midia_thumbnail` int(11) DEFAULT NULL,
  `evento_oficial` tinyint(4) DEFAULT 0,
  `evento_produtor` varchar(50) DEFAULT NULL,
  `evento_privado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`evento_id`),
  KEY `evento_proprietario` (`evento_proprietario`),
  KEY `evento_midia_thumbnail` (`evento_midia_thumbnail`),
  CONSTRAINT `FK_evento_proprietario` FOREIGN KEY (`evento_proprietario`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `FK_evento_thumbnail` FOREIGN KEY (`evento_midia_thumbnail`) REFERENCES `eventos_midias` (`evento_midia_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.eventos_datas
CREATE TABLE IF NOT EXISTS `eventos_datas` (
  `evento_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_data_evento` int(11) DEFAULT NULL,
  `evento_data_dia` date NOT NULL,
  `evento_data_hora_inicial` time NOT NULL,
  `evento_data_hora_final` time NOT NULL,
  `evento_data_observacao` text NOT NULL,
  PRIMARY KEY (`evento_data_id`),
  KEY `evento_data_evento` (`evento_data_evento`),
  CONSTRAINT `FK_evento_data_evento` FOREIGN KEY (`evento_data_evento`) REFERENCES `eventos` (`evento_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.eventos_midias
CREATE TABLE IF NOT EXISTS `eventos_midias` (
  `evento_midia_id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_midia_evento` int(11) NOT NULL,
  `evento_midia_url` text DEFAULT NULL,
  `evento_midia_ordem` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`evento_midia_id`),
  KEY `evento_midia_evento` (`evento_midia_evento`),
  CONSTRAINT `FK_evento_midia_evento` FOREIGN KEY (`evento_midia_evento`) REFERENCES `eventos` (`evento_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.eventos_taxas
CREATE TABLE IF NOT EXISTS `eventos_taxas` (
  `evento_taxa_id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_taxa_evento` int(11) NOT NULL,
  `evento_taxa_ordem` int(11) DEFAULT NULL,
  `evento_taxa_titulo` varchar(50) DEFAULT NULL,
  `evento_taxa_valor` decimal(10,2) DEFAULT NULL,
  `evento_taxa_observacao` text DEFAULT NULL,
  PRIMARY KEY (`evento_taxa_id`),
  KEY `evento_taxa_evento` (`evento_taxa_evento`),
  CONSTRAINT `FK_evento_taxa_evento` FOREIGN KEY (`evento_taxa_evento`) REFERENCES `eventos` (`evento_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.eventos_vantagens
CREATE TABLE IF NOT EXISTS `eventos_vantagens` (
  `evento_vantagem_id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_vantagem_evento` int(11) NOT NULL,
  `evento_vantagem_vantagem` int(11) NOT NULL,
  PRIMARY KEY (`evento_vantagem_id`),
  KEY `evento_vantagem_evento` (`evento_vantagem_evento`),
  KEY `evento_vantagem_vantagem` (`evento_vantagem_vantagem`),
  CONSTRAINT `FK_evento_vantagem_evento` FOREIGN KEY (`evento_vantagem_evento`) REFERENCES `eventos` (`evento_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_evento_vantagem_vantagem` FOREIGN KEY (`evento_vantagem_vantagem`) REFERENCES `vantagens` (`vantagem_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.graficos_loja
CREATE TABLE IF NOT EXISTS `graficos_loja` (
  `grafico_id` int(11) NOT NULL AUTO_INCREMENT,
  `grafico_loja` int(11) DEFAULT NULL,
  `grafico_tipo` varchar(50) DEFAULT NULL,
  `grafico_contador` varchar(50) DEFAULT NULL,
  `grafico_alvo` varchar(50) DEFAULT NULL,
  `grafico_filtro` varchar(50) DEFAULT NULL,
  `grafico_lista` text DEFAULT NULL,
  `grafico_posicao` int(11) DEFAULT NULL,
  PRIMARY KEY (`grafico_id`) USING BTREE,
  KEY `FK_grafico_loja` (`grafico_loja`) USING BTREE,
  CONSTRAINT `FK_grafico_loja` FOREIGN KEY (`grafico_loja`) REFERENCES `lojas` (`loja_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.inscricoes
CREATE TABLE IF NOT EXISTS `inscricoes` (
  `inscricao_id` int(11) NOT NULL AUTO_INCREMENT,
  `inscricao_evento` int(11) NOT NULL,
  `inscricao_loja` int(11) NOT NULL,
  `inscricao_data_cadastro` datetime DEFAULT NULL,
  `inscricao_cancelada` tinyint(1) DEFAULT NULL,
  `inscricao_realizada` tinyint(1) DEFAULT 0,
  `inscricao_aprovada` tinyint(1) DEFAULT 0,
  `inscricao_data_aprovacao` datetime DEFAULT NULL,
  `inscricao_observacao_evento` text DEFAULT NULL,
  `inscricao_observacao_loja` text DEFAULT NULL,
  `inscricao_mensagem_evento` text DEFAULT NULL,
  `inscricao_mensagem_loja` text DEFAULT NULL,
  `inscricao_feedback_loja` text DEFAULT NULL,
  `inscricao_tags_evento` text DEFAULT NULL,
  PRIMARY KEY (`inscricao_id`),
  KEY `inscricao_evento` (`inscricao_evento`),
  KEY `inscricao_loja` (`inscricao_loja`),
  CONSTRAINT `FK_inscricao_evento` FOREIGN KEY (`inscricao_evento`) REFERENCES `eventos` (`evento_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_inscricao_loja` FOREIGN KEY (`inscricao_loja`) REFERENCES `lojas` (`loja_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.lojas
CREATE TABLE IF NOT EXISTS `lojas` (
  `loja_id` int(11) NOT NULL AUTO_INCREMENT,
  `loja_dt_criacao` datetime NOT NULL,
  `loja_ativa` tinyint(4) NOT NULL DEFAULT 0,
  `loja_nome_unico` varchar(255) DEFAULT NULL,
  `loja_nome` varchar(255) DEFAULT NULL,
  `loja_descricao` text DEFAULT NULL,
  `loja_foto` text DEFAULT NULL,
   `loja_banner` text DEFAULT NULL,
  `loja_proprietario` int(11) DEFAULT NULL,
  PRIMARY KEY (`loja_id`),
  UNIQUE KEY `loja_nome_unico` (`loja_nome_unico`),
  KEY `FK_LOJA_PROPRIETARIO` (`loja_proprietario`),
  CONSTRAINT `FK_LOJA_PROPRIETARIO` FOREIGN KEY (`loja_proprietario`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.produtos
CREATE TABLE IF NOT EXISTS `produtos` (
  `produto_id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_loja` int(11) NOT NULL,
  `produto_data_cadastro` datetime DEFAULT NULL,
  `produto_nome` varchar(50) NOT NULL,
  `produto_thumbnail` text DEFAULT NULL,
  `produto_descricao` text DEFAULT NULL,
  `produto_valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `produto_valor_desconto` decimal(10,2) DEFAULT 0.00,
  `produto_custo` decimal(10,2) DEFAULT 0.00,
  `produto_estoque` int(11) NOT NULL DEFAULT 0,
  `produto_estoque_minimo` int(11) NOT NULL DEFAULT 0,
  `produto_ativo` tinyint(1) NOT NULL DEFAULT 0,
  `produto_codigo_interno` varchar(50) DEFAULT NULL,
  `produto_original` int(11) DEFAULT NULL,
  `produto_palavras_chave` text DEFAULT NULL,
  `produto_ultima_venda` datetime DEFAULT NULL,
  PRIMARY KEY (`produto_id`) USING BTREE,
  KEY `produto_loja` (`produto_loja`),
  KEY `produto_original` (`produto_original`),
  FULLTEXT KEY `ft_produtos` (`produto_nome`,`produto_palavras_chave`,`produto_descricao`,`produto_codigo_interno`),
  CONSTRAINT `FK_LOJA_PRODUTO` FOREIGN KEY (`produto_loja`) REFERENCES `lojas` (`loja_id`),
  CONSTRAINT `FK_PRODUTO_ORIGINAL` FOREIGN KEY (`produto_original`) REFERENCES `produtos` (`produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_cadastro` datetime NOT NULL,
  `usuario_nome` varchar(100) NOT NULL,
  `usuario_nome_completo` varchar(50) NOT NULL,
  `usuario_email` varchar(255) DEFAULT NULL,
  `usuario_senha` varchar(50) DEFAULT NULL,
  `usuario_foto` text DEFAULT NULL,
  `usuario_email_validado` tinyint(4) DEFAULT NULL,
  `usuario_codigo_validacao` varchar(10) DEFAULT NULL,
  `usuario_envio_validacao` datetime DEFAULT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.vantagens
CREATE TABLE IF NOT EXISTS `vantagens` (
  `vantagem_id` int(11) NOT NULL AUTO_INCREMENT,
  `vantagem_nome` varchar(50) DEFAULT NULL,
  `vantagem_ativa` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`vantagem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.vendas
CREATE TABLE IF NOT EXISTS `vendas` (
  `venda_id` int(11) NOT NULL AUTO_INCREMENT,
  `venda_loja_id` int(11) NOT NULL,
  `venda_numero` int(11) DEFAULT NULL,
  `venda_pagamento` varchar(50) DEFAULT NULL,
  `venda_data_criacao` datetime DEFAULT NULL,
  `venda_pago` tinyint(1) DEFAULT NULL,
  `venda_data_pagamento` datetime DEFAULT NULL,
  `venda_entregue` tinyint(1) DEFAULT NULL,
  `venda_data_entrega` datetime DEFAULT NULL,
  `venda_cancelada` tinyint(1) DEFAULT NULL,
  `venda_data_cancelamento` datetime DEFAULT NULL,
  `venda_ultima_atualizacao` datetime DEFAULT NULL,
  `venda_data_venda` datetime DEFAULT NULL,
  `venda_evento_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`venda_id`) USING BTREE,
  KEY `FK_venda_loja` (`venda_loja_id`),
  KEY `venda_numero` (`venda_numero`),
  KEY `FK_venda_evento` (`venda_evento_id`),
  CONSTRAINT `FK_venda_evento` FOREIGN KEY (`venda_evento_id`) REFERENCES `eventos` (`evento_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_venda_loja` FOREIGN KEY (`venda_loja_id`) REFERENCES `lojas` (`loja_id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela artistar.vendas_itens
CREATE TABLE IF NOT EXISTS `vendas_itens` (
  `venda_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `venda_item_produto` int(11) DEFAULT NULL,
  `venda_item_venda` int(11) DEFAULT NULL,
  `venda_item_data_criacao` datetime DEFAULT NULL,
  `venda_item_unidades` int(11) DEFAULT NULL,
  `venda_item_desconto` decimal(10,2) DEFAULT NULL,
  `venda_item_valor` decimal(10,2) DEFAULT NULL,
  `venda_item_data_ultima_atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`venda_item_id`),
  KEY `FK_venda_item_produto` (`venda_item_produto`),
  KEY `FK_venda_item_venda` (`venda_item_venda`),
  CONSTRAINT `FK_venda_item_produto` FOREIGN KEY (`venda_item_produto`) REFERENCES `produtos` (`produto_id`),
  CONSTRAINT `FK_venda_item_venda` FOREIGN KEY (`venda_item_venda`) REFERENCES `vendas` (`venda_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para trigger artistar.trg_lojas_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_lojas_insert
BEFORE INSERT ON lojas
FOR EACH ROW
BEGIN
    IF NEW.loja_dt_criacao IS NULL THEN
        SET NEW.loja_dt_criacao = NOW();
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_produtos_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_produtos_insert
BEFORE INSERT ON produtos
FOR EACH ROW
BEGIN

   IF NEW.produto_data_cadastro IS NULL THEN
   	SET NEW.produto_data_cadastro = NOW();
   END IF;
	
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_usuarios_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_usuarios_insert
BEFORE INSERT ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.usuario_cadastro IS NULL THEN
        SET NEW.usuario_cadastro = NOW();
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_insert` BEFORE INSERT ON `vendas` FOR EACH ROW BEGIN
   DECLARE proximo_numero INT;
   
   IF NEW.venda_data_criacao IS NULL THEN
   	SET NEW.venda_data_criacao = NOW();
   END IF;
   
   IF NEW.venda_data_venda IS NULL THEN
   	SET NEW.venda_data_venda = NOW();
   END IF;
   
   IF NEW.venda_numero IS NULL THEN
     SELECT 
         COALESCE(MAX(venda_numero), 0) + 1
     INTO 
         proximo_numero
     FROM 
         vendas
     WHERE 
         venda_loja_id = NEW.venda_loja_id;

     SET NEW.venda_numero = proximo_numero;
   END IF;
    
   IF NEW.venda_data_pagamento IS NULL THEN
	   IF NEW.venda_pago = 1 THEN
	    	SET NEW.venda_data_pagamento = NOW();
	   ELSE
	   	SET NEW.venda_data_pagamento = NULL;
	   END IF;
	END IF;
   
   IF NEW.venda_data_entrega IS NULL THEN
	   IF NEW.venda_entregue = 1 THEN
	   	SET NEW.venda_data_entrega = NOW();
	   ELSE
	   	SET NEW.venda_data_entrega = NULL;
	   END IF;
	END IF;
	
	IF NEW.venda_data_cancelamento IS NULL THEN
	   IF NEW.venda_cancelada = 1 THEN
	   	SET NEW.venda_data_cancelamento = NOW();
	   ELSE
	   	SET NEW.venda_data_cancelamento = NULL;
	   END IF;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_item_delete
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_item_delete` AFTER DELETE ON `vendas_itens` FOR EACH ROW BEGIN
   DECLARE venda_cancelada_atual INT DEFAULT 0;

   -- Lê status da venda (tabela pai)
   SELECT COALESCE(venda_cancelada, 0)
   INTO venda_cancelada_atual
   FROM vendas
   WHERE venda_id = OLD.venda_item_venda;

   -- Se a venda estiver cancelada, NÃO mexe no estoque
   IF venda_cancelada_atual = 0 THEN
	   UPDATE 
	      produtos 
	   SET 
	      produto_estoque = produto_estoque + OLD.venda_item_unidades,
	      produto_ultima_venda = NOW()
	   WHERE  
	      produto_id = OLD.venda_item_produto;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_item_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_item_insert` BEFORE INSERT ON `vendas_itens` FOR EACH ROW BEGIN
   DECLARE venda_cancelada_atual INT DEFAULT 0;

   -- Lê status da venda (tabela pai)
   SELECT COALESCE(venda_cancelada, 0)
   INTO venda_cancelada_atual
   FROM vendas
   WHERE venda_id = NEW.venda_item_venda;

   -- Se a venda estiver cancelada, NÃO mexe no estoque
   IF venda_cancelada_atual = 0 THEN
	   UPDATE 
	      produtos 
	   SET 
	      produto_estoque = produto_estoque - NEW.venda_item_unidades,
	      produto_ultima_venda = NOW()
	   WHERE  
	      produto_id = NEW.venda_item_produto;
	      
	   IF NEW.venda_item_data_criacao IS NULL THEN
		   SET NEW.venda_item_data_criacao = NOW();
		END IF;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_item_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_item_update` BEFORE UPDATE ON `vendas_itens` FOR EACH ROW BEGIN

   DECLARE venda_cancelada_atual INT DEFAULT 0;

   -- Lê status da venda (tabela pai)
   SELECT COALESCE(venda_cancelada, 0)
   INTO venda_cancelada_atual
   FROM vendas
   WHERE venda_id = NEW.venda_item_venda;

   -- Se a venda estiver cancelada, NÃO mexe no estoque
   IF venda_cancelada_atual = 0 THEN
   -- Se o produto for o mesmo, apenas ajusta o estoque pela diferença de unidades
	   IF OLD.venda_item_produto = NEW.venda_item_produto THEN
	      UPDATE produtos
	      SET 
	         produto_estoque = produto_estoque + (OLD.venda_item_unidades - NEW.venda_item_unidades),
	         produto_ultima_venda = NOW()
	      WHERE 
	         produto_id = NEW.venda_item_produto;
	         
	   -- Se o produto for diferente, devolve o estoque ao antigo e desconta do novo
	   ELSE
	      -- Devolve unidades ao produto antigo
	      UPDATE produtos
	      SET 
	         produto_estoque = produto_estoque + OLD.venda_item_unidades,
	         produto_ultima_venda = NOW()
	      WHERE 
	         produto_id = OLD.venda_item_produto;
	
	      -- Subtrai unidades do novo produto
	      UPDATE produtos
	      SET 
	         produto_estoque = produto_estoque - NEW.venda_item_unidades,
	         produto_ultima_venda = NOW()
	      WHERE 
	         produto_id = NEW.venda_item_produto;
	   END IF;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_update` BEFORE UPDATE ON `vendas` FOR EACH ROW BEGIN
   -- Se o campo venda_pago foi alterado de 0 para 1, define a data de pagamento
   IF COALESCE(OLD.venda_pago, 0) = 0 AND NEW.venda_pago = 1 THEN
       SET NEW.venda_data_pagamento = NOW();
   ELSEIF OLD.venda_pago = 1 AND COALESCE(NEW.venda_pago, 0) = 0 THEN
   	SET NEW.venda_data_pagamento = NULL;
   END IF;

   -- Se o campo venda_entregue foi alterado de 0 para 1, define a data de entrega
   IF COALESCE(OLD.venda_entregue, 0) = 0 AND NEW.venda_entregue = 1 THEN
      SET NEW.venda_data_entrega = NOW();
   ELSEIF OLD.venda_entregue = 1 AND COALESCE(NEW.venda_entregue, 0) = 0 THEN
   	SET NEW.venda_data_entrega = NULL;
   END IF;
   
   -- Se o campo venda_cancelada foi alterado de 0 para 1, define a data de entrega
   IF COALESCE(OLD.venda_cancelada, 0) = 0 AND NEW.venda_cancelada = 1 THEN
      SET NEW.venda_data_cancelamento = NOW();
      UPDATE
      	produtos p
      INNER JOIN
      	vendas_itens vi ON p.produto_id = vi.venda_item_produto AND vi.venda_item_venda = OLD.venda_id
      SET
      	p.produto_estoque = p.produto_estoque + vi.venda_item_unidades;
   ELSEIF OLD.venda_cancelada = 1 AND COALESCE(NEW.venda_cancelada, 0) = 0 THEN
   	SET NEW.venda_data_cancelamento = NULL;
   	UPDATE
      	produtos p
      INNER JOIN
      	vendas_itens vi ON p.produto_id = vi.venda_item_produto AND vi.venda_item_venda = OLD.venda_id
      SET
      	p.produto_estoque = p.produto_estoque - vi.venda_item_unidades;
   END IF;
   
   SET NEW.venda_ultima_atualizacao = NOW();
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
