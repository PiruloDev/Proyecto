<?php
$isAdmin = $isAdmin ?? false;

if (!$isAdmin) {
    require_once __DIR__ . '/../controllers/productoscontroller/CategoriaProductosController.php';
    $service = new CategoriaProductosService();
    $categorias = $service->listarCategorias();
}
?>
<!doctype html>
<html lang="es-CO">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>El Castillo del Pan</title>
  <link rel="icon" href="/GITHUB%20REPO/PHP%20Modulos/images/logoprincipal.jpg">

  <!-- Bootstrap + icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Tus CSS (rutas absolutas) -->
  <link rel="stylesheet" href="/GITHUB%20REPO/PHP%20Modulos/css/styleadmindst.css">
  <link rel="stylesheet" href="/GITHUB%20REPO/PHP%20Modulos/css/stylehomepage.css">
  <link rel="stylesheet" href="/GITHUB%20REPO/PHP%20Modulos/css/homepage-custom.css">
</head>
<body class="bg-blanco-cálido">

<header>
  <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm animate__animated animate__fadeInDown">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center logo" href="/GITHUB%20REPO/PHP%20Modulos/views/homepage.php">
        <img src="/GITHUB%20REPO/PHP%20Modulos/images/logoprincipal.jpg" width="50" alt="logo" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
        <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
          <?php if (!$isAdmin): ?>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/views/homepage.php">Inicio</a></li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-marron fw-semibold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                ¡Explorar!
              </a>
              <ul class="dropdown-menu bg-crema shadow rounded-3 border-0 mt-2" aria-labelledby="navbarDropdown">
                <?php if (!empty($categorias)): ?>
                  <?php foreach ($categorias as $cat):
                    $catId = $cat['ID_CATEGORIA_PRODUCTO'] ?? $cat['id'] ?? $cat['ID'] ?? null;
                    $catName = $cat['NOMBRE_CATEGORIAPRODUCTO'] ?? $cat['NOMBRE'] ?? $cat['nombre'] ?? '';
                  ?>
                    <li><a class="dropdown-item text-marron fw-semibold py-2" href="/GITHUB%20REPO/PHP%20Modulos/templates/menu.php?categoria=<?= urlencode($catId) ?>"><?= htmlspecialchars($catName) ?></a></li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li><span class="dropdown-item text-muted">No hay categorías</span></li>
                <?php endif; ?>
              </ul>
            </li>

            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/productosindex.php">Productos</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/pedidosindex.php">Pedidos</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/reportesindex.php">Reportes</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/userindex.php">Usuarios</a></li>
            <li class="nav-item ms-2"><a class="btn btn-primary btn-rounded fw-bold" href="/GITHUB%20REPO/PHP%20Modulos/login.php">Acceder</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/Productosindex.php?seccion=productos">Productos</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/Productosindex.php?seccion=categorias">Categorías</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/Pedidosindex.php">Pedidos</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/Reportesindex.php">Reportes</a></li>
            <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="/GITHUB%20REPO/PHP%20Modulos/Userindex.php">Usuarios</a></li>
            <li class="nav-item ms-2"><a class="btn btn-danger btn-rounded fw-bold" href="/GITHUB%20REPO/PHP%20Modulos/logout.php">Cerrar sesión</a></li>
          <?php endif; ?>
        </ul>
      </div> 
    </div> 
  </nav>
</header>

<main id="main-content" role="main" class="pt-4">
