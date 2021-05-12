-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: sgiweb
-- ------------------------------------------------------
-- Server version	8.0.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `inspecoes`
--

DROP TABLE IF EXISTS `inspecoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inspecoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidade_id` bigint unsigned NOT NULL,
  `descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciclo` year NOT NULL,
  `tipoUnidade_id` bigint NOT NULL,
  `tipoVerificacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Em Inspeção','Inspecionado','Em Análise','Em Manifestação','Concluida') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Em Inspeção',
  `inspetorcoordenador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspetorcolaborador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datainiPreInspeção` date NOT NULL,
  `NumHrsPreInsp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NumHrsDesloc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NumHrsInsp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eventoInspecao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `xml` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalPontos` decimal(6,2) DEFAULT '0.00',
  `totalpontosInspetor` decimal(6,2) DEFAULT '0.00',
  `pontuacaoFinal` decimal(6,2) DEFAULT '0.00',
  `valor_ref_itens_inspecionados` decimal(6,2) DEFAULT '0.00',
  `totalpontosnaoconforme` decimal(4,2) DEFAULT '0.00',
  `tnc` decimal(4,2) DEFAULT '0.00',
  `totalitensavaliados` int DEFAULT NULL,
  `totalitensnaoconforme` int DEFAULT NULL,
  `classificacao` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_programacao` date DEFAULT NULL,
  `job_programado` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inspecoes_unidade_id_index` (`unidade_id`),
  CONSTRAINT `inspecoes_unidade_id_foreign` FOREIGN KEY (`unidade_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2920 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itensdeinspecoes`
--

DROP TABLE IF EXISTS `itensdeinspecoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itensdeinspecoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspecao_id` bigint unsigned NOT NULL,
  `unidade_id` bigint unsigned NOT NULL,
  `tipoUnidade_id` bigint unsigned NOT NULL,
  `grupoVerificacao_id` bigint unsigned NOT NULL,
  `testeVerificacao_id` bigint unsigned NOT NULL,
  `avaliacao` enum('Conforme','Não Conforme','Não Executa Tarefa','Não Verificado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Conforme',
  `oportunidadeAprimoramento` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `diretorio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagem` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `evidencia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `consequencias` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `reincidencia` enum('Sim','Não') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Não',
  `codVerificacaoAnterior` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeroGrupoReincidente` int DEFAULT NULL,
  `numeroItemReincidente` int DEFAULT NULL,
  `itemQuantificado` enum('Sim','Não') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Não',
  `valorFalta` decimal(8,2) NOT NULL DEFAULT '0.00',
  `valorSobra` decimal(8,2) NOT NULL DEFAULT '0.00',
  `valorRisco` decimal(8,2) NOT NULL DEFAULT '0.00',
  `orientacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `norma` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `situacao` enum('Em Inspeção','Inspecionado','Corroborado','Concluido','Não Respondido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eventosSistema` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pontuado` decimal(4,2) NOT NULL DEFAULT '0.00',
  `pontuadoInspetor` decimal(6,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `itensdeinspecoes_inspecao_id_index` (`inspecao_id`),
  KEY `itensdeinspecoes_unidade_id_index` (`unidade_id`),
  KEY `itensdeinspecoes_tipounidade_id_index` (`tipoUnidade_id`),
  KEY `itensdeinspecoes_grupoverificacao_id_index` (`grupoVerificacao_id`),
  KEY `itensdeinspecoes_testeverificacao_id_index` (`testeVerificacao_id`),
  CONSTRAINT `itensdeinspecoes_grupoverificacao_id_foreign` FOREIGN KEY (`grupoVerificacao_id`) REFERENCES `gruposdeverificacao` (`id`),
  CONSTRAINT `itensdeinspecoes_inspecao_id_foreign` FOREIGN KEY (`inspecao_id`) REFERENCES `inspecoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `itensdeinspecoes_testeverificacao_id_foreign` FOREIGN KEY (`testeVerificacao_id`) REFERENCES `testesdeverificacao` (`id`),
  CONSTRAINT `itensdeinspecoes_tipounidade_id_foreign` FOREIGN KEY (`tipoUnidade_id`) REFERENCES `tiposdeunidade` (`id`),
  CONSTRAINT `itensdeinspecoes_unidade_id_foreign` FOREIGN KEY (`unidade_id`) REFERENCES `unidades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57878 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sequence_inspcaos`
--

DROP TABLE IF EXISTS `sequence_inspcaos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sequence_inspcaos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `se` bigint NOT NULL,
  `sequence` bigint NOT NULL,
  `ciclo` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'sgiweb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-12  6:53:18
