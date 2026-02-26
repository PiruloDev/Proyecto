  -- phpMyAdmin SQL Dump
  -- version 5.2.1
  -- https://www.phpmyadmin.net/
  --
  -- Servidor: 127.0.0.1
  -- Tiempo de generación: 05-12-2025 a las 18:19:06
  -- Versión del servidor: 10.4.32-MariaDB
  -- Versión de PHP: 8.2.12

  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  START TRANSACTION;
  SET time_zone = "+00:00";


  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
  /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
  /*!40101 SET NAMES utf8mb4 */;

  --
  -- Base de datos: `proyectopanaderia`
  --
  CREATE DATABASE IF NOT EXISTS `proyectopanaderia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
  USE `proyectopanaderia`;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `administradores`
  --

  CREATE TABLE `administradores` (
    `ID_ADMIN` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `NOMBRE_ADMIN` varchar(100) NOT NULL,
    `TELEFONO_ADMIN` varchar(20) DEFAULT NULL,
    `EMAIL_ADMIN` varchar(100) DEFAULT NULL,
    `CONTRASENA_ADMIN` varchar(255) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `administradores`
  --

  INSERT INTO `administradores` (`ID_ADMIN`, `NOMBRE_ADMIN`, `TELEFONO_ADMIN`, `EMAIL_ADMIN`, `CONTRASENA_ADMIN`) VALUES
  (1, 'Admin Principal', '3005550101', 'admin@panaderia.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9'),
  (2, 'Admin Secundario', '3005550102', 'admin2@panaderia.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9'),
  (3, 'fgag', '1234323433', 'admin@gmail.com', '1234');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `cache`
  --

  CREATE TABLE `cache` (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `cache_locks`
  --

  CREATE TABLE `cache_locks` (
    `key` varchar(255) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `expiration` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `categoria_ingredientes`
  --

  CREATE TABLE `categoria_ingredientes` (
    `ID_CATEGORIA` int(11) NOT NULL,
    `NOMBRE_CATEGORIA_INGREDIENTE` varchar(100) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `categoria_ingredientes`
  --

  INSERT INTO `categoria_ingredientes` (`ID_CATEGORIA`, `NOMBRE_CATEGORIA_INGREDIENTE`) VALUES
  (1, 'Harinas y Cereales'),
  (2, 'Lacteos y Derivados'),
  (3, 'Endulzantes y Azucares'),
  (4, 'Grasas y Aceites'),
  (5, 'Esencias y Saborizantes'),
  (6, 'Frutas y Verduras'),
  (7, 'Frutos Secos'),
  (8, 'Levaduras'),
  (9, 'Huevos'),
  (10, 'Chocolate y Cacao'),
  (11, 'Espesantes y Gelificantes'),
  (12, 'Colorantes Alimentarios'),
  (13, 'Sal'),
  (14, 'Aditivos y Mejoradores'),
  (15, 'Semillas'),
  (16, 'Coberturas y Rellenos');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `categoria_productos`
  --

  CREATE TABLE `categoria_productos` (
    `ID_CATEGORIA_PRODUCTO` int(11) NOT NULL,
    `NOMBRE_CATEGORIAPRODUCTO` varchar(100) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `categoria_productos`
  --

  INSERT INTO `categoria_productos` (`ID_CATEGORIA_PRODUCTO`, `NOMBRE_CATEGORIAPRODUCTO`) VALUES
  (1, 'Tortas Tres Leches'),
  (2, 'Tortas Milyway'),
  (3, 'Tortas por Encargo'),
  (4, 'Pan Grande'),
  (5, 'Pan Pequeno'),
  (6, 'Postres'),
  (7, 'Galletas'),
  (8, 'Tamales'),
  (9, 'Yogures'),
  (10, 'Pasteles Pollo');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `clientes`
  --

  CREATE TABLE `clientes` (
    `ID_CLIENTE` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `NOMBRE_CLI` varchar(100) NOT NULL,
    `TELEFONO_CLI` varchar(20) DEFAULT NULL,
    `ACTIVO_CLI` tinyint(1) DEFAULT 1,
    `EMAIL_CLI` varchar(100) DEFAULT NULL,
    `CONTRASENA_CLI` varchar(255) DEFAULT NULL,
    `FECHA_ULTIMA_MODIFICACION` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `clientes`
  --

  INSERT INTO `clientes` (`ID_CLIENTE`, `NOMBRE_CLI`, `TELEFONO_CLI`, `ACTIVO_CLI`, `EMAIL_CLI`, `CONTRASENA_CLI`, `FECHA_ULTIMA_MODIFICACION`) VALUES
  (1, 'Ana Perez', '3101234567', 1, 'ana.p@mail.com', '09a31a7001e261ab1e056182a71d3cf57f582ca9a29cff5eb83be0f0549730a9', '2025-11-11 05:18:25'),
  (2, 'Luis Gomez', '3209876543', 1, 'luis.g@mail.com', '8d0cb54601bfecce6840eff8c2b0a4fb2d5e52cfc7754997d2902eb2306fc275', '2025-11-11 05:18:25'),
  (3, 'Maria Rodriguez', '3001122334', 1, 'maria.r@mail.com', 'd6a2339d155e81f11349280374b228b27273e8f7725a1d2f0feae84c95caa2f9', '2025-11-11 05:18:25'),
  (4, 'Damian Cliente', '3001234567', 1, 'damian@cliente.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2025-11-11 05:18:25'),
  (5, 'dfasdf', '1234565466', 1, 'dfa@gmail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '2025-11-25 19:35:10'),
  (6, 'ads', '123456789', 1, 'algo@gmail.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2025-11-26 15:28:08'),
  (7, 'wcsf', '1223223333', 1, 'adsf@gmail.com', '2f63f1edd8b2c3926f52154eb4672e43a0563f0fcc36c98166f829f1c77bac6e', '2025-12-04 05:53:04');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `detalle_pedidos`
  --

  CREATE TABLE `detalle_pedidos` (
    `ID_DETALLE` int(11) NOT NULL,
    `ID_PEDIDO` int(11) NOT NULL,
    `ID_PRODUCTO` int(11) NOT NULL,
    `CANTIDAD_PRODUCTO` int(11) DEFAULT NULL,
    `PRECIO_UNITARIO` decimal(10,2) DEFAULT NULL,
    `SUBTOTAL` decimal(10,2) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `detalle_pedidos`
  --

  INSERT INTO `detalle_pedidos` (`ID_DETALLE`, `ID_PEDIDO`, `ID_PRODUCTO`, `CANTIDAD_PRODUCTO`, `PRECIO_UNITARIO`, `SUBTOTAL`) VALUES
  (1, 1, 14, 3, 3.00, 34.00);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `detalle_pedidos_proveedores`
  --

  CREATE TABLE `detalle_pedidos_proveedores` (
    `ID_DETALLE_PROV` int(11) NOT NULL,
    `ID_PEDIDO_PROV` int(11) NOT NULL,
    `ID_INGREDIENTE` int(11) NOT NULL,
    `CANTIDAD_ORDENADA` decimal(10,4) NOT NULL,
    `PRECIO_COMPRA` decimal(10,2) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `detalle_pedidos_proveedores`
  --

  INSERT INTO `detalle_pedidos_proveedores` (`ID_DETALLE_PROV`, `ID_PEDIDO_PROV`, `ID_INGREDIENTE`, `CANTIDAD_ORDENADA`, `PRECIO_COMPRA`) VALUES
  (1, 1, 1, 50.0000, 40.00),
  (2, 1, 15, 5.0000, 5.00),
  (3, 2, 2, 100.0000, 2.50),
  (4, 2, 5, 30.0000, 0.50),
  (5, 3, 4, 10.0000, 15.00),
  (8, 16, 11, 500.0000, 1.50),
  (9, 16, 12, 250.0000, 2.25),
  (10, 17, 11, 500.0000, 1.50),
  (11, 17, 12, 250.0000, 2.25),
  (12, 18, 11, 500.0000, 1.50),
  (13, 18, 12, 250.0000, 2.25);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `empleados`
  --

  CREATE TABLE `empleados` (
    `ID_EMPLEADO` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `NOMBRE_EMPLEADO` varchar(100) NOT NULL,
    `EMAIL_EMPLEADO` varchar(100) DEFAULT NULL,
    `ACTIVO_EMPLEADO` tinyint(1) DEFAULT 1,
    `CONTRASENA_EMPLEADO` varchar(255) DEFAULT NULL,
    `FECHA_REGISTRO` timestamp NOT NULL DEFAULT current_timestamp(),
    `FECHA_ULTIMA_MODIFICACION` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `empleados`
  --

  INSERT INTO `empleados` (`ID_EMPLEADO`, `NOMBRE_EMPLEADO`, `EMAIL_EMPLEADO`, `ACTIVO_EMPLEADO`, `CONTRASENA_EMPLEADO`, `FECHA_REGISTRO`, `FECHA_ULTIMA_MODIFICACION`) VALUES
  (1, 'Andres Alkaeda', 'andres@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (2, 'Damian Avila', 'damian@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (3, 'Brayan Jimenez', 'brayan@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (4, 'Ana Goyeneche', 'ana@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (5, 'Sharyt Zamora', 'sharyt@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (6, 'Carlos Mendoza', 'carlos@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (7, 'Sofia Rodriguez', 'sofia@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (8, 'Miguel Torres', 'miguel@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (9, 'Valentina Castro', 'valentina@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (10, 'Diego Herrera', 'diego@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (11, 'Camila Vargas', 'camila@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (12, 'Alejandro Morales', 'alejandro@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (13, 'Isabella Gutierrez', 'isabella@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (14, 'Sebastian Ramirez', 'sebastian@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25'),
  (15, 'Natalia Delgado', 'natalia@panaderia.com', 1, 'ccc13e8ab0819e3ab61719de4071ecae6c1d3cd35dc48b91cad3481f20922f9f', '2025-11-11 05:18:25', '2025-11-11 05:18:25');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `estado_pedidos`
  --

  CREATE TABLE `estado_pedidos` (
    `ID_ESTADO_PEDIDO` int(11) NOT NULL,
    `NOMBRE_ESTADO` varchar(50) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `estado_pedidos`
  --

  INSERT INTO `estado_pedidos` (`ID_ESTADO_PEDIDO`, `NOMBRE_ESTADO`) VALUES
  (1, 'Recibido'),
  (2, 'Aceptado'),
  (3, 'Cancelado'),
  (4, 'En preparación'),
  (5, 'Enviado'),
  (6, 'Entregado');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `failed_jobs`
  --

  CREATE TABLE `failed_jobs` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `uuid` varchar(255) NOT NULL,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `ingredientes`
  --

  CREATE TABLE `ingredientes` (
    `ID_INGREDIENTE` int(11) NOT NULL,
    `ID_PROVEEDOR` int(11) DEFAULT NULL,
    `ID_CATEGORIA` int(11) DEFAULT NULL,
    `ID_UNIDAD_MEDIDA` int(11) DEFAULT NULL,
    `NOMBRE_INGREDIENTE` varchar(100) NOT NULL,
    `CANTIDAD_INGREDIENTE` decimal(10,4) DEFAULT NULL,
    `FECHA_VENCIMIENTO` date DEFAULT NULL,
    `REFERENCIA_INGREDIENTE` varchar(100) DEFAULT NULL,
    `FECHA_ENTREGA_INGREDIENTE` date DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;  

  --
  -- Volcado de datos para la tabla `ingredientes`
  --

  INSERT INTO `ingredientes` (`ID_INGREDIENTE`, `ID_PROVEEDOR`, `ID_CATEGORIA`, `ID_UNIDAD_MEDIDA`, `NOMBRE_INGREDIENTE`, `CANTIDAD_INGREDIENTE`, `FECHA_VENCIMIENTO`, `REFERENCIA_INGREDIENTE`, `FECHA_ENTREGA_INGREDIENTE`) VALUES
  (1, 2, 1, 1, 'Harina de Trigo', 13479.4648, '2025-12-20', 'HAR-TRG-05', '2025-07-01'),
  (2, 1, 2, 3, 'Leche Entera', 8668.0000, '2025-08-01', 'LECH-ENT-1L', '2025-07-01'),
  (3, 3, 3, 1, 'Azucar Blanca', 24600.0000, '2026-01-30', 'AZUC-BLN-KG', '2025-07-01'),
  (4, 4, 4, 1, 'Mantequilla sin Sal', 25775.0000, '2025-09-15', 'MANT-SS-KG', '2025-07-01'),
  (5, 5, 9, 5, 'Huevos Grandes', 316.0000, '2025-07-25', 'HUEV-GR-DZ', '2025-07-01'),
  (6, 6, 10, 1, 'Chocolate Semi-Amargo (Gotas)', 15.0000, '2026-03-10', 'CHOC-SM-KG', '2025-07-01'),
  (7, 7, 8, 2, 'Levadura Fresca', 5.0000, '2025-07-10', 'LEV-FRES-GR', '2025-07-01'),
  (8, 1, 3, 1, 'Azucar Moreno', 20680.0000, '2026-02-20', 'AZUC-MRN-KG', '2025-07-01'),
  (10, 3, 4, 3, 'Aceite Vegetal', 352.0000, '2026-05-01', 'ACET-VEG-LT', '2025-07-01'),
  (11, 4, 10, 1, 'Cacao en Polvo', 93.0000, '2026-04-15', 'CACAO-POL-KG', '2025-07-01'),
  (12, 5, 6, 1, 'Manzanas Verdes (Kg)', 10.0000, '2025-07-12', 'MANZ-VRD-KG', '2025-07-01'),
  (13, 6, 7, 1, 'Nueces Picadas', 17615.0000, '2025-10-01', 'NUEZ-PIC-KG', '2025-07-01'),
  (14, 7, 5, 4, 'Esencia de Vainilla', 106.0000, '2027-01-01', 'ESEN-VN-LT', '2025-07-01'),
  (15, 8, 13, 1, 'Sal Fina', 113.0000, '2028-01-01', 'SAL-FIN-KG', '2025-07-01'),
  (20, 5, 16, 1, 'Dulce de Leche', 10.0000, '2025-11-15', 'DDL-KG', '2025-07-01'),
  (21, 1, 1, 1, 'Avena en Hojuelas', 30917.0000, '2026-10-01', 'ADS-LT', NULL);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `jobs`
  --

  CREATE TABLE `jobs` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `attempts` tinyint(3) UNSIGNED NOT NULL,
    `reserved_at` int(10) UNSIGNED DEFAULT NULL,
    `available_at` int(10) UNSIGNED NOT NULL,
    `created_at` int(10) UNSIGNED NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `job_batches`
  --

  CREATE TABLE `job_batches` (
    `id` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `total_jobs` int(11) NOT NULL,
    `pending_jobs` int(11) NOT NULL,
    `failed_jobs` int(11) NOT NULL,
    `failed_job_ids` longtext NOT NULL,
    `options` mediumtext DEFAULT NULL,
    `cancelled_at` int(11) DEFAULT NULL,
    `created_at` int(11) NOT NULL,
    `finished_at` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `migrations`
  --

  CREATE TABLE `migrations` (
    `id` int(10) UNSIGNED NOT NULL,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  --
  -- Volcado de datos para la tabla `migrations`
  --

  INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
  (1, '0001_01_01_000000_create_users_table', 1),
  (2, '0001_01_01_000001_create_cache_table', 1),
  (3, '0001_01_01_000002_create_jobs_table', 1),
  (4, '2025_08_26_100418_add_two_factor_columns_to_users_table', 1);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `ordenes_salida`
  --

  CREATE TABLE `ordenes_salida` (
    `ID_FACTURA` int(11) NOT NULL,
    `ID_CLIENTE` int(11) DEFAULT NULL,
    `ID_PEDIDO` int(11) DEFAULT NULL,
    `FECHA_FACTURACION` datetime DEFAULT NULL,
    `TOTAL_FACTURA` decimal(10,2) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `password_reset_tokens`
  --

  CREATE TABLE `password_reset_tokens` (
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `pedidos`
  --

  CREATE TABLE `pedidos` (
    `ID_PEDIDO` int(11) NOT NULL,
    `ID_CLIENTE` int(11) DEFAULT NULL,
    `ID_EMPLEADO` int(11) DEFAULT NULL,
    `ID_ESTADO_PEDIDO` int(11) DEFAULT NULL,
    `FECHA_INGRESO` datetime DEFAULT NULL,
    `FECHA_ENTREGA` datetime DEFAULT NULL,
    `TOTAL_PRODUCTO` decimal(10,2) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `pedidos`
  --

  INSERT INTO `pedidos` (`ID_PEDIDO`, `ID_CLIENTE`, `ID_EMPLEADO`, `ID_ESTADO_PEDIDO`, `FECHA_INGRESO`, `FECHA_ENTREGA`, `TOTAL_PRODUCTO`) VALUES
  (1, 1, 1, 4, '2025-11-12 17:36:31', '2025-11-18 17:36:31', 12222.00),
  (2, 1, 1, 3, '2025-11-11 17:34:36', '2025-11-11 00:00:00', 2.00);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `pedidos_proveedores`
  --

  CREATE TABLE `pedidos_proveedores` (
    `ID_PEDIDO_PROV` int(11) NOT NULL,
    `ID_PROVEEDOR` int(11) NOT NULL,
    `NUMERO_PEDIDO` int(11) NOT NULL,
    `FECHA_PEDIDO` date NOT NULL,
    `ESTADO_PEDIDO` varchar(50) DEFAULT 'Pendiente'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `pedidos_proveedores`
  --

  INSERT INTO `pedidos_proveedores` (`ID_PEDIDO_PROV`, `ID_PROVEEDOR`, `NUMERO_PEDIDO`, `FECHA_PEDIDO`, `ESTADO_PEDIDO`) VALUES
  (1, 1, 1001, '2025-10-01', 'Recibido'),
  (2, 2, 1002, '2025-11-05', 'Pendiente'),
  (3, 4, 1003, '2025-11-09', 'Cancelado'),
  (16, 1, 20016, '2025-01-22', 'Completado'),
  (17, 1, 20016, '2025-01-22', 'aaaa'),
  (18, 1, 20216, '2025-01-22', 'aaaa');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `produccion`
  --

  CREATE TABLE `produccion` (
    `ID_PRODUCCION` int(11) NOT NULL,
    `ID_PRODUCTO` int(11) NOT NULL,
    `CANTIDAD_PRODUCIDA` decimal(10,4) NOT NULL,
    `FECHA_PRODUCCION` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `produccion`
  --

  INSERT INTO `produccion` (`ID_PRODUCCION`, `ID_PRODUCTO`, `CANTIDAD_PRODUCIDA`, `FECHA_PRODUCCION`) VALUES
  (1, 24, 100.0000, '2025-11-11 12:22:37'),
  (13, 24, 2.0000, '2025-12-05 11:48:08');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `productos`
  --

  CREATE TABLE `productos` (
    `ID_PRODUCTO` int(11) NOT NULL,
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
    `FECHA_ULTIMA_MODIFICACION` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `productos`
  --

  INSERT INTO `productos` (`ID_PRODUCTO`, `ID_ADMIN`, `ID_CATEGORIA_PRODUCTO`, `NOMBRE_PRODUCTO`, `DESCRIPCION_PRODUCTO`, `PRODUCTO_STOCK_MIN`, `STOCK_ACTUAL`, `PRECIO_PRODUCTO`, `FECHA_VENCIMIENTO_PRODUCTO`, `FECHA_INGRESO_PRODUCTO`, `TIPO_PRODUCTO_MARCA`, `ACTIVO`, `FECHA_ULTIMA_MODIFICACION`) VALUES
  (1, 1, 1, 'Postre de Tres Leches', 'Clasico postre colombiano', 7, 10, 7500.00, '2025-07-08', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (2, 1, 2, 'Torta de Chocolate Pequena', 'Deliciosa torta de chocolate', 5, 10, 18000.00, '2025-07-07', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (3, 1, 2, 'Cheesecake de Frutos Rojos', 'Cheesecake cremoso', 6, 10, 25000.00, '2025-07-09', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (4, 1, 2, 'Milhoja de Arequipe', 'Milhoja rellena de arequipe', 12, 10, 6000.00, '2025-07-08', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (5, 1, 3, 'Brazo de Reina', 'Bizcocho enrollado con dulce de leche', 10, 10, 9500.00, '2025-07-09', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (6, 1, 3, 'Ponque de Naranja (Porcion)', 'Porcion individual con glaseado citrico', 15, 10, 3000.00, '2025-07-08', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (7, 1, 4, 'Pan Campesino Grande', 'Pan artesanal', 8, 10, 5500.00, '2025-07-08', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (8, 1, 4, 'Baguette Clasica', 'Pan frances tradicional', 25, 10, 2800.00, '2025-07-06', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (9, 1, 4, 'Pan Artesanal de Masa Madre', 'Fermentacion larga', 7, 10, 8000.00, '2025-07-07', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (10, 1, 4, 'Mogolla Chicharrona', NULL, 15, 10, 3500.00, '2025-07-06', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (11, 1, 5, 'Pan Tajado Integral', 'Pan de molde integral', 15, 10, 4200.00, '2025-07-02', '2025-07-01', 'Propio', 1, '2025-11-11 05:18:25'),
  (12, 1, 5, 'Pan Blanco de Molde', NULL, 20, 10, 3900.00, '2025-07-02', '2025-07-01', 'Propio', 1, '2025-11-11 05:18:25'),
  (13, 1, 5, 'Pan de Bono Pequeno', NULL, 30, 10, 1500.00, '2025-07-06', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (14, 1, 5, 'Croissant de Almendras', NULL, 18, 10, 3500.00, '2025-07-06', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (15, 1, 5, 'Pan Blandito', NULL, 28, 10, 2500.00, '2025-07-07', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (16, 1, 5, 'Pan de Hamburguesa', NULL, 30, 10, 4500.00, '2025-07-10', '2025-07-02', 'Propio', 1, '2025-11-11 05:18:25'),
  (17, 1, 6, 'Jugo de Naranja Natural', 'Jugo 100% natural', 10, 10, 4500.00, '2025-07-05', '2025-07-04', 'Postobon', 1, '2025-11-11 05:18:25'),
  (18, 1, 6, 'Avena La Lechera (500ml)', NULL, 18, 10, 5800.00, '2025-08-20', '2025-07-03', 'Nestle', 1, '2025-11-11 05:18:25'),
  (19, 1, 6, 'Brownie con Nuez', 'Brownie humedo y delicioso', 40, 10, 1800.00, '2025-07-08', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (20, 1, 6, 'Muffin de Arandanos', 'Muffin esponjoso', 15, 10, 3000.00, '2025-07-07', '2025-07-04', 'Propio', 1, '2025-11-11 05:18:25'),
  (21, 1, 7, 'Galleta de Tres Ojos', 'Galleta tradicional', 20, 10, 2500.00, '2025-11-01', '2025-07-01', 'Propio', 1, '2025-11-11 05:18:25'),
  (22, 1, 7, 'Bizcochos de Achira', NULL, 15, 10, 4000.00, '2025-12-01', '2025-07-01', 'Propio', 1, '2025-11-11 05:18:25'),
  (23, 1, 7, 'Galletas Surtidas de Mantequilla', 'Variedad de galletas caseras', 25, 10, 3200.00, '2025-12-30', '2025-07-01', 'Propio', 1, '2025-11-11 05:18:25'),
  (24, 1, 7, 'Galletas de Avena y Pasas', 'Con avena integral y pasas', 22, 12, 2700.00, '2026-01-01', '2025-07-01', 'Propio', 1, '2025-12-05 11:48:08'),
  (25, 1, 8, 'Tamales Tolimenses', 'Tradicionales tamales', 10, 10, 3800.00, '2025-09-15', '2025-07-01', 'Propio', 1, '2025-11-11 05:18:25'),
  (26, 1, 8, 'Empanadas de Carne (unidad)', 'Empanada frita con carne', 20, 10, 2000.00, '2025-07-06', '2025-07-05', 'Propio', 1, '2025-11-11 05:18:25'),
  (27, 1, 9, 'Yogurt Fresa Litro', 'Yogurt cremoso con trozos de fresa', 12, 10, 6000.00, '2025-07-30', '2025-07-03', 'Alpina', 1, '2025-11-11 05:18:25'),
  (28, 1, 9, 'Kumiss Natural', NULL, 10, 10, 4900.00, '2025-07-25', '2025-07-03', 'Alqueria', 1, '2025-11-11 05:18:25');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `proveedores`
  --

  CREATE TABLE `proveedores` (
    `ID_PROVEEDOR` int(11) NOT NULL,
    `NOMBRE_PROV` varchar(100) NOT NULL,
    `TELEFONO_PROV` varchar(20) DEFAULT NULL,
    `ACTIVO_PROV` tinyint(1) DEFAULT 1,
    `EMAIL_PROV` varchar(100) DEFAULT NULL,
    `DIRECCION_PROV` varchar(200) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `proveedores`
  --

  INSERT INTO `proveedores` (`ID_PROVEEDOR`, `NOMBRE_PROV`, `TELEFONO_PROV`, `ACTIVO_PROV`, `EMAIL_PROV`, `DIRECCION_PROV`) VALUES
  (1, 'Harina Dorada', '3001234567', 1, 'ventas@harinadorada.com', NULL),
  (2, 'Dulce Granero', '3007654321', 1, 'pedidos@dulcegranero.com', NULL),
  (3, 'El Horno Magico S.A.S.', '3009876543', 1, 'contacto@hornoimagico.com', NULL),
  (4, 'Masa Maestra Distribuciones', '3005555555', 1, 'info@masamaestra.com', NULL),
  (5, 'Insumos Panaderos del Sol', NULL, 1, NULL, NULL),
  (6, 'La Esencia del Pan', NULL, 1, NULL, NULL),
  (7, 'Proveedora Integral del Panadero', NULL, 1, NULL, NULL),
  (8, 'Alimentos para Hornear Cia. Ltda.', '323232', 1, 'vfa@gmail.com', 'dfadgafg'),
  (10, 'sdsdd', '22323233', 1, 'vdffga@gmail.com', 'fgfgsf');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `recetas`
  --

  CREATE TABLE `recetas` (
    `ID_RECETA` int(11) NOT NULL,
    `ID_PRODUCTO` int(11) NOT NULL,
    `NOMBRE_RECETA` varchar(255) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `recetas`
  --

  INSERT INTO `recetas` (`ID_RECETA`, `ID_PRODUCTO`, `NOMBRE_RECETA`) VALUES
  (1, 24, 'Receta Clasica Avena y Pasas'),
  (8, 2, 'Receta para Producto ID 2'),
  (9, 12, 'Receta para Producto ID 12');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `recetas_detalle`
  --

  CREATE TABLE `recetas_detalle` (
    `ID_DETALLE` int(11) NOT NULL,
    `ID_RECETA` int(11) NOT NULL,
    `ID_INGREDIENTE` int(11) NOT NULL,
    `CANTIDAD_REQUERIDA` decimal(10,4) NOT NULL,
    `ID_UNIDAD` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `recetas_detalle`
  --

  INSERT INTO `recetas_detalle` (`ID_DETALLE`, `ID_RECETA`, `ID_INGREDIENTE`, `CANTIDAD_REQUERIDA`, `ID_UNIDAD`) VALUES
  (1, 1, 3, 200.0000, 2),
  (2, 1, 8, 150.0000, 2),
  (3, 1, 4, 250.0000, 2),
  (4, 1, 15, 1.0000, 6),
  (5, 1, 14, 1.0000, 6),
  (6, 1, 5, 2.0000, 5),
  (7, 1, 21, 300.0000, 2),
  (8, 1, 1, 120.0000, 2),
  (9, 1, 13, 150.0000, 2),
  (19, 8, 2, 122.0000, 2),
  (20, 8, 5, 4333.0000, 3),
  (21, 8, 1, 200.0000, 3),
  (23, 9, 1, 2233.0000, 2),
  (24, 9, 2, 2333.0000, 3),
  (25, 9, 3, 1222.0000, 3);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `sessions`
  --

  CREATE TABLE `sessions` (
    `id` varchar(255) NOT NULL,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `payload` longtext NOT NULL,
    `last_activity` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  --
  -- Volcado de datos para la tabla `sessions`
  --

  INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
  ('LqaociqfwVLRFYVYhLQpgbkReQlCouKq1WEUc6ku', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMVRseTVwV1RBN3FrbWttdFRjblVUd2ZEVlU3ZERhWTB1M2thSW9pTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1764628755),
  ('ZkIsfSkEv0UNjBVTehYN23UeOrQ2WYO6QC4dfIwL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjhFWjMyTmtLUnpWNDNpamhBNnEzSlpTVkEwbDlXS25sMnQ1bDZOeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1764644113);

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `unidades_medida`
  --

  CREATE TABLE `unidades_medida` (
    `ID_UNIDAD` int(11) NOT NULL,
    `NOMBRE_UNIDAD` varchar(50) NOT NULL,
    `ABREVIATURA_UNIDAD` varchar(10) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Volcado de datos para la tabla `unidades_medida`
  --

  INSERT INTO `unidades_medida` (`ID_UNIDAD`, `NOMBRE_UNIDAD`, `ABREVIATURA_UNIDAD`) VALUES
  (1, 'Kilogramo', 'kg'),
  (2, 'Gramo', 'gr'),
  (3, 'Litro', 'Lts'),
  (4, 'Mililitro', 'ml'),
  (5, 'Unidad', 'un'),
  (6, 'Cucharada', 'cda');

  -- --------------------------------------------------------

  --
  -- Estructura de tabla para la tabla `users`
  --

  CREATE TABLE `users` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `two_factor_secret` text DEFAULT NULL,
    `two_factor_recovery_codes` text DEFAULT NULL,
    `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- --------------------------------------------------------

  --
  -- Estructura Stand-in para la vista `view_recetas_completa`
  -- (Véase abajo para la vista actual)
  --
  CREATE TABLE `view_recetas_completa` (
  `ID_PRODUCTO` int(11)
  ,`NOMBRE_PRODUCTO` varchar(100)
  ,`ID_RECETA` int(11)
  ,`NOMBRE_RECETA` varchar(255)
  ,`ID_INGREDIENTE` int(11)
  ,`NOMBRE_INGREDIENTE` varchar(100)
  ,`CANTIDAD_REQUERIDA` decimal(10,4)
  ,`UNIDAD_MEDIDA` varchar(10)
  ,`NOMBRE_UNIDAD_COMPLETA` varchar(50)
  );

  -- --------------------------------------------------------

  --
  -- Estructura para la vista `view_recetas_completa`
  --
  DROP TABLE IF EXISTS `view_recetas_completa`;

  CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_recetas_completa`  AS SELECT `p`.`ID_PRODUCTO` AS `ID_PRODUCTO`, `p`.`NOMBRE_PRODUCTO` AS `NOMBRE_PRODUCTO`, `r`.`ID_RECETA` AS `ID_RECETA`, `r`.`NOMBRE_RECETA` AS `NOMBRE_RECETA`, `i`.`ID_INGREDIENTE` AS `ID_INGREDIENTE`, `i`.`NOMBRE_INGREDIENTE` AS `NOMBRE_INGREDIENTE`, `rd`.`CANTIDAD_REQUERIDA` AS `CANTIDAD_REQUERIDA`, `um`.`ABREVIATURA_UNIDAD` AS `UNIDAD_MEDIDA`, `um`.`NOMBRE_UNIDAD` AS `NOMBRE_UNIDAD_COMPLETA` FROM ((((`productos` `p` join `recetas` `r` on(`p`.`ID_PRODUCTO` = `r`.`ID_PRODUCTO`)) join `recetas_detalle` `rd` on(`r`.`ID_RECETA` = `rd`.`ID_RECETA`)) join `ingredientes` `i` on(`rd`.`ID_INGREDIENTE` = `i`.`ID_INGREDIENTE`)) join `unidades_medida` `um` on(`rd`.`ID_UNIDAD` = `um`.`ID_UNIDAD`)) ORDER BY `p`.`ID_PRODUCTO` ASC, `rd`.`ID_DETALLE` ASC ;

  --
  -- Índices para tablas volcadas
  --

  --
  -- Indices de la tabla `administradores`
  -- Indices de la tabla `cache`
  --
  ALTER TABLE `cache`
    ADD PRIMARY KEY (`key`);

  --
  -- Indices de la tabla `cache_locks`
  --
  ALTER TABLE `cache_locks`
    ADD PRIMARY KEY (`key`);

  --
  -- Indices de la tabla `categoria_ingredientes`
  --
  ALTER TABLE `categoria_ingredientes`
    ADD PRIMARY KEY (`ID_CATEGORIA`);

  --
  -- Indices de la tabla `categoria_productos`
  --
  ALTER TABLE `categoria_productos`
    ADD PRIMARY KEY (`ID_CATEGORIA_PRODUCTO`);
  --
  -- Indices de la tabla `clientes`
  -- Indices de la tabla `detalle_pedidos`
  --
  ALTER TABLE `detalle_pedidos`
    ADD PRIMARY KEY (`ID_DETALLE`),
    ADD KEY `FK_DETALLE_PEDIDO` (`ID_PEDIDO`),
    ADD KEY `FK_DETALLE_PRODUCTO` (`ID_PRODUCTO`);

  --
  -- Indices de la tabla `detalle_pedidos_proveedores`
  --
  ALTER TABLE `detalle_pedidos_proveedores`
    ADD PRIMARY KEY (`ID_DETALLE_PROV`),
    ADD KEY `FK_DETALLE_PEDIDOPROV` (`ID_PEDIDO_PROV`),
    ADD KEY `FK_INGREDIENTE_ORDENADO` (`ID_INGREDIENTE`);

  --
  -- Indices de la tabla `empleados`
  -- Indices de la tabla `estado_pedidos`
  --
  ALTER TABLE `estado_pedidos`
    ADD PRIMARY KEY (`ID_ESTADO_PEDIDO`);

  --
  -- Indices de la tabla `failed_jobs`
  --
  ALTER TABLE `failed_jobs`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

  --
  -- Indices de la tabla `ingredientes`
  --
  ALTER TABLE `ingredientes`
    ADD PRIMARY KEY (`ID_INGREDIENTE`),
    ADD CONSTRAINT `FK_CATEGORIA_INGREDIENTE` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categoria_ingredientes` (`ID_CATEGORIA`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_PROVEEDOR_INGREDIENTE` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `proveedores` (`ID_PROVEEDOR`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_UNIDAD_INGREDIENTE` FOREIGN KEY (`ID_UNIDAD_MEDIDA`) REFERENCES `unidades_medida` (`ID_UNIDAD`) ON UPDATE CASCADE;

  --
  -- Indices de la tabla `jobs`
  --
  ALTER TABLE `jobs`
    ADD PRIMARY KEY (`id`),
    ADD KEY `jobs_queue_index` (`queue`);

  --
  -- Indices de la tabla `job_batches`
  --
  ALTER TABLE `job_batches`
    ADD PRIMARY KEY (`id`);

  --
  -- Indices de la tabla `migrations`
  --
  ALTER TABLE `migrations`
    ADD PRIMARY KEY (`id`);

  --
  -- Indices de la tabla `ordenes_salida`
  --
  ALTER TABLE `ordenes_salida`
    ADD PRIMARY KEY (`ID_FACTURA`),
    ADD KEY `FK_ORDENSALIDA_CLIENTE` (`ID_CLIENTE`),
    ADD KEY `FK_ORDENSALIDA_PEDIDO` (`ID_PEDIDO`);

  --
  -- Indices de la tabla `password_reset_tokens`
  --
  ALTER TABLE `password_reset_tokens`
    ADD PRIMARY KEY (`email`);

  --
  -- Indices de la tabla `pedidos`
  --
  ALTER TABLE `pedidos`
    ADD PRIMARY KEY (`ID_PEDIDO`),
    ADD KEY `FK_CLIENTE_PEDIDO` (`ID_CLIENTE`),
    ADD KEY `FK_EMPLEADO_PEDIDO` (`ID_EMPLEADO`),
    ADD KEY `FK_ESTADO_PEDIDO_PEDIDO` (`ID_ESTADO_PEDIDO`);

  --
  -- Indices de la tabla `pedidos_proveedores`
  --
  ALTER TABLE `pedidos_proveedores`
    ADD PRIMARY KEY (`ID_PEDIDO_PROV`),
    ADD KEY `FK_PEDIDO_PROVEEDOR` (`ID_PROVEEDOR`);

  --
  -- Indices de la tabla `produccion`
  --
  ALTER TABLE `produccion`
    ADD PRIMARY KEY (`ID_PRODUCCION`),
    ADD KEY `ID_PRODUCTO` (`ID_PRODUCTO`);

  --
  -- Indices de la tabla `productos`
  --
  ALTER TABLE `productos`
    ADD PRIMARY KEY (`ID_PRODUCTO`),
    ADD KEY `FK_CATEGORIA_PRODUCTO` (`ID_CATEGORIA_PRODUCTO`),
    ADD KEY `FK_ADMIN_PRODUCTO` (`ID_ADMIN`);

  --
  -- Indices de la tabla `proveedores`
  --
  ALTER TABLE `proveedores`
    ADD PRIMARY KEY (`ID_PROVEEDOR`);

  --
  -- Indices de la tabla `recetas`
  --
  ALTER TABLE `recetas`
    ADD PRIMARY KEY (`ID_RECETA`),
    ADD UNIQUE KEY `UK_PRODUCTO_RECETA` (`ID_PRODUCTO`);

  --
  -- Indices de la tabla `recetas_detalle`
  --
  ALTER TABLE `recetas_detalle`
    ADD PRIMARY KEY (`ID_DETALLE`),
    ADD UNIQUE KEY `UK_INGREDIENTE_EN_RECETA` (`ID_RECETA`,`ID_INGREDIENTE`),
    ADD KEY `FK_INGREDIENTE_DETALLE` (`ID_INGREDIENTE`),
    ADD KEY `FK_UNIDAD_DETALLE` (`ID_UNIDAD`);

  --
  -- Indices de la tabla `sessions`
  --
  ALTER TABLE `sessions`
    ADD PRIMARY KEY (`id`),
    ADD KEY `sessions_user_id_index` (`user_id`),
    ADD KEY `sessions_last_activity_index` (`last_activity`);

  --
  -- Indices de la tabla `unidades_medida`
  --
  ALTER TABLE `unidades_medida`
    ADD PRIMARY KEY (`ID_UNIDAD`);

  --
  -- Indices de la tabla `users`
  --
  ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `users_email_unique` (`email`);

  --
  -- AUTO_INCREMENT de las tablas volcadas
  --

  --
  -- AUTO_INCREMENT de la tabla `administradores`
  --
  ALTER TABLE `administradores`
    MODIFY `ID_ADMIN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

  --
  -- AUTO_INCREMENT de la tabla `categoria_ingredientes`
  --
  ALTER TABLE `categoria_ingredientes`
    MODIFY `ID_CATEGORIA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

  --
  -- AUTO_INCREMENT de la tabla `categoria_productos`
  --
  ALTER TABLE `categoria_productos`
    MODIFY `ID_CATEGORIA_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

  --
  -- AUTO_INCREMENT de la tabla `clientes`
  --
  ALTER TABLE `clientes`
    MODIFY `ID_CLIENTE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

  --
  -- AUTO_INCREMENT de la tabla `detalle_pedidos`
  --
  ALTER TABLE `detalle_pedidos`
    MODIFY `ID_DETALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

  --
  -- AUTO_INCREMENT de la tabla `detalle_pedidos_proveedores`
  --
  ALTER TABLE `detalle_pedidos_proveedores`
    MODIFY `ID_DETALLE_PROV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

  --
  -- AUTO_INCREMENT de la tabla `empleados`
  --
  ALTER TABLE `empleados`
    MODIFY `ID_EMPLEADO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

  --
  -- AUTO_INCREMENT de la tabla `estado_pedidos`
  --
  ALTER TABLE `estado_pedidos`
    MODIFY `ID_ESTADO_PEDIDO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

  --
  -- AUTO_INCREMENT de la tabla `failed_jobs`
  --
  ALTER TABLE `failed_jobs`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT de la tabla `ingredientes`
  --
  ALTER TABLE `ingredientes`
    MODIFY `ID_INGREDIENTE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

  --
  -- AUTO_INCREMENT de la tabla `jobs`
  --
  ALTER TABLE `jobs`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT de la tabla `migrations`
  --
  ALTER TABLE `migrations`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT de la tabla `ordenes_salida`
  --
  ALTER TABLE `ordenes_salida`
    MODIFY `ID_FACTURA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT de la tabla `pedidos`
  --
  ALTER TABLE `pedidos`
    MODIFY `ID_PEDIDO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

  --
  -- AUTO_INCREMENT de la tabla `pedidos_proveedores`
  --
  ALTER TABLE `pedidos_proveedores`
    MODIFY `ID_PEDIDO_PROV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

  --
  -- AUTO_INCREMENT de la tabla `produccion`
  --
  ALTER TABLE `produccion`
    MODIFY `ID_PRODUCCION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

  --
  -- AUTO_INCREMENT de la tabla `productos`
  --
  ALTER TABLE `productos`
    MODIFY `ID_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

  --
  -- AUTO_INCREMENT de la tabla `proveedores`
  --
  ALTER TABLE `proveedores`
    MODIFY `ID_PROVEEDOR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

  --
  -- AUTO_INCREMENT de la tabla `recetas`
  --
  ALTER TABLE `recetas`
    MODIFY `ID_RECETA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

  --
  -- AUTO_INCREMENT de la tabla `recetas_detalle`
  --
  ALTER TABLE `recetas_detalle`
    MODIFY `ID_DETALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

  --
  -- AUTO_INCREMENT de la tabla `unidades_medida`
  --
  ALTER TABLE `unidades_medida`
    MODIFY `ID_UNIDAD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

  --
  -- AUTO_INCREMENT de la tabla `users`
  --
  ALTER TABLE `users`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- Restricciones para tablas volcadas
  --

  --
  -- Filtros para la tabla `detalle_pedidos`
  --
  ALTER TABLE `detalle_pedidos`
    ADD CONSTRAINT `FK_DETALLE_PEDIDO` FOREIGN KEY (`ID_PEDIDO`) REFERENCES `pedidos` (`ID_PEDIDO`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_DETALLE_PRODUCTO` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `productos` (`ID_PRODUCTO`) ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `detalle_pedidos_proveedores`
  --
  ALTER TABLE `detalle_pedidos_proveedores`
    ADD CONSTRAINT `FK_DETALLE_PEDIDOPROV` FOREIGN KEY (`ID_PEDIDO_PROV`) REFERENCES `pedidos_proveedores` (`ID_PEDIDO_PROV`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_INGREDIENTE_ORDENADO` FOREIGN KEY (`ID_INGREDIENTE`) REFERENCES `ingredientes` (`ID_INGREDIENTE`) ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `ingredientes`
  --
  ALTER TABLE `ingredientes`
    ADD CONSTRAINT `FK_CATEGORIA_INGREDIENTE` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `categoria_ingredientes` (`ID_CATEGORIA`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_PROVEEDOR_INGREDIENTE` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `proveedores` (`ID_PROVEEDOR`) ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `ordenes_salida`
  --
  ALTER TABLE `ordenes_salida`
    ADD CONSTRAINT `FK_ORDENSALIDA_CLIENTE` FOREIGN KEY (`ID_CLIENTE`) REFERENCES `clientes` (`ID_CLIENTE`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_ORDENSALIDA_PEDIDO` FOREIGN KEY (`ID_PEDIDO`) REFERENCES `pedidos` (`ID_PEDIDO`) ON DELETE CASCADE ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `pedidos`
  --
  ALTER TABLE `pedidos`
    ADD CONSTRAINT `FK_CLIENTE_PEDIDO` FOREIGN KEY (`ID_CLIENTE`) REFERENCES `clientes` (`ID_CLIENTE`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_EMPLEADO_PEDIDO` FOREIGN KEY (`ID_EMPLEADO`) REFERENCES `empleados` (`ID_EMPLEADO`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_ESTADO_PEDIDO_PEDIDO` FOREIGN KEY (`ID_ESTADO_PEDIDO`) REFERENCES `estado_pedidos` (`ID_ESTADO_PEDIDO`) ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `pedidos_proveedores`
  --
  ALTER TABLE `pedidos_proveedores`
    ADD CONSTRAINT `FK_PEDIDO_PROVEEDOR` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `proveedores` (`ID_PROVEEDOR`) ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `produccion`
  --
  ALTER TABLE `produccion`
    ADD CONSTRAINT `produccion_ibfk_1` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `productos` (`ID_PRODUCTO`);

  --
  -- Filtros para la tabla `productos`
  --
  ALTER TABLE `productos`
    ADD CONSTRAINT `FK_ADMIN_PRODUCTO` FOREIGN KEY (`ID_ADMIN`) REFERENCES `administradores` (`ID_ADMIN`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_CATEGORIA_PRODUCTO` FOREIGN KEY (`ID_CATEGORIA_PRODUCTO`) REFERENCES `categoria_productos` (`ID_CATEGORIA_PRODUCTO`) ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `recetas`
  --
  ALTER TABLE `recetas`
    ADD CONSTRAINT `FK_RECETA_PRODUCTO_MAESTRA` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `productos` (`ID_PRODUCTO`) ON DELETE CASCADE ON UPDATE CASCADE;

  --
  -- Filtros para la tabla `recetas_detalle`
  --
  ALTER TABLE `recetas_detalle`
    ADD CONSTRAINT `FK_INGREDIENTE_DETALLE` FOREIGN KEY (`ID_INGREDIENTE`) REFERENCES `ingredientes` (`ID_INGREDIENTE`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_RECETA_DETALLE` FOREIGN KEY (`ID_RECETA`) REFERENCES `recetas` (`ID_RECETA`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_UNIDAD_DETALLE` FOREIGN KEY (`ID_UNIDAD`) REFERENCES `unidades_medida` (`ID_UNIDAD`) ON UPDATE CASCADE;
  COMMIT;



  CREATE TRIGGER `trg_orden_salida_after_pedido`
  AFTER INSERT ON `pedidos`
  FOR EACH ROW
  BEGIN
      INSERT INTO `ordenes_salida` (
          `ID_CLIENTE`,
          `ID_PEDIDO`,
          `FECHA_FACTURACION`,
          `TOTAL_FACTURA`
      )
      VALUES (
          NEW.ID_CLIENTE,
          NEW.ID_PEDIDO,
          NOW(),
          NEW.TOTAL_PRODUCTO
      );
  END;

  --
  -- Consulta de tablas para productos mas vendidos
  --


  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
