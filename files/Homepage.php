<!DOCTYPE html>
<html lang="es-CO">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>El Castillo del Pan</title>
  <link rel="icon" type="image/x-icon" href="../files/img/logoprincipal.jpg">
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Animate CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="stylehomepage.css">
</head>

<body class="bg-blanco-calido">
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm animate__animated animate__fadeInDown">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center logo" href="index1.php">
          <img src="../files/img/logoprincipal.jpg" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
          <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active text-marron fw-semibold" aria-current="page" href="modulos/modulo_categorias/modulo categoria/vista_cliente/categoria usuario.html">Menú</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-marron fw-semibold" href="#">Pedidos</a>
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
              <img src="../files/img/pangrande1.jpg" class="d-block w-100 carousel-fixed-img" alt="Panes grandes">
              <div class="carousel-caption d-none d-md-block">
                <h5 class="fw-bold">Panes Artesanales</h5>
                <p>Elaborados con masa madre tradicional</p>
              </div>
            </a>
          </div>
          <div class="carousel-item">
            <a href="#" class="carousel-link">
              <img src="../files/img/torta1.jpg" class="d-block w-100 carousel-fixed-img" alt="Postres">
              <div class="carousel-caption d-none d-md-block">
                <h5 class="fw-bold">Dulces Tentaciones</h5>
                <p>Postres únicos para cada ocasión</p>
              </div>
            </a>
          </div>
          <div class="carousel-item">
            <a href="#" class="carousel-link">
              <img src="../files/img/torta2.jpg" class="d-block w-100 carousel-fixed-img" alt="Torta">
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
            <img src="../files/img/brazoreina1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Brazo de Reina">
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
            <img src="../files/img/hojaldre1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Hojaldres">
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
            <img src="../files/img/pangrande1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Panes grandes">
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
            <img src="../files/img/brownie1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Brownies">
            <div class="card-body">
              <h5 class="card-title text-marron fw-bold">Brownies</h5>
              <p class="card-text text-gris-oscuro">Esponjosos con rellenos de Chocolate.</p>
            </div>
            <div class="card-overlay">
              <h5>Brownies</h5>
              <p>Receta familiar con más de 20 años de tradición en cada bocado.</p>
            </div>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="col animate__animated animate__fadeInUp animate__delay-5s">
          <div class="card h-100 rounded-3 shadow-sm">
            <img src="../files/img/croissants.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Croissants">
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
            <img src="../files/img/muffins1.jpg" class="card-img-top rounded-top-3 card-img-custom" alt="Muffins">
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
            <h6 class="text-cafe-oscuro bi bi-award-fill">
              Cada producto es elaborado con ingredientes frescos y naturales, siguiendo recetas que han pasado de generación en generación. Nuestro compromiso es llevarte el sabor auténtico del pan casero y con esta nueva innovación de pedidos en línea, queremos que disfrutes de nuestros productos sin salir de casa.
            </h6>
            <a href="#" class="btn btn-primary btn-rounded mt-3">Conoce más</a>
          </div>
          <div class="col-md-6 animate__animated animate__fadeInRight">
            <img src="../files/img/Local.png" class="img-fluid rounded-3 shadow" alt="Nuestra panadería">
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="text-white py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold mb-3">El Castillo del Pan</h5>
          <p class="mb-3">Tradición, calidad y sabor en cada producto. Tu panadería de confianza desde 1995.</p>
        </div>
        <div class="col-md-4 mb-4 offset-4 text-white">
          <h5 class="fw-bold mb-3">Contacto</h5>
          <p><i class="bi bi-geo-alt me-2"></i> Cl. 73 Sur, Bosa el Recreo. Bosa</p>
          <p><i class="bi bi-envelope me-2"></i>info@elcastillodelpan.com</p>
          <p><i class="bi bi-clock me-2"></i>Lun - Dom: 6:00 AM - 10:00 PM</p>
        </div>
      </div>
      <div class="col-md-12 text-center mt-4">
        <p class="text-muted mb-0 text-cafe-claro">Desarrollado con <i class="bi bi-heart-fill text-danger"></i> para nuestros clientes</p>
      </div>
      <hr class="my-4">
      <div class="row align-items-center">
        <div class="col-12 text-center">
          <p class="mb-0">&copy; 2025 El Castillo del Pan. Todos los derechos reservados.</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var carousel = new bootstrap.Carousel(document.getElementById('smallCarousel'), {
        interval: 4000,
        wrap: true,
        keyboard: true,
        pause: 'hover'
      });
      const cards = document.querySelectorAll('.card');
      cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
          carousel.pause();
        });
        card.addEventListener('mouseleave', () => {
          carousel.cycle();
        });
      });
    });
  </script>
</body>
</html>
