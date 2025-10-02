<?php
// Iniciar sesión
if(!isset($_SESSION)) session_start();

// Importar controlador de categorías para menú dinámico
require_once __DIR__ . '/../services/productosservices/CategoriaProductosService.php';
$service = new CategoriaProductosService();
$categorias = $service->listarCategorias();
?>
<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - El Castillo del Pan</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <!-- Iconos -->
    <link rel="stylesheet" href="../../css/bootstrap-icons.css">
    <!-- CSS Admin -->
    <link rel="stylesheet" href="../../css/styleadmindst.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="../../images/logoprincipal.jpg" class="logo-img" alt="Logo">
                <h4 class="logo-text">Admin Panadería</h4>
            </div>
        </div>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item"><a href="index_admin.php?seccion=productos" class="nav-link">Productos</a></li>
                <li class="nav-item"><a href="index_admin.php?seccion=categorias" class="nav-link">Categorías</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link logout-btn">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
    <!-- Contenido principal -->
    <div class="main-content">
        <div class="top-header">
            <h2 class="page-title">Panel de Administración</h2>
            <span class="bienvenida-admin">Bienvenido, <?php echo $_SESSION['usuario_nombre'] ?? 'Admin'; ?></span>
        </div>
        <div class="dashboard-content">
