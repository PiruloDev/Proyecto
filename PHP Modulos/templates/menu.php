<?php
require_once __DIR__ . '/../controllers/productoscontroller/ProductosControllerUsuario.php';
$controller = new ProductosControllerUsuario();
$productos = $controller->obtenerProductos();

$categoriaId = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

if ($categoriaId) {
    $productos_filtrados = array_filter($productos, function($p) use ($categoriaId) {
        return isset($p['idCategoriaProducto']) && (int)$p['idCategoriaProducto'] === $categoriaId;
    });
} else {
    $productos_filtrados = $productos;
}

$productos_data = $productos_filtrados;
?>
<!DOCTYPE html>
<html lang="es-CO">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>El Castillo del Pan - Menú</title>
  <link rel="icon" type="image/x-icon" href="../files/img/logoprincipal.jpg">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Animate CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="../css/stylehomepage.css">
  <link rel="stylesheet" href="../css/stylemenu.css">
  <link rel="stylesheet" href="../css/menu-custom.css">
</head>
<body class="bg-blanco-cálido">

<!-- Header -->
<header>
  <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm animate__animated animate__fadeInDown">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center logo" href="Homepage.php">
        <img src="../images/logoprincipal.jpg" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
        <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-marron fw-semibold" href="menu.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              ¡Explorar!
            </a>
            <ul class="dropdown-menu bg-crema shadow rounded-3 border-0 mt-2">
              <li><a class="dropdown-item text-marron fw-semibold py-2" href="menu.php">Ver Menú</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="pedidos.php">Pedidos</a></li>
          <li class="nav-item"><a class="nav-link text-marron fw-semibold" href="#">Contáctanos</a></li>
        </ul>
        <a href="login.php" class="btn btn-primary btn-rounded fw-bold ms-3">Acceder</a>
      </div>
    </div>
  </nav>
</header>

<!-- Hero -->
<section class="hero-section text-center py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h1 class="display-4 fw-bold text-cafe-crema mb-4 fade-in">
          <?php if ($categoriaId): ?>
            Productos de la categoría seleccionada
          <?php else: ?>
            ¿Estás listo para disfrutar de tu antojo?
          <?php endif; ?>
        </h1>
        <img src="../images/Pan1.jpg" class="img-fluid" alt="Imagen representativa de pan">
        <p class="lead text-gris-oscuro mb-5 fade-in">
          Descubre nuestros deliciosos productos artesanales, elaborados con los mejores ingredientes y mucho amor.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Sección de Productos -->
<section class="py-5">
  <div class="container">

    <!-- Barra de búsqueda -->
    <div class="row mb-5 justify-content-center">
      <div class="col-lg-8">
        <div class="search-container text-center">
          <h3 class="text-marron fw-bold mb-4">
            <i class="fas fa-search me-2"></i>Busca tu Producto Favorito
          </h3>
          <div class="input-group input-group-lg shadow-sm">
            <input type="text" id="productSearchInput" class="form-control" placeholder="Ej: Torta de chocolate, Pan francés, Brownie...">
            <span class="input-group-text bg-marron text-white"><i class="fas fa-search"></i></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Productos -->
    <div class="row" id="product-container">
      <?php if (!empty($productos_data)): ?>
        <?php foreach ($productos_data as $producto): ?>
          <?php
            $nombre = $producto['nombreProducto'] ?? 'Producto sin nombre';
            $precio = $producto['precio'] ?? '0';
            $marca  = $producto['marcaProducto'] ?? 'Genérico';
            $stock  = $producto['stockMinimo'] ?? '0';
            $id     = $producto['idProducto'] ?? 0;

            // Nombre de imagen basado en producto
            $imagen_nombre = strtolower(str_replace(" ", "_", $nombre)) . ".jpeg";
            $ruta_imagen = "../images/categoria/" . $imagen_nombre;
          ?>
          <div class="col-lg-4 col-md-6 mb-4 product-card-item" data-nombre="<?php echo strtolower($nombre); ?>">
            <div class="product-card card h-100 shadow-sm border-0 card-hover">
              <div class="card-img-container position-relative">
                <img src="<?php echo $ruta_imagen; ?>" 
                     class="card-img-top product-image" 
                     alt="<?php echo $nombre; ?>" 
                     onerror="this.onerror=null;this.src='../images/categoria/default.jpg';"> 
                <div class="price-badge">$<?php echo number_format((float)$precio, 0); ?></div>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-marron fw-bold mb-2"><?php echo $nombre; ?></h5>
                <p class="card-text text-muted flex-grow-1"><?php echo $marca; ?></p>
                <div class="product-info mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="availability-badge"><i class="fas fa-check-circle me-1"></i>Disponible</span>
                    <small class="text-muted stock-info">Stock: <?php echo $stock; ?></small>
                  </div>
                </div>
                <div class="d-grid">
                  <button class="btn btn-agregar-pedido" 
                          data-producto-id="<?php echo $id; ?>"
                          data-producto-nombre="<?php echo $nombre; ?>"
                          data-producto-precio="<?php echo $precio; ?>">
                    <i class="fas fa-plus-circle me-2"></i>Agregar pedido
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <div class="alert alert-info">
            <h4 class="alert-heading">¡No hay productos!</h4>
            <p>No hay productos disponibles para esta categoría en este momento.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5 bg-marron text-white text-center">
  <div class="container">
    <h2 class="display-5 fw-bold mb-4">¿Listo para ordenar?</h2>
    <p class="lead mb-4">Contacta con nosotros para realizar tu pedido personalizado o visita nuestra tienda.</p>
    <a href="tel:+1234567890" class="btn btn-light btn-rounded fw-bold me-3">
      <i class="fas fa-phone me-2"></i>Llamar Ahora
    </a>
    <a href="#" class="btn btn-outline-light btn-rounded fw-bold">
      <i class="fas fa-map-marker-alt me-2"></i>Visitar Tienda
    </a>
  </div>
</section>

<!-- Footer -->
<footer class="py-5 bg-gris-oscuro text-white">
  <div class="container text-center">
    <p>&copy; 2024 El Castillo del Pan. Todos los derechos reservados.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
