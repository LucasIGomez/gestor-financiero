-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: gestor_financiero
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(50) NOT NULL,
  `tipo_flujo` enum('ingreso','gasto') NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_categoria`),
  KEY `fk_categorias_usuarios_idx` (`id_usuario`),
  CONSTRAINT `fk_categorias_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deudas`
--

DROP TABLE IF EXISTS `deudas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deudas` (
  `id_deuda` int NOT NULL AUTO_INCREMENT,
  `nombre_deuda` varchar(100) NOT NULL,
  `saldo_total` decimal(12,2) NOT NULL,
  `cuota_mensual` decimal(10,2) NOT NULL,
  `tipo_deuda` enum('prestamo','tarjeta_credito') NOT NULL,
  `cft` decimal(10,2) NOT NULL,
  `tna` decimal(10,2) DEFAULT NULL,
  `tea` int DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `limite_credito` decimal(12,2) DEFAULT NULL,
  `dia_cierre` int DEFAULT NULL,
  `dia_vencimiento` int DEFAULT NULL,
  `cuotas_totales` int DEFAULT NULL,
  `cuotas_pagadas` int DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  PRIMARY KEY (`id_deuda`),
  KEY `fk_deudas_usuarios1_idx` (`id_usuario`),
  CONSTRAINT `fk_deudas_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gastos_recurrentes`
--

DROP TABLE IF EXISTS `gastos_recurrentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos_recurrentes` (
  `id_recurrente` int NOT NULL AUTO_INCREMENT,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `dia_cobro` int NOT NULL,
  `ultimo_procesamiento` date DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `id_categoria` int NOT NULL,
  PRIMARY KEY (`id_recurrente`),
  KEY `fk_gastos_recurrentes_usuarios1_idx` (`id_usuario`),
  KEY `fk_gastos_recurrentes_categorias1_idx` (`id_categoria`),
  CONSTRAINT `fk_gastos_recurrentes_categorias1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `fk_gastos_recurrentes_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `metas_financieras`
--

DROP TABLE IF EXISTS `metas_financieras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metas_financieras` (
  `id_meta` int NOT NULL AUTO_INCREMENT,
  `nombre_meta` varchar(100) NOT NULL,
  `monto_objetivo` decimal(12,2) NOT NULL,
  `saldo_actual` decimal(12,2) NOT NULL,
  `fecha_limite` date NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_meta`),
  KEY `fk_metas_financieras_usuarios1_idx` (`id_usuario`),
  CONSTRAINT `fk_metas_financieras_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transacciones`
--

DROP TABLE IF EXISTS `transacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transacciones` (
  `id_transaccion` int NOT NULL AUTO_INCREMENT,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_transaccion` date NOT NULL,
  `id_usuario` int NOT NULL,
  `id_categoria` int NOT NULL,
  `id_deuda` int DEFAULT NULL,
  PRIMARY KEY (`id_transaccion`),
  KEY `fk_transacciones_usuarios1_idx` (`id_usuario`),
  KEY `fk_transacciones_categorias1_idx` (`id_categoria`),
  KEY `fk_transacciones_deudas1_idx` (`id_deuda`),
  CONSTRAINT `fk_transacciones_categorias1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `fk_transacciones_deudas1` FOREIGN KEY (`id_deuda`) REFERENCES `deudas` (`id_deuda`),
  CONSTRAINT `fk_transacciones_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-08 13:40:45
