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
  <?php
  // Verificar si hay una sesión de cliente activa
  session_start();
  $clienteLogueado = false;
  $nombreCliente = '';
  
  if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] && 
      isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'CLIENTE') {
      $clienteLogueado = true;
      $nombreCliente = $_SESSION['usuario_nombre'] ?? 'Cliente';
  }
  ?>
  
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
          <?php if ($clienteLogueado): ?>
            <div class="dropdown">
              <button class="btn btn-primary btn-rounded fw-bold ms-3 dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($nombreCliente); ?>
              </button>
              <ul class="dropdown-menu" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="../logout.php">Cerrar Sesión</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="../loginusers.php" class="btn btn-primary btn-rounded fw-bold ms-3">Acceder</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main>
    <!-- Carousel Section -->
    <section class="carousel-fullwidth mt-4">
      <div id="smallCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#smallCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#smallCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#smallCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <a href="#" class="carousel-link">
              <img src="../images/pangrande1.jpg" class="d-block w-100 carousel-fixed-img" alt="Panes grandes">
              <div class="carousel-caption d-none d-md-block">
                <h5 class="fw-bold">Panes Artesanales</h5>
                <p>Elaborados con masa madre tradicional</p>
              </div>
            </a>
          </div>
          <div class="carousel-item">
            <a href="#" class="carousel-link">
              <img src="../images/torta1.jpg" class="d-block w-100 carousel-fixed-img" alt="Postres">
              <div class="carousel-caption d-none d-md-block">
                <h5 class="fw-bold">Dulces Tentaciones</h5>
                <p>Postres únicos para cada ocasión</p>
              </div>
            </a>
          </div>
          <div class="carousel-item">
            <a href="#" class="carousel-link">
              <img src="../images/torta2.jpg" class="d-block w-100 carousel-fixed-img" alt="Torta">
              <div class="carousel-caption d-none d-md-block">
                <h5 class="fw-bold">Tortas a tu Gusto</h5>
                <p>Personalizadas para tus momentos especiales</p>
              </div>
            </a>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#smallCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#smallCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>
    </section>

    <!-- Products Section -->
    <section class="container my-5">
      <h2 class="text-center text-gris-oscuro mb-4 animate__animated animate__fadeInUp">Nuestros Productos Destacados</h2>
      <p class="text-center text-cafe-oscuro mb-5 animate__animated animate__fadeInUp animate__delay-1s">Una selección de nuestras delicias más populares.</p>
      
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <!-- Card 1 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-1s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../images/brazoreina1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Brazo de Reina">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Brazo de Reina</h5>
              <p class="card-text text-gris-oscuro">Bizcochuelo suave relleno de arequipe artesanal, un clásico irresistible.</p>
            </div>
            <div class="card-overlay">
              <h5>Brazo de Reina</h5>
              <p>¡Especialidad de la casa! Preparado con ingredientes premium y amor artesanal.</p>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-2s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../images/hojaldre1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Hojaldres">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Hojaldres</h5>
              <p class="card-text text-gris-oscuro">Capas crujientes de masa fina con rellenos selectos y dorados perfectos.</p>
            </div>
            <div class="card-overlay">
              <h5>Hojaldres</h5>
              <p>Masa hojaldrada artesanal con rellenos que cambian según la temporada.</p>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-3s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../images/pangrande1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Panes grandes">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Panes Grandes</h5>
              <p class="card-text text-gris-oscuro">Pan artesanal con corteza dorada y miga suave, ideal para compartir.</p>
            </div>
            <div class="card-overlay">
              <h5>Panes Grandes</h5>
              <p>Horneados diariamente con masa madre tradicional e ingredientes naturales.</p>
            </div>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-4s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../images/brownie1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Brownies">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Brownies</h5>
              <p class="card-text text-gris-oscuro">Esponjosos con rellenos de Chocolate.</p>
            </div>
            <div class="card-overlay">
              <h5>Brownies</h5>
              <p>Nuestra obra magna de años de tradición en cada bocado.</p>
            </div>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-5s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../images/croissants.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Croissants">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Croissants</h5>
              <p class="card-text text-gris-oscuro">Hojaldre francés auténtico, mantequillosos y perfectamente dorados.</p>
            </div>
            <div class="card-overlay">
              <h5>Croissants</h5>
              <p>Técnica francesa tradicional con ingredientes locales de primera calidad.</p>
            </div>
          </div>
        </div>

        <!-- Card 6 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-6s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../images/muffins1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Muffins">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Muffins</h5>
              <p class="card-text text-gris-oscuro">Esponjosos y llenos de sabor, perfectos para acompañar tu café.</p>
            </div>
            <div class="card-overlay">
              <h5>Muffins</h5>
              <p>Variedad de sabores que cambian semanalmente para sorprenderte.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="bg-crema py-5 my-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 animate__animated animate__fadeInLeft">
            <h1 class="text-gris-oscuro mb-4">Un poco sobre nosotros...</h1>
            <p class="text-gris-oscuro lead">
              Somos una Panadería Artesanal con más de 10 años de experiencia, ubicada en <strong>Bogotá</strong> en el sector de Bosa el Recreo. Desde 2015, El Castillo del Pan ha sido el corazón de la panadería artesanal en nuestra comunidad.
              Nos caracterizamos por ofrecer productos frescos y de alta calidad, elaborados con recetas tradicionales.

            </p>
            <a href="#" class="btn btn-primary btn-rounded mt-3">Conoce más</a>
          </div>
          <div class="col-md-6 animate__animated animate__fadeInRight">
            <img src="../images/Local.png" class="img-fluid rounded-3 shadow" alt="Nuestra panadería">
          </div>
        </div>
      </div>
    </section>
  </main>

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
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Carousel functionality
      var carousel = new bootstrap.Carousel(document.getElementById('smallCarousel'), {
        interval: 4000,
        wrap: true,
        keyboard: true,
        pause: 'hover'
      });
      
      // Pause carousel on card hover
      const cards = document.querySelectorAll('.card');
      cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
          carousel.pause();
        });
        card.addEventListener('mouseleave', () => {
          carousel.cycle();
        });
      });
      
      // Logout confirmation
      const logoutLinks = document.querySelectorAll('a[href="logout.php"]');
      logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
            window.location.href = 'logout.php';
          }
        });
      });
      
      // Smooth scroll for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth'
            });
          }
        });
      });
    });
  </script>
</body>
</html>
