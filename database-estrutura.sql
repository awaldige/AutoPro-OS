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
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipe`
--

CREATE TABLE `equipe` (
  `idEquipe` int(11) NOT NULL,
  `Nome` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--

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
--

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
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `mecanicos_equipe`
--

CREATE TABLE `mecanicos_equipe` (
  `Mecanicos_idMecanicos` int(11) NOT NULL,
  `Equipe_idEquipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--

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
--

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
--

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
--

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
--

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
--

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
--

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
--

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
  MODIFY `idClientes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `equipe`
--
ALTER TABLE `equipe`
  MODIFY `idEquipe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_os`
--
ALTER TABLE `itens_os`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mecanicos`
--
ALTER TABLE `mecanicos`
  MODIFY `idMecanicos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ordemdeservico`
--
ALTER TABLE `ordemdeservico`
  MODIFY `idOrdemServico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pecas`
--
ALTER TABLE `pecas`
  MODIFY `idPecas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servico`
--
ALTER TABLE `servico`
  MODIFY `idServico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tabelareferenciamaoobra`
--
ALTER TABLE `tabelareferenciamaoobra`
  MODIFY `idTabelaReferenciaMaoObra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `idVeiculos` int(11) NOT NULL AUTO_INCREMENT;

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
