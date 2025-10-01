<!DOCTYPE html>
<html lang="es-CO">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>El Castillo del Pan</title>
  <link rel="icon" type="image/x-icon" href="../images/logoprincipal.jpg">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Animate CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="../css/stylehomepage.css">
  <link rel="stylesheet" href="../css/homepage-custom.css">
</head>
<body class="bg-blanco-cálido">
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm animate__animated animate__fadeInDown">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center logo" href="../views/homepage.php">
          <img src="../images/logoprincipal.jpg" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
          <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="../views/homepage.php">Inicio</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="../views/productosviews/categoriaProductosIndex.php">Categorías</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="../productosindex.php">Productos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="../pedidosindex.php">Pedidos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="../reportesindex.php">Reportes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="../userindex.php">Usuarios</a>
            </li>
          </ul>
          <a href="../login.php" class="btn btn-primary btn-rounded fw-bold ms-3">Acceder</a>
        </div>
      </div>
    </nav>
  </header>
  <main class="container py-4">
