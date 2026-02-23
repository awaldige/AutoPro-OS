-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Tempo de geração: 23/02/2026 às 18:54
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `oficina`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `idClientes` int(11) NOT NULL,
  `NomeCliente` varchar(45) NOT NULL,
  `Endereco` varchar(45) DEFAULT NULL,
  `Telefone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`idClientes`, `NomeCliente`, `Endereco`, `Telefone`) VALUES
(1, 'João Silva', 'Rua A, 123', '11987654321'),
(2, 'Maria Oliveira', 'Avenida B, 456', '11976543210'),
(3, 'Carlos Santos', 'Travessa C, 789', '11965432198'),
(4, 'andre wal ', 'rua estreita 222', '11978574369'),
(5, 'andré wal', 'aleluia 227 93', '11978756382');

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipe`
--

CREATE TABLE `equipe` (
  `idEquipe` int(11) NOT NULL,
  `Nome` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipe`
--

INSERT INTO `equipe` (`idEquipe`, `Nome`) VALUES
(1, 'Equipe Alfa'),
(2, 'Equipe Beta'),
(3, 'Equipe Gama');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_os`
--

CREATE TABLE `itens_os` (
  `idItem` int(11) NOT NULL,
  `idOrdemServico` int(11) NOT NULL,
  `Descricao` varchar(255) NOT NULL,
  `Quantidade` decimal(10,2) NOT NULL DEFAULT 1.00,
  `ValorUnitario` decimal(10,2) NOT NULL,
  `Tipo` enum('Peça','Serviço') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_os`
--

INSERT INTO `itens_os` (`idItem`, `idOrdemServico`, `Descricao`, `Quantidade`, `ValorUnitario`, `Tipo`) VALUES
(19, 4, 'Pastilha de freio', 2.00, 125.00, 'Peça'),
(20, 4, 'troca de óleo ', 1.00, 70.00, 'Serviço'),
(21, 4, 'mão de obra  ', 1.00, 85.00, 'Serviço'),
(22, 3, 'pastilhas de freio ', 2.00, 90.00, 'Peça'),
(23, 3, 'óleo', 1.00, 100.00, 'Serviço'),
(24, 3, 'filtro', 1.00, 85.00, 'Peça'),
(25, 7, 'Velas de Igniçã', 1.00, 125.00, 'Peça'),
(26, 7, 'Filtro de Combustível', 1.00, 140.00, 'Peça'),
(27, 7, 'mão de obra ', 1.00, 85.00, 'Serviço');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mecanicos`
--

CREATE TABLE `mecanicos` (
  `idMecanicos` int(11) NOT NULL,
  `CodigoMecanico` int(11) NOT NULL,
  `Nome` varchar(45) NOT NULL,
  `Endereco` varchar(45) DEFAULT NULL,
  `Especialidade` varchar(45) DEFAULT NULL,
  `Servico_idServico` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mecanicos`
--

INSERT INTO `mecanicos` (`idMecanicos`, `CodigoMecanico`, `Nome`, `Endereco`, `Especialidade`, `Servico_idServico`) VALUES
(1, 1001, 'Pedro Lima', 'Rua X, 101', 'Suspensão', 1),
(2, 1002, 'Lucas Souza', 'Avenida Y, 202', 'Motor', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `mecanicos_equipe`
--

CREATE TABLE `mecanicos_equipe` (
  `Mecanicos_idMecanicos` int(11) NOT NULL,
  `Equipe_idEquipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mecanicos_equipe`
--

INSERT INTO `mecanicos_equipe` (`Mecanicos_idMecanicos`, `Equipe_idEquipe`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordemdeservico`
--

CREATE TABLE `ordemdeservico` (
  `idOrdemServico` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `NumeroOS` int(11) NOT NULL,
  `DataEmissao` date NOT NULL,
  `Valor` decimal(10,2) NOT NULL,
  `Status` varchar(30) DEFAULT NULL,
  `Descricao` text DEFAULT NULL,
  `DataEntrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ordemdeservico`
--

INSERT INTO `ordemdeservico` (`idOrdemServico`, `veiculo_id`, `NumeroOS`, `DataEmissao`, `Valor`, `Status`, `Descricao`, `DataEntrega`) VALUES
(1, 1, 3, '2025-02-15', 0.00, 'Em Aberto', '', '2026-02-22'),
(2, 2, 2, '2025-02-16', 1200.00, 'Concluída', NULL, '2026-02-23'),
(3, 3, 1, '2026-02-23', 365.00, 'Em Aberto', 'troca de pastilhas de freio  óleo e filtro', '2026-02-19'),
(4, 4, 4, '2026-02-23', 405.00, 'Em Aberto', 'O carro está engasgando nas retomadas de velocidade, principalmente em subidas. Sinto que ele perdeu a potência e a luz da injeção eletrônica piscou no painel algumas vezes hoje de manhã. O motor parece estar \'manco\' ou trabalhando de forma irregular.', '2026-02-24'),
(7, 1, 5, '2026-02-23', 350.00, 'Em Andamento', 'O carro está engasgando nas retomadas de velocidade, principalmente em subidas. Sinto que ele perdeu a potência e a luz da injeção eletrônica piscou no painel algumas vezes hoje de manhã. O motor parece estar \'manco\' ou trabalhando de forma irregular.\"', '2026-02-24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordemdeservico_pecas`
--

CREATE TABLE `ordemdeservico_pecas` (
  `OrdemDeServico_idOrdemServico` int(11) NOT NULL,
  `Pecas_idPecas` int(11) NOT NULL,
  `Quantidade` int(11) NOT NULL,
  `SubtotalPecas` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ordemdeservico_pecas`
--

INSERT INTO `ordemdeservico_pecas` (`OrdemDeServico_idOrdemServico`, `Pecas_idPecas`, `Quantidade`, `SubtotalPecas`) VALUES
(1, 1, 1, 50.00),
(2, 2, 1, 120.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordemdeservico_servico`
--

CREATE TABLE `ordemdeservico_servico` (
  `OrdemDeServico_idOrdemServico` int(11) NOT NULL,
  `Servico_idServico` int(11) NOT NULL,
  `Quantidade` int(11) NOT NULL,
  `SubtotalMaoObra` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ordemdeservico_servico`
--

INSERT INTO `ordemdeservico_servico` (`OrdemDeServico_idOrdemServico`, `Servico_idServico`, `Quantidade`, `SubtotalMaoObra`) VALUES
(1, 1, 1, 150.00),
(2, 2, 1, 250.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pecas`
--

CREATE TABLE `pecas` (
  `idPecas` int(11) NOT NULL,
  `Descricao` varchar(45) NOT NULL,
  `Valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pecas`
--

INSERT INTO `pecas` (`idPecas`, `Descricao`, `Valor`) VALUES
(1, 'Óleo 5W30', 50.00),
(2, 'Pastilha Freio', 120.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico`
--

CREATE TABLE `servico` (
  `idServico` int(11) NOT NULL,
  `Descricao` varchar(45) NOT NULL,
  `TabelaReferenciaMaoObra_idTabelaReferenciaMaoObra` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servico`
--

INSERT INTO `servico` (`idServico`, `Descricao`, `TabelaReferenciaMaoObra_idTabelaReferenciaMaoObra`) VALUES
(1, 'Troca de óleo', 1),
(2, 'Alinhamento', 2),
(3, 'Troca de Freios', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tabelareferenciamaoobra`
--

CREATE TABLE `tabelareferenciamaoobra` (
  `idTabelaReferenciaMaoObra` int(11) NOT NULL,
  `Descricao` varchar(45) NOT NULL,
  `ValorMaoObra` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tabelareferenciamaoobra`
--

INSERT INTO `tabelareferenciamaoobra` (`idTabelaReferenciaMaoObra`, `Descricao`, `ValorMaoObra`) VALUES
(1, 'Serviço Básico', 150.00),
(2, 'Serviço Intermediário', 250.00),
(3, 'Serviço Avançado', 400.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `idVeiculos` int(11) NOT NULL,
  `Marca` varchar(45) NOT NULL,
  `Modelo` varchar(45) NOT NULL,
  `AnoFabricacao` int(11) DEFAULT NULL,
  `Placa` char(8) NOT NULL,
  `Clientes_idClientes` int(11) DEFAULT NULL,
  `Equipe_idEquipe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`idVeiculos`, `Marca`, `Modelo`, `AnoFabricacao`, `Placa`, `Clientes_idClientes`, `Equipe_idEquipe`) VALUES
(1, 'Toyota', 'Corolla', 2020, 'ABC-1234', 1, 1),
(2, 'Honda', 'Civic', 2018, 'DEF-5678', 2, 2),
(3, 'Ford', 'Focus', 2019, 'GHI-9012', 3, 1),
(4, 'GM ', 'onix', 2016, 'fsd 5742', 5, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`idClientes`);

--
-- Índices de tabela `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`idEquipe`);

--
-- Índices de tabela `itens_os`
--
ALTER TABLE `itens_os`
  ADD PRIMARY KEY (`idItem`),
  ADD KEY `idOrdemServico` (`idOrdemServico`);

--
-- Índices de tabela `mecanicos`
--
ALTER TABLE `mecanicos`
  ADD PRIMARY KEY (`idMecanicos`),
  ADD UNIQUE KEY `CodigoMecanico` (`CodigoMecanico`),
  ADD KEY `Servico_idServico` (`Servico_idServico`);

--
-- Índices de tabela `mecanicos_equipe`
--
ALTER TABLE `mecanicos_equipe`
  ADD PRIMARY KEY (`Mecanicos_idMecanicos`,`Equipe_idEquipe`),
  ADD KEY `Equipe_idEquipe` (`Equipe_idEquipe`);

--
-- Índices de tabela `ordemdeservico`
--
ALTER TABLE `ordemdeservico`
  ADD PRIMARY KEY (`idOrdemServico`),
  ADD UNIQUE KEY `NumeroOS` (`NumeroOS`);

--
-- Índices de tabela `ordemdeservico_pecas`
--
ALTER TABLE `ordemdeservico_pecas`
  ADD PRIMARY KEY (`OrdemDeServico_idOrdemServico`,`Pecas_idPecas`),
  ADD KEY `Pecas_idPecas` (`Pecas_idPecas`);

--
-- Índices de tabela `ordemdeservico_servico`
--
ALTER TABLE `ordemdeservico_servico`
  ADD PRIMARY KEY (`OrdemDeServico_idOrdemServico`,`Servico_idServico`),
  ADD KEY `Servico_idServico` (`Servico_idServico`);

--
-- Índices de tabela `pecas`
--
ALTER TABLE `pecas`
  ADD PRIMARY KEY (`idPecas`);

--
-- Índices de tabela `servico`
--
ALTER TABLE `servico`
  ADD PRIMARY KEY (`idServico`),
  ADD KEY `TabelaReferenciaMaoObra_idTabelaReferenciaMaoObra` (`TabelaReferenciaMaoObra_idTabelaReferenciaMaoObra`);

--
-- Índices de tabela `tabelareferenciamaoobra`
--
ALTER TABLE `tabelareferenciamaoobra`
  ADD PRIMARY KEY (`idTabelaReferenciaMaoObra`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`idVeiculos`),
  ADD UNIQUE KEY `Placa` (`Placa`),
  ADD KEY `Clientes_idClientes` (`Clientes_idClientes`),
  ADD KEY `Equipe_idEquipe` (`Equipe_idEquipe`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `idClientes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `equipe`
--
ALTER TABLE `equipe`
  MODIFY `idEquipe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `itens_os`
--
ALTER TABLE `itens_os`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `mecanicos`
--
ALTER TABLE `mecanicos`
  MODIFY `idMecanicos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `ordemdeservico`
--
ALTER TABLE `ordemdeservico`
  MODIFY `idOrdemServico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `pecas`
--
ALTER TABLE `pecas`
  MODIFY `idPecas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `servico`
--
ALTER TABLE `servico`
  MODIFY `idServico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tabelareferenciamaoobra`
--
ALTER TABLE `tabelareferenciamaoobra`
  MODIFY `idTabelaReferenciaMaoObra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `idVeiculos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_os`
--
ALTER TABLE `itens_os`
  ADD CONSTRAINT `itens_os_ibfk_1` FOREIGN KEY (`idOrdemServico`) REFERENCES `ordemdeservico` (`idOrdemServico`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mecanicos`
--
ALTER TABLE `mecanicos`
  ADD CONSTRAINT `mecanicos_ibfk_1` FOREIGN KEY (`Servico_idServico`) REFERENCES `servico` (`idServico`);

--
-- Restrições para tabelas `mecanicos_equipe`
--
ALTER TABLE `mecanicos_equipe`
  ADD CONSTRAINT `mecanicos_equipe_ibfk_1` FOREIGN KEY (`Mecanicos_idMecanicos`) REFERENCES `mecanicos` (`idMecanicos`),
  ADD CONSTRAINT `mecanicos_equipe_ibfk_2` FOREIGN KEY (`Equipe_idEquipe`) REFERENCES `equipe` (`idEquipe`);

--
-- Restrições para tabelas `ordemdeservico_pecas`
--
ALTER TABLE `ordemdeservico_pecas`
  ADD CONSTRAINT `ordemdeservico_pecas_ibfk_1` FOREIGN KEY (`OrdemDeServico_idOrdemServico`) REFERENCES `ordemdeservico` (`idOrdemServico`),
  ADD CONSTRAINT `ordemdeservico_pecas_ibfk_2` FOREIGN KEY (`Pecas_idPecas`) REFERENCES `pecas` (`idPecas`);

--
-- Restrições para tabelas `ordemdeservico_servico`
--
ALTER TABLE `ordemdeservico_servico`
  ADD CONSTRAINT `ordemdeservico_servico_ibfk_1` FOREIGN KEY (`OrdemDeServico_idOrdemServico`) REFERENCES `ordemdeservico` (`idOrdemServico`),
  ADD CONSTRAINT `ordemdeservico_servico_ibfk_2` FOREIGN KEY (`Servico_idServico`) REFERENCES `servico` (`idServico`);

--
-- Restrições para tabelas `servico`
--
ALTER TABLE `servico`
  ADD CONSTRAINT `servico_ibfk_1` FOREIGN KEY (`TabelaReferenciaMaoObra_idTabelaReferenciaMaoObra`) REFERENCES `tabelareferenciamaoobra` (`idTabelaReferenciaMaoObra`);

--
-- Restrições para tabelas `veiculos`
--
ALTER TABLE `veiculos`
  ADD CONSTRAINT `veiculos_ibfk_1` FOREIGN KEY (`Clientes_idClientes`) REFERENCES `clientes` (`idClientes`),
  ADD CONSTRAINT `veiculos_ibfk_2` FOREIGN KEY (`Equipe_idEquipe`) REFERENCES `equipe` (`idEquipe`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
