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

-- Copiando estrutura para trigger artistar.trg_lojas_insert
DROP TRIGGER IF EXISTS `trg_lojas_insert`;
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
DROP TRIGGER IF EXISTS `trg_produtos_insert`;
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
DROP TRIGGER IF EXISTS `trg_usuarios_insert`;
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
DROP TRIGGER IF EXISTS `trg_vendas_insert`;
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
DROP TRIGGER IF EXISTS `trg_vendas_item_delete`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_vendas_item_delete
AFTER DELETE ON vendas_itens
FOR EACH ROW
BEGIN
   UPDATE 
      produtos 
   SET 
      produto_estoque = produto_estoque + OLD.venda_item_unidades,
      produto_ultima_venda = NOW()
   WHERE  
      produto_id = OLD.venda_item_produto;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_item_insert
DROP TRIGGER IF EXISTS `trg_vendas_item_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_item_insert` BEFORE INSERT ON `vendas_itens` FOR EACH ROW BEGIN
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
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_item_update
DROP TRIGGER IF EXISTS `trg_vendas_item_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER trg_vendas_item_update
BEFORE UPDATE ON vendas_itens
FOR EACH ROW
BEGIN
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
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Copiando estrutura para trigger artistar.trg_vendas_update
DROP TRIGGER IF EXISTS `trg_vendas_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_vendas_update` BEFORE UPDATE ON `vendas` FOR EACH ROW BEGIN
   -- Se o campo venda_pago foi alterado de 0 para 1, define a data de pagamento
   IF NEW.venda_data_pagamento = OLD.venda_data_pagamento THEN
	   IF COALESCE(OLD.venda_pago, 0) = 0 AND NEW.venda_pago = 1 THEN
	       SET NEW.venda_data_pagamento = NOW();
	   ELSEIF OLD.venda_pago = 1 AND COALESCE(NEW.venda_pago, 0) = 0 THEN
	   	SET NEW.venda_data_pagamento = NULL;
	   END IF;
	END IF;

   -- Se o campo venda_entregue foi alterado de 0 para 1, define a data de entrega
   IF NEW.venda_data_entrega = OLD.venda_data_entrega THEN
	   IF COALESCE(OLD.venda_entregue, 0) = 0 AND NEW.venda_entregue = 1 THEN
	      SET NEW.venda_data_entrega = NOW();
	   ELSEIF OLD.venda_entregue = 1 AND COALESCE(NEW.venda_entregue, 0) = 0 THEN
	   	SET NEW.venda_data_entrega = NULL;
	   END IF;
	END IF;
   
   -- Se o campo venda_cancelada foi alterado de 0 para 1, define a data de entrega
	IF NEW.venda_data_cancelamento = OLD.venda_data_cancelamento THEN
	   IF COALESCE(OLD.venda_cancelada, 0) = 0 AND NEW.venda_cancelada = 1 THEN
	      SET NEW.venda_data_cancelamento = NOW();
	   ELSEIF OLD.venda_cancelada = 1 AND COALESCE(NEW.venda_cancelada, 0) = 0 THEN
	   	SET NEW.venda_data_cancelamento = NULL;
	   END IF;
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
