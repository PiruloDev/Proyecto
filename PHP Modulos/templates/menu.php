
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
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Animate CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
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
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-marron fw-semibold" href="menu.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                ¡Explorar!
                            </a>
                            <ul class="dropdown-menu bg-crema shadow rounded-3 border-0 mt-2" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item text-marron fw-semibold py-2" href="menu.php">Ver Menú</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-marron fw-semibold" href="pedidos.php">Pedidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-marron fw-semibold" href="#">Contáctanos</a>
                        </li>
                    </ul>
                    <a href="login.php" class="btn btn-primary btn-rounded fw-bold ms-3">Acceder</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sección Hero del Menú -->
<section class="hero-section text-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-cafe-crema mb-4 fade-in">
                    ¿Estás Listo para Disfrutar de tú antojo?
                </h1>
                <img src="../images/Pan1.jpg" class="img-fluid" alt="Imagen representativa de un pan">
                <p class="lead text-gris-oscuro mb-5 fade-in">
                    Descubre nuestros deliciosos productos artesanales, elaborados con los mejores ingredientes y mucho amor
                </p>
            </div>
        </div>
    </div>
</section>
    <!-- Sección de Productos por Categorías -->
    <section class="py-5">
        <div class="container">

            
            <!-- Barra de Búsqueda -->
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

            <!-- Contenedor de Productos -->
            <div class="row" id="product-container">
                <?php if (!empty($productos_data)): ?>
                    <?php foreach ($productos_data as $producto): ?>
                        <?php
                            // Mapeo específico de imágenes basado en los productos reales de la base de datos
                            $product_images = [
                                // Tamales (Categoría 8)
                                'Tamales Tolimenses' => 'img/tamal1.jpg',
                                'Empanadas de Carne (unidad)' => 'img/empanadas.png',
                                'Arequipe (Tarro 500g)' => 'img/arequipe-alpina.png',
                                
                                // Pan Grande (Categoría 4) 
                                'Pan Tajado Integral' => 'img/pan-integral.jpg',
                                'Pan Blanco de Molde' => 'img/pan-de-molde.jpg',
                                'Pan de Hamburguesa' => 'img/panhamburguesa.jpg',
                                
                                // Yogures (Categoría 7)
                                'Yogurt Fresa Litro' => 'img/yogurt1.jpg',
                                'Postre de Tres Leches' => 'img/postreleche.jpg',
                                'Kumiss Natural' => 'img/kumiss.1.png',
                                
                                // Galletas (Categoría 5)
                                'Galleta de Tres Ojos' => 'img/galletatresojos.png',
                                'Galletas Surtidas de Mantequilla' => 'img/galletasmantequilla.jpg',
                                'Galletas de Avena y Pasas' => 'img/galletas-de-avena-con-pasas-y-nuez.jpg',
                                
                                // Tortas Tres Leches (Categoría 1)
                                'Pan Campesino Grande' => 'img/pancampesino.png',
                                'Baguette Clásica' => 'img/baguette.jpg',
                                'Pan Trenza Integral' => 'img/pantrenza.jpg',
                                'Pan Artesanal de Masa Madre' => 'img/pan-con-masa-madre-.jpg',
                                'Pan Blandito' => 'img/panblando.jpeg',
                                
                                // Tortas por Encargo (Categoría 3)
                                'Torta de Chocolate Pequeña' => 'img/tortachocolate.jpg',
                                'Muffin de Arándanos' => 'img/muffins1.jpg',
                                'Brazo de Reina' => 'img/brazoreina1.jpg',
                                'Cheesecake de Frutos Rojos' => 'img/cheesecake-frutos-rojos.jpg',
                                'Milhoja de Arequipe' => 'img/miloha_de_arequipe.jpg',

                                // Tortas Milyway (Categoría 2)
                                'Croissant de Almendras' => 'img/croissants.jpg',
                                'Pan de Bono Pequeño' => 'img/pandebono.png',
                                'Mogolla Chicharrona' => 'img/chicharrona.png',           
                                // Pasteles Pollo (Categoría 9)
                                'Ponqué de Naranja (Porción)' => 'img/naranjaponque.jpg',
                                'Brownie con Nuez' => 'img/browni-con-nueces.jpg'
                            ];
                            
                            // Obtener la imagen del producto o usar imagen por defecto
                            $image_path = $product_images[$producto['NOMBRE_PRODUCTO']] ?? 'img/pan-rtzqhi1ok4k1bxlo.jpg';
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4 product-card-item" data-nombre="<?php echo htmlspecialchars(strtolower($producto['NOMBRE_PRODUCTO'])); ?>">
                            <div class="product-card card h-100 shadow-sm border-0 card-hover">
                                <div class="card-img-container position-relative">
                                    <img src="<?php echo $image_path; ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($producto['NOMBRE_PRODUCTO']); ?>" onerror="this.onerror=null;this.src='../images/pan-rtzqhi1ok4k1bxlo.jpg';"> 
                                    <div class="price-badge">
                                        $<?php echo number_format($producto['PRECIO_PRODUCTO'], 0); ?>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-marron fw-bold mb-2">
                                        <?php echo htmlspecialchars($producto['NOMBRE_PRODUCTO']); ?>
                                    </h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php echo htmlspecialchars($producto['TIPO_PRODUCTO_MARCA']); ?>
                                    </p>
                                    <div class="product-info mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="availability-badge">
                                                <i class="fas fa-check-circle me-1"></i>Disponible
                                            </span>
                                            <small class="text-muted stock-info">Stock: <?php echo $producto['PRODUCTO_STOCK_MIN']; ?></small>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-agregar-pedido" 
                                                data-producto-id="<?php echo $producto['ID_PRODUCTO']; ?>"
                                                data-producto-nombre="<?php echo htmlspecialchars($producto['NOMBRE_PRODUCTO']); ?>"
                                                data-producto-precio="<?php echo $producto['PRECIO_PRODUCTO']; ?>">
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
                            <p>Actualmente no tenemos productos disponibles en el menú. Por favor, vuelve a intentarlo más tarde.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Sección de Llamada a la Acción -->
    <section class="py-5 bg-marron text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-4">¿Listo para ordenar?</h2>
                    <p class="lead mb-4">
                        Contacta con nosotros para realizar tu pedido personalizado o visita nuestra tienda
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="tel:+1234567890" class="btn btn-light btn-rounded fw-bold">
                            <i class="fas fa-phone me-2"></i>Llamar Ahora
                        </a>
                        <a href="#" class="btn btn-outline-light btn-rounded fw-bold">
                            <i class="fas fa-map-marker-alt me-2"></i>Visitar Tienda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 bg-gris-oscuro text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-bread-slice me-2 text-dorado"></i>
                        El Castillo del Pan
                    </h5>
                    <p class="text-light">
                        Panadería artesanal con más de 10 años de experiencia, 
                        ofreciendo productos frescos y de la más alta calidad.
                    </p>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Enlaces</h6>
                    <ul class="list-unstyled">
                        <li><a href="Homepage.php" class="text-light text-decoration-none">Inicio</a></li>
                        <li><a href="menu.php" class="text-light text-decoration-none">Menú</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Pedidos</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Productos</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Panes</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Pasteles</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Galletas</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Especiales</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <p class="text-light mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        123 Calle Principal, Ciudad
                    </p>
                    <p class="text-light mb-2">
                        <i class="fas fa-phone me-2"></i>
                        (555) 123-4567
                    </p>
                    <p class="text-light mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        info@elcastillodelpan.com
                    </p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-0">
                        &copy; 2024 El Castillo del Pan. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-light">
                        Hecho con <i class="fas fa-heart text-danger"></i> para nuestros clientes
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- JavaScript personalizado -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('productSearchInput');
            const productContainer = document.getElementById('product-container');
            const productCards = productContainer.getElementsByClassName('product-card-item');

            // Funcionalidad de búsqueda
            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase().trim();

                for (let i = 0; i < productCards.length; i++) {
                    const card = productCards[i];
                    const productName = card.dataset.nombre;

                    if (productName.includes(searchTerm)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                }
            });

            // Funcionalidad para agregar productos al carrito
            const addToCartButtons = document.querySelectorAll('.btn-agregar-pedido');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productoId;
                    const productName = this.dataset.productoNombre;
                    const productPrice = this.dataset.productoPrecio;
                    
                    // Verificar si el usuario está logueado
                    <?php if (!$user_logged_in): ?>
                        Swal.fire({
                            icon: 'warning',
                            title: 'Inicia sesión',
                            text: 'Debes iniciar sesión para agregar productos al carrito',
                            confirmButtonColor: '#bb9467',
                            confirmButtonText: 'Ir a login'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login.php';
                            }
                        });
                        return;
                    <?php endif; ?>
                    
                    // Agregar al carrito
                    fetch('procesar_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            accion: 'agregar',
                            producto_id: productId,
                            cantidad: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Agregado!',
                                text: `${productName} ha sido agregado a tu carrito`,
                                confirmButtonColor: '#bb9467',
                                showCancelButton: true,
                                confirmButtonText: 'Ver carrito',
                                cancelButtonText: 'Continuar comprando'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'pedidos.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                confirmButtonColor: '#bb9467'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al agregar el producto',
                            confirmButtonColor: '#bb9467'
                        });
                    });
                });
            });
        });
    </script>
</body>
</html>

