/*Foi usado o MySQL 8.0 no banco de dados*/

/*Tabela: usuarios*/
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/* Inserts: usuarios */
INSERT INTO usuarios (nome, email, senha, is_admin) VALUES 
('João Silva', 'joao@example.com', 'senha123', 0),
('Maria Oliveira', 'maria@example.com', 'senha456', 0),
('Admin', 'admin@gmail.com', 'adminsenha', 1);

/* Tabela: planos */
CREATE TABLE `planos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_original` decimal(10,2) DEFAULT NULL,
  `armazenamento` varchar(20) DEFAULT NULL,
  `largura_banda` varchar(20) DEFAULT NULL,
  `sites` int(11) DEFAULT NULL,
  `contas_email` int(11) DEFAULT NULL,
  `banco_dados` int(11) DEFAULT NULL,
  `dominio_gratis` tinyint(1) DEFAULT 0,
  `economia` decimal(10,2) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Inserts: planos*/
INSERT INTO planos (nome, descricao, preco, preco_original, armazenamento, largura_banda, sites, contas_email, banco_dados, dominio_gratis, economia, destaque) VALUES 
('Plano Básico', 'Ideal para iniciantes', 19.99, 29.99, '10GB', '100GB', 1, 5, 1, 0, 10.00, 0),
('Plano Intermediário', 'Para sites com mais tráfego', 49.99, 69.99, '50GB', '500GB', 5, 10, 3, 1, 20.00, 1),
('Plano Avançado', 'Melhor desempenho e recursos', 99.99, 129.99, '200GB', '1TB', 10, 50, 10, 1, 30.00, 1);


/*Tabela: assinaturas*/
CREATE TABLE `assinaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `plano_id` int(11) NOT NULL,
  `data_inicio` datetime DEFAULT current_timestamp(),
  `data_vencimento` datetime DEFAULT NULL,
  `status` enum('ativo','cancelado','suspenso') DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `plano_id` (`plano_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Inserts: assinaturas */
INSERT INTO assinaturas (usuario_id, plano_id, data_vencimento, status) VALUES 
(1, 1, '2025-06-14 23:59:59', 'ativo'),
(2, 2, '2025-06-14 23:59:59', 'ativo'),
(3, 3, '2025-06-14 23:59:59', 'cancelado');


/* Tabela: pagamentos */
CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assinatura_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `metodo` enum('cartao','pix') NOT NULL,
  `data_pagamento` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','completo','falhou') DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  KEY `assinatura_id` (`assinatura_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/* Inserts: pagamentos */
INSERT INTO pagamentos (assinatura_id, valor, metodo, status) VALUES 
(1, 19.99, 'cartao', 'completo'),
(2, 49.99, 'pix', 'completo'),
(3, 99.99, 'cartao', 'pendente');


/*Tabela: arquivos */
CREATE TABLE `arquivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(20) DEFAULT NULL,
  `nome_arquivo` varchar(30) DEFAULT NULL,
  `tamanho` int(11) DEFAULT NULL,
  `data_upload` datetime(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

