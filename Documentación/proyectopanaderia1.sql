-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: proyectopanaderia
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `administradores`
--

DROP TABLE IF EXISTS `administradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores` (
  `ID_ADMIN` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_ADMIN` varchar(100) NOT NULL,
  `TELEFONO_ADMIN` varchar(20) DEFAULT NULL,
  `EMAIL_ADMIN` varchar(100) DEFAULT NULL,
  `CONTRASENA_ADMIN` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_ADMIN`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores`
--

LOCK TABLES `administradores` WRITE;
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
INSERT INTO `administradores` VALUES (1,'Admin Uno','3005550101','admin1@store.com','240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9');
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria_ingredientes`
--

DROP TABLE IF EXISTS `categoria_ingredientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_ingredientes` (
  `ID_CATEGORIA` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_CATEGORIA_INGREDIENTE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_CATEGORIA`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_ingredientes`
--

LOCK TABLES `categoria_ingredientes` WRITE;
/*!40000 ALTER TABLE `categoria_ingredientes` DISABLE KEYS */;
INSERT INTO `categoria_ingredientes` VALUES (1,'Frutas Frescas'),(2,'Lácteos y Derivados'),(3,'Azúcares y Endulzantes'),(4,'Grasas'),(5,'Esencias'),(6,'Fruta'),(7,'Frutos Secos'),(8,'Levaduras'),(9,'Huevos'),(10,'Chocolate y Cacao'),(11,'Espesantes y Gelificantes'),(12,'Colorantes Alimentarios'),(13,'Sal'),(14,'Aditivos y Mejoradores'),(15,'Semillas'),(16,'Coberturas y Rellenos'),(17,'Especias');
/*!40000 ALTER TABLE `categoria_ingredientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria_productos`
--

DROP TABLE IF EXISTS `categoria_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria_productos` (
  `ID_CATEGORIA_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_CATEGORIAPRODUCTO` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_CATEGORIA_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_productos`
--

LOCK TABLES `categoria_productos` WRITE;
/*!40000 ALTER TABLE `categoria_productos` DISABLE KEYS */;
INSERT INTO `categoria_productos` VALUES (1,'Tortas Tres Leches'),(2,'Tortas Milyway'),(3,'Tortas por Encargo'),(4,'Pan Grande'),(5,'Pan Pequeño'),(6,'Postres'),(7,'Galletas'),(8,'Tamales'),(9,'Yogures'),(10,'Pasteles Pollo');
/*!40000 ALTER TABLE `categoria_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `ID_CLIENTE` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_CLI` varchar(100) NOT NULL,
  `TELEFONO_CLI` varchar(20) DEFAULT NULL,
  `ACTIVO_CLI` tinyint(1) DEFAULT 1,
  `EMAIL_CLI` varchar(100) DEFAULT NULL,
  `CONTRASENA_CLI` varchar(255) DEFAULT NULL,
  `FECHA_ULTIMA_MODIFICACION` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_CLIENTE`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Ana Pérez','3101234567',1,'ana.p@mail.com','09a31a7001e261ab1e056182a71d3cf57f582ca9a29cff5eb83be0f0549730a9','2025-08-31 23:10:39'),(2,'Luis Gómez','3209876543',1,'luis.g@mail.com','8d0cb54601bfecce6840eff8c2b0a4fb2d5e52cfc7754997d2902eb2306fc275','2025-08-31 23:10:39'),(3,'Maria Rodriguez','3001122334',1,'maria.r@mail.com','d6a2339d155e81f11349280374b228b27273e8f7725a1d2f0feae84c95caa2f9','2025-08-31 23:10:39'),(4,'damian','3233437537',1,'damian@cliente.com','8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92','2025-10-02 05:01:07');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_clientes_validar_email
    BEFORE INSERT ON Clientes
    FOR EACH ROW
BEGIN
    IF NEW.EMAIL_CLI IS NOT NULL AND NEW.EMAIL_CLI NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email inválido para cliente';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_clientes_fecha_modificacion
    BEFORE UPDATE ON Clientes
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `detalle_pedidos`
--

DROP TABLE IF EXISTS `detalle_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedidos` (
  `ID_DETALLE` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PEDIDO` int(11) NOT NULL,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD_PRODUCTO` int(11) DEFAULT NULL,
  `PRECIO_UNITARIO` decimal(10,2) DEFAULT NULL,
  `SUBTOTAL` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`ID_DETALLE`),
  KEY `FK_DETALLE_PEDIDO` (`ID_PEDIDO`),
  KEY `FK_DETALLE_PRODUCTO` (`ID_PRODUCTO`),
  CONSTRAINT `FK_DETALLE_PEDIDO` FOREIGN KEY (`ID_PEDIDO`) REFERENCES `pedidos` (`ID_PEDIDO`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DETALLE_PRODUCTO` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `productos` (`ID_PRODUCTO`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos`
--

LOCK TABLES `detalle_pedidos` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos` DISABLE KEYS */;
INSERT INTO `detalle_pedidos` VALUES (1,1,1,2,3500.00,7000.00),(2,1,3,1,6000.00,6000.00),(3,2,2,1,4200.00,4200.00),(4,2,4,3,2500.00,7500.00),(5,3,1,3,3500.00,10500.00),(6,3,4,2,2500.00,5000.00),(13,10,1,1,3800.00,3800.00),(14,11,1,2,3800.00,7600.00),(15,13,1,1,3800.00,3800.00),(16,13,4,3,2500.00,7500.00),(17,13,26,2,1800.00,3600.00);
/*!40000 ALTER TABLE `detalle_pedidos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_actualizar_total_pedido
AFTER INSERT ON Detalle_Pedidos
FOR EACH ROW
BEGIN
    UPDATE Pedidos 
    SET TOTAL_PRODUCTO = (
        SELECT SUM(SUBTOTAL) 
        FROM Detalle_Pedidos 
        WHERE ID_PEDIDO = NEW.ID_PEDIDO
    )
    WHERE ID_PEDIDO = NEW.ID_PEDIDO;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_actualizar_total_pedido_update
AFTER UPDATE ON Detalle_Pedidos
FOR EACH ROW
BEGIN
    UPDATE Pedidos 
    SET TOTAL_PRODUCTO = (
        SELECT SUM(SUBTOTAL) 
        FROM Detalle_Pedidos 
        WHERE ID_PEDIDO = NEW.ID_PEDIDO
    )
    WHERE ID_PEDIDO = NEW.ID_PEDIDO;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `ID_EMPLEADO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_EMPLEADO` varchar(100) NOT NULL,
  `EMAIL_EMPLEADO` varchar(100) DEFAULT NULL,
  `ACTIVO_EMPLEADO` tinyint(1) DEFAULT 1,
  `CONTRASENA_EMPLEADO` varchar(255) DEFAULT NULL,
  `FECHA_REGISTRO` timestamp NOT NULL DEFAULT current_timestamp(),
  `FECHA_ULTIMA_MODIFICACION` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_EMPLEADO`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'Andres Alkaeda',NULL,1,'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f','2025-08-31 23:10:39','2025-08-31 23:10:39'),(2,'Damian Avila',NULL,1,'e17fa44d1243206b4e1345a7e8d4c804809fd7533e8fb725a2a26c903bb8e4e0','2025-08-31 23:10:39','2025-08-31 23:10:39'),(3,'Brayan Jimenez',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(4,'Ana Goyeneche',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(5,'Sharyt Zamora',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(6,'Carlos Mendoza',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(7,'Sofia Rodriguez',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(8,'Miguel Torres',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(9,'Valentina Castro',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(10,'Diego Herrera',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(11,'Camila Vargas',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(12,'Alejandro Morales',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(13,'Isabella Gutierrez',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(14,'Sebastian Ramirez',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39'),(15,'Natalia Delgado',NULL,1,NULL,'2025-08-31 23:10:39','2025-08-31 23:10:39');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_empleados_validar_email
    BEFORE INSERT ON Empleados
    FOR EACH ROW
BEGIN
    IF NEW.EMAIL_EMPLEADO IS NOT NULL AND NEW.EMAIL_EMPLEADO NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email inválido para empleado';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_empleados_fecha_modificacion
    BEFORE UPDATE ON Empleados
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `estado_pedidos`
--

DROP TABLE IF EXISTS `estado_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_pedidos` (
  `ID_ESTADO_PEDIDO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_ESTADO` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_ESTADO_PEDIDO`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_pedidos`
--

LOCK TABLES `estado_pedidos` WRITE;
/*!40000 ALTER TABLE `estado_pedidos` DISABLE KEYS */;
INSERT INTO `estado_pedidos` VALUES (1,'Pendiente'),(2,'En Preparación'),(3,'Listo para Entrega'),(4,'Entregado'),(5,'Cancelado');
/*!40000 ALTER TABLE `estado_pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingredientes`
--

DROP TABLE IF EXISTS `ingredientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredientes` (
  `ID_INGREDIENTE` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PROVEEDOR` int(11) DEFAULT NULL,
  `ID_CATEGORIA` int(11) DEFAULT NULL,
  `NOMBRE_INGREDIENTE` varchar(100) NOT NULL,
  `CANTIDAD_INGREDIENTE` int(11) DEFAULT NULL,
  `FECHA_VENCIMIENTO` date DEFAULT NULL,
  `REFERENCIA_INGREDIENTE` varchar(100) DEFAULT NULL,
  `FECHA_ENTREGA_INGREDIENTE` date DEFAULT NULL,
  PRIMARY KEY (`ID_INGREDIENTE`),
  KEY `FK_PROVEEDOR_INGREDIENTE` (`ID_PROVEEDOR`),
  KEY `FK_CATEGORIA_INGREDIENTE` (`ID_CATEGORIA`),
  CONSTRAINT `FK_CATEGORIA_INGREDIENTE` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categoria_ingredientes` (`ID_CATEGORIA`) ON UPDATE CASCADE,
  CONSTRAINT `FK_PROVEEDOR_INGREDIENTE` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `proveedores` (`ID_PROVEEDOR`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredientes`
--

LOCK TABLES `ingredientes` WRITE;
/*!40000 ALTER TABLE `ingredientes` DISABLE KEYS */;
INSERT INTO `ingredientes` VALUES (1,2,1,'Harina de Trigo',1151,'2025-12-20','HAR-TRG-05','2025-07-01'),(2,1,2,'Leche Entera UHT',1030,'2025-08-01','LECH-ENT-1L','2025-07-01'),(3,3,3,'Azúcar Blanca',1050,'2026-01-30','AZUC-BLN-KG','2025-07-01'),(4,4,4,'Mantequilla sin Sal',1013,'2025-09-15','MANT-SS-KG','2025-07-01'),(6,6,10,'Chocolate Semi-Amargo (Gotas)',1005,'2026-03-10','CHOC-SM-KG','2025-07-01'),(7,7,8,'Levadura Fresca',1005,'2025-07-10','LEV-FRES-GR','2025-07-01'),(8,1,3,'Azúcar Moreno',1010,'2026-02-20','AZUC-MRN-KG','2025-07-01'),(9,2,1,'Harina Integral',1050,'2025-11-01','HAR-INT-02','2025-07-01'),(10,3,4,'Aceite Vegetal',1007,'2026-05-01','ACET-VEG-LT','2025-07-01'),(11,4,10,'Cacao en Polvo',1008,'2026-04-15','CACAO-POL-KG','2025-07-01'),(12,5,6,'Manzanas Verdes (Kg)',1010,'2025-07-12','MANZ-VRD-KG','2025-07-01'),(13,6,7,'Nueces Picadas',979,'2025-10-01','NUEZ-PIC-KG','2025-07-01'),(14,7,5,'Esencia de Vainilla',1002,'2027-01-01','ESEN-VN-LT','2025-07-01'),(15,8,13,'Sal Fina',1001,'2028-01-01','SAL-FIN-KG','2025-07-01'),(16,1,2,'Crema de Leche',1005,'2025-08-05','CREM-LECH-LT','2025-07-01'),(17,2,11,'Gelatina sin Sabor',1001,'2026-09-01','GEL-SS-KG','2025-07-01'),(18,3,12,'Colorante Alimentario Rojo',1001,'2027-03-01','COLR-ROJ-ML','2025-07-01'),(19,4,15,'Semillas de Sésamo',1003,'2026-06-01','SEM-SES-KG','2025-07-01'),(20,5,16,'Dulce de Leche',1010,'2025-11-15','DDL-KG','2025-07-01'),(21,1,13,'Sal de maria',1010,'2028-04-30','SAL-MAR-002','2025-08-25'),(22,1,3,'Jimorea',1004,'2025-07-04','GRR-TRT-TNT','2025-05-31'),(35,1,9,'Huevo',0,'2025-10-30','HUE-HUE-UND','2025-09-16'),(36,1,2,'Queso',100,'2025-10-30','QUE-COS-KG','2025-09-24'),(37,1,15,'Avena',100,'2025-10-29','AVN-KG','2025-09-22');
/*!40000 ALTER TABLE `ingredientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordenes_salida`
--

DROP TABLE IF EXISTS `ordenes_salida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordenes_salida` (
  `ID_FACTURA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_CLIENTE` int(11) DEFAULT NULL,
  `ID_PEDIDO` int(11) DEFAULT NULL,
  `FECHA_FACTURACION` datetime DEFAULT NULL,
  `TOTAL_FACTURA` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`ID_FACTURA`),
  KEY `FK_ORDENSALIDA_CLIENTE` (`ID_CLIENTE`),
  KEY `FK_ORDENSALIDA_PEDIDO` (`ID_PEDIDO`),
  CONSTRAINT `FK_ORDENSALIDA_CLIENTE` FOREIGN KEY (`ID_CLIENTE`) REFERENCES `clientes` (`ID_CLIENTE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ORDENSALIDA_PEDIDO` FOREIGN KEY (`ID_PEDIDO`) REFERENCES `pedidos` (`ID_PEDIDO`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordenes_salida`
--

LOCK TABLES `ordenes_salida` WRITE;
/*!40000 ALTER TABLE `ordenes_salida` DISABLE KEYS */;
INSERT INTO `ordenes_salida` VALUES (1,1,1,'2025-06-20 15:30:00',13000.00),(2,2,2,'2025-06-19 17:00:00',11700.00),(3,3,3,'2025-06-21 14:30:00',15500.00);
/*!40000 ALTER TABLE `ordenes_salida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `ID_PEDIDO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_CLIENTE` int(11) DEFAULT NULL,
  `ID_EMPLEADO` int(11) DEFAULT NULL,
  `ID_ESTADO_PEDIDO` int(11) DEFAULT NULL,
  `FECHA_INGRESO` datetime DEFAULT NULL,
  `FECHA_ENTREGA` datetime DEFAULT NULL,
  `TOTAL_PRODUCTO` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`ID_PEDIDO`),
  KEY `FK_CLIENTE_PEDIDO` (`ID_CLIENTE`),
  KEY `FK_EMPLEADO_PEDIDO` (`ID_EMPLEADO`),
  KEY `FK_ESTADO_PEDIDO_PEDIDO` (`ID_ESTADO_PEDIDO`),
  CONSTRAINT `FK_CLIENTE_PEDIDO` FOREIGN KEY (`ID_CLIENTE`) REFERENCES `clientes` (`ID_CLIENTE`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EMPLEADO_PEDIDO` FOREIGN KEY (`ID_EMPLEADO`) REFERENCES `empleados` (`ID_EMPLEADO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_ESTADO_PEDIDO_PEDIDO` FOREIGN KEY (`ID_ESTADO_PEDIDO`) REFERENCES `estado_pedidos` (`ID_ESTADO_PEDIDO`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,1,1,2,'2025-06-20 09:00:00','2025-06-20 15:00:00',1473000.00),(2,2,2,4,'2025-06-19 10:30:00','2025-06-19 16:30:00',245700.00),(3,3,1,1,'2025-06-21 08:00:00','2025-06-21 14:00:00',27822.00),(10,1,1,1,'2025-09-30 00:00:00',NULL,3800.00),(11,1,1,1,'2025-09-30 00:00:00',NULL,7600.00),(13,2,1,1,'2025-09-30 00:00:00',NULL,14900.00);
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos_proveedores`
--

DROP TABLE IF EXISTS `pedidos_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos_proveedores` (
  `ID_PEDIDO_PROV` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PROVEEDOR` int(11) NOT NULL,
  `NUMERO_PEDIDO` int(11) NOT NULL,
  `FECHA_PEDIDO` date NOT NULL,
  `ESTADO_PEDIDO` varchar(50) DEFAULT 'Pendiente',
  PRIMARY KEY (`ID_PEDIDO_PROV`),
  KEY `FK_PEDIDO_PROVEEDOR` (`ID_PROVEEDOR`),
  CONSTRAINT `FK_PEDIDO_PROVEEDOR` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `proveedores` (`ID_PROVEEDOR`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos_proveedores`
--

LOCK TABLES `pedidos_proveedores` WRITE;
/*!40000 ALTER TABLE `pedidos_proveedores` DISABLE KEYS */;
INSERT INTO `pedidos_proveedores` VALUES (2,2,1002,'2024-02-10','Entregado'),(3,3,1003,'2024-03-05','Entregado'),(4,3,2024003,'2024-08-29',NULL),(6,1,1,'2025-02-28','Preparación');
/*!40000 ALTER TABLE `pedidos_proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produccion`
--

DROP TABLE IF EXISTS `produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produccion` (
  `ID_PRODUCCION` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD_PRODUCIDA` int(11) NOT NULL,
  `FECHA_PRODUCCION` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_PRODUCCION`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  CONSTRAINT `produccion_ibfk_1` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `productos` (`ID_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produccion`
--

LOCK TABLES `produccion` WRITE;
/*!40000 ALTER TABLE `produccion` DISABLE KEYS */;
INSERT INTO `produccion` VALUES (1,1,60,'2025-10-01 11:28:15'),(3,1,10,'2025-10-01 14:31:50'),(4,1,2,'2025-10-02 00:49:45'),(5,1,12,'2025-10-02 01:39:51'),(6,1,1,'2025-10-02 01:40:20'),(7,1,2,'2025-10-02 03:06:43'),(8,1,1,'2025-10-02 03:06:59'),(9,1,2,'2025-10-02 03:38:38');
/*!40000 ALTER TABLE `produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `ID_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_ADMIN` int(11) DEFAULT NULL,
  `ID_CATEGORIA_PRODUCTO` int(11) DEFAULT NULL,
  `NOMBRE_PRODUCTO` varchar(100) NOT NULL,
  `DESCRIPCION_PRODUCTO` text DEFAULT NULL,
  `PRODUCTO_STOCK_MIN` int(11) DEFAULT NULL,
  `STOCK_ACTUAL` int(11) DEFAULT 0,
  `PRECIO_PRODUCTO` decimal(10,2) NOT NULL,
  `FECHA_VENCIMIENTO_PRODUCTO` date DEFAULT NULL,
  `FECHA_INGRESO_PRODUCTO` date DEFAULT NULL,
  `TIPO_PRODUCTO_MARCA` varchar(100) DEFAULT NULL,
  `ACTIVO` tinyint(1) DEFAULT 1,
  `FECHA_ULTIMA_MODIFICACION` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_PRODUCTO`),
  KEY `FK_CATEGORIA_PRODUCTO` (`ID_CATEGORIA_PRODUCTO`),
  KEY `FK_ADMIN_PRODUCTO` (`ID_ADMIN`),
  CONSTRAINT `FK_ADMIN_PRODUCTO` FOREIGN KEY (`ID_ADMIN`) REFERENCES `administradores` (`ID_ADMIN`) ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORIA_PRODUCTO` FOREIGN KEY (`ID_CATEGORIA_PRODUCTO`) REFERENCES `categoria_productos` (`ID_CATEGORIA_PRODUCTO`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,1,8,'Tamales Tolimenses','Tradicionales tamales envueltos en hoja de plátano, con masa de maíz y relleno de carne de cerdo y pollo',10,80,3800.00,'2025-09-15','2025-07-01','Propio',1,'2025-10-02 08:38:38'),(2,1,4,'Pan Tajado Integral','Pan de molde integral, ideal para desayunos saludables, rico en fibra',15,100,4200.00,'2025-07-02','2025-07-01','Propio',1,'2025-09-30 16:52:03'),(3,1,7,'Yogurt Fresa Litro','Yogurt cremoso con trozos de fresa natural, sin conservantes artificiales',12,100,6000.00,'2025-07-30','2025-07-03','Alpina',1,'2025-09-30 16:52:03'),(4,1,5,'Galleta de Tres Ojos','Galleta tradicional colombiana con tres círculos de dulce, crujiente y deliciosa',20,97,2500.00,'2025-11-01','2025-07-01','Propio',1,'2025-09-30 16:53:07'),(5,1,1,'Pan Campesino Grande','Pan artesanal de corteza dorada y miga suave, ideal para acompañar comidas',8,100,5500.00,'2025-07-08','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(6,1,3,'Torta de Chocolate Pequeña','Deliciosa torta de chocolate húmeda con cobertura de chocolate, perfecta para ocasiones especiales',5,100,18000.00,'2025-07-07','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(7,1,2,'Croissant de Almendras',NULL,18,200,3500.00,'2025-07-06','2025-07-05','Propio',1,'2025-10-01 16:32:57'),(8,1,1,'Baguette Clásica','Pan francés tradicional con corteza crujiente y miga aireada',25,100,2800.00,'2025-07-06','2025-07-05','Propio',1,'2025-09-30 16:52:03'),(9,1,5,'Bizcochos de Achira',NULL,15,100,4000.00,'2025-12-01','2025-07-01','Propio',1,'2025-09-30 16:52:03'),(10,1,6,'Jugo de Naranja Natural (500ml)','Jugo 100% natural exprimido de naranjas frescas, sin azúcar añadido',10,100,4500.00,'2025-07-05','2025-07-04','Postobón',1,'2025-09-30 16:52:03'),(11,1,7,'Postre de Tres Leches','Clásico postre colombiano empapado en tres tipos de leche, suave y cremoso',7,100,7500.00,'2025-07-08','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(12,1,4,'Pan Blanco de Molde',NULL,20,100,3900.00,'2025-07-02','2025-07-01','Propio',1,'2025-09-30 16:52:03'),(13,1,3,'Muffin de Arándanos','Muffin esponjoso con arándanos frescos, perfecto para el desayuno o merienda',15,100,3000.00,'2025-07-07','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(14,1,2,'Pan de Bono Pequeño',NULL,30,100,1500.00,'2025-07-06','2025-07-05','Propio',1,'2025-09-30 16:52:03'),(15,1,8,'Empanadas de Carne (unidad)','Empanada frita rellena de carne molida sazonada con especias tradicionales',20,100,2000.00,'2025-07-06','2025-07-05','Propio',1,'2025-09-30 16:52:03'),(16,1,3,'Brazo de Reina','Bizcocho enrollado relleno de dulce de leche y cubierto con coco rallado',10,100,9500.00,'2025-07-09','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(17,1,1,'Pan Trenza Integral',NULL,12,100,4800.00,'2025-07-07','2025-07-03','Propio',1,'2025-09-30 16:52:03'),(18,1,5,'Galletas Surtidas de Mantequilla','Variedad de galletas caseras de mantequilla con diferentes formas y sabores',25,100,3200.00,'2025-12-30','2025-07-01','Propio',1,'2025-09-30 16:52:03'),(19,1,7,'Avena La Lechera (500ml)',NULL,18,100,5800.00,'2025-08-20','2025-07-03','Nestlé',1,'2025-09-30 16:52:03'),(20,1,9,'Ponqué de Naranja (Porción)','Porción individual de ponqué de naranja con glaseado cítrico',15,100,3000.00,'2025-07-08','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(21,1,10,'Pan Artesanal de Masa Madre','Pan elaborado con masa madre natural, fermentación larga para mejor digestibilidad',7,100,8000.00,'2025-07-07','2025-07-05','Propio',1,'2025-09-30 16:52:03'),(22,1,3,'Cheesecake de Frutos Rojos','Cheesecake cremoso con base de galleta y cobertura de frutos rojos frescos',6,100,25000.00,'2025-07-09','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(23,1,4,'Pan de Hamburguesa',NULL,30,100,4500.00,'2025-07-10','2025-07-02','Propio',1,'2025-09-30 16:52:03'),(24,1,5,'Galletas de Avena y Pasas','Galletas nutritivas con avena integral y pasas, sin azúcar refinado',22,100,2700.00,'2026-01-01','2025-07-01','Propio',1,'2025-09-30 16:52:03'),(25,1,7,'Kumiss Natural',NULL,10,100,4900.00,'2025-07-25','2025-07-03','Alquería',1,'2025-09-30 16:52:03'),(26,1,9,'Brownie con Nuez','Brownie de chocolate intenso con trozos de nuez, húmedo y delicioso',40,98,1800.00,'2025-07-08','2025-07-05','Propio',1,'2025-09-30 16:53:07'),(27,1,1,'Pan Blandito',NULL,28,100,2500.00,'2025-07-07','2025-07-05','Propio',1,'2025-09-30 16:52:03'),(28,1,3,'Milhoja de Arequipe','Delicada milhoja rellena de arequipe casero y cubierta con azúcar glass',12,100,6000.00,'2025-07-08','2025-07-04','Propio',1,'2025-09-30 16:52:03'),(29,1,2,'Mogolla Chicharrona',NULL,15,100,3500.00,'2025-07-06','2025-07-05','Propio',1,'2025-09-30 16:52:03'),(30,1,8,'Arequipe (Tarro 500g)','Arequipe casero cremoso y dulce, perfecto para postres y acompañamientos',8,100,9000.00,'2026-04-10','2025-07-01','Propio',1,'2025-09-30 16:52:03');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_actualizar_fecha_producto 
    BEFORE UPDATE ON Productos
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `ID_PROVEEDOR` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_PROV` varchar(100) NOT NULL,
  `TELEFONO_PROV` varchar(20) DEFAULT NULL,
  `ACTIVO_PROV` tinyint(1) DEFAULT 1,
  `EMAIL_PROV` varchar(100) DEFAULT NULL,
  `DIRECCION_PROV` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID_PROVEEDOR`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'Proveedor C','555-9876',1,'c@proveedor.com','Calle 789'),(2,'Dulce Granero','3007654321',1,'pedidos@dulcegranero.com',NULL),(3,'El Horno Mágico S.A.S.','3009876543',1,'contacto@hornoimagico.com',NULL),(4,'Masa Maestra Distribuciones','3005555555',1,'info@masamaestra.com',NULL),(5,'Insumos Panaderos del Sol','3002222222',1,'ventas@insumossol.com',NULL),(6,'La Esencia del Pan','3003333333',1,'pedidos@esenciapan.com',NULL),(7,'Proveedora Integral del Panadero','3004444444',1,'contacto@provintegral.com',NULL),(8,'Alimentos para Hornear Cía. Ltda.','3006666666',1,'ventas@alimentoshornear.com',NULL);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recetas_productos`
--

DROP TABLE IF EXISTS `recetas_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recetas_productos` (
  `ID_RECETA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCTO` int(11) NOT NULL,
  `ID_INGREDIENTE` int(11) NOT NULL,
  `CANTIDAD_REQUERIDA` decimal(10,3) NOT NULL,
  `UNIDAD_MEDIDA` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_RECETA`),
  UNIQUE KEY `UK_PRODUCTO_INGREDIENTE` (`ID_PRODUCTO`,`ID_INGREDIENTE`),
  KEY `FK_RECETA_INGREDIENTE` (`ID_INGREDIENTE`),
  CONSTRAINT `FK_RECETA_INGREDIENTE` FOREIGN KEY (`ID_INGREDIENTE`) REFERENCES `ingredientes` (`ID_INGREDIENTE`) ON UPDATE CASCADE,
  CONSTRAINT `FK_RECETA_PRODUCTO` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `productos` (`ID_PRODUCTO`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recetas_productos`
--

LOCK TABLES `recetas_productos` WRITE;
/*!40000 ALTER TABLE `recetas_productos` DISABLE KEYS */;
INSERT INTO `recetas_productos` VALUES (12,5,1,1.500,'kg'),(13,5,11,0.050,'g'),(19,12,1,0.150,'kg'),(20,12,4,0.100,'kg'),(21,12,3,0.150,'kg'),(22,12,35,1.000,'unidad'),(23,12,37,0.250,'kg'),(24,7,1,0.200,'kg'),(25,7,4,0.125,'kg'),(26,7,3,0.200,'kg'),(27,7,35,1.000,'unidad'),(28,7,6,0.100,'kg'),(32,1,13,1.500,'LITROS');
/*!40000 ALTER TABLE `recetas_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'proyectopanaderia'
--
/*!50003 DROP FUNCTION IF EXISTS `HashPassword` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `HashPassword`(password VARCHAR(255)) RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
    READS SQL DATA
    DETERMINISTIC
BEGIN
    RETURN SHA2(password, 256);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-02  3:42:59
