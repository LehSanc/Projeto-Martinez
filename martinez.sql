-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 04/10/2024 às 01:42
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
-- Banco de dados: `Martinez`
--
  CREATE DATABASE martinez;

  USE martinez;
-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `ID_servico` int(255) NOT NULL,
  `username` varchar(20) NOT NULL,
  `imagem` varchar(100) DEFAULT NULL,
  `data` date NOT NULL,
  `marca` varchar(30) NOT NULL,
  `modelo` varchar(30) NOT NULL,
  `chassi` varchar(30) NOT NULL,
  `placa` varchar(30) NOT NULL,
  `endereco` varchar(30) NOT NULL,
  `servico_prestado` varchar(30) NOT NULL,
  `aux1` varchar(11) NOT NULL,
  `aux2` varchar(11) NOT NULL,
  `aux3` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` tinyint(1) DEFAULT 0 COMMENT '2 = usuário adm, 1 = usuário comum, 0 = usuário sem acesso ',
  `username` varchar(20) NOT NULL,
  `nomeCompleto` varchar(40) NOT NULL,
  `email` varchar(40) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `funcao` varchar(20) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `passaporte` varchar(20) NOT NULL,
  `A1` varchar(20) NOT NULL,
  `fotoDNI` varchar(40) NOT NULL,
  `fotoPassaporte` varchar(40) NOT NULL,
  `fotoA1` varchar(40) NOT NULL,
  `senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`ID_servico`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `ID_servico` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
