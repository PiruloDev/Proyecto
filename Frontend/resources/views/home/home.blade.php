@extends('layouts.app')

@section('title', 'El Castillo del Pan - Inicio')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylehomepage.css') }}">
@endpush

@section('content')
@include('partials.navbar')

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
                        <img src="{{ asset('images/cupcake.jpg') }}" class="d-block w-100 carousel-fixed-img" alt="Panes grandes">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="fw-bold">Postres que deleitan</h5>
                            <p>Date el gusto que tanto quieres</p>
                        </div>
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="#" class="carousel-link">
                        <img src="{{ asset('images/pan.jpg') }}" class="d-block w-100 carousel-fixed-img" alt="Postres">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="fw-bold">La mejor masa de Pan</h5>
                            <p>Pan fresco todo el día, todos los días</p>
                        </div>
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="#" class="carousel-link">
                        <img src="{{ asset('images/pastel.jpg') }}" class="d-block w-100 carousel-fixed-img" alt="Torta">
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
            <div>
                <div class="card h-100 rounded-3 shadow-sm">
                    <img src="{{ asset('images/brazoreina1.jpg') }}" class="card-img-top rounded-top-3 card-img-custom" alt="Brazo de Reina">
                    <div class="card-body">
                        <h5 class="card-title text-marron fw-bold">Brazo de Reina</h5>
                        <p class="card-text text-gris-oscuro">Bizcochuelo suave relleno de arequipe artesanal, un clásico irresistible.</p>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div>
                <div class="card h-100 rounded-3 shadow-sm">
                    <img src="{{ asset('images/hojaldre1.jpg') }}" class="card-img-top rounded-top-3 card-img-custom" alt="Hojaldres">
                    <div class="card-body">
                        <h5 class="card-title text-marron fw-bold">Hojaldres</h5>
                        <p class="card-text text-gris-oscuro">Capas crujientes de masa fina con rellenos selectos y dorados perfectos.</p>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div>
                <div class="card h-100 rounded-3 shadow-sm">
                    <img src="{{ asset('images/pangrande1.jpg') }}" class="card-img-top rounded-top-3 card-img-custom" alt="Panes grandes">
                    <div class="card-body">
                        <h5 class="card-title text-marron fw-bold">Panes Grandes</h5>
                        <p class="card-text text-gris-oscuro">Pan artesanal con corteza dorada y miga suave, ideal para compartir.</p>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div>
                <div class="card h-100 rounded-3 shadow-sm">
                    <img src="{{ asset('images/brownie1.jpg') }}" class="card-img-top rounded-top-3 card-img-custom" alt="Brownies">
                    <div class="card-body">
                        <h5 class="card-title text-marron fw-bold">Brownies</h5>
                        <p class="card-text text-gris-oscuro">Esponjosos con rellenos de Chocolate.</p>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div>
                <div class="card h-100 rounded-3 shadow-sm">
                    <img src="{{ asset('images/croissants.jpg') }}" class="card-img-top rounded-top-3 card-img-custom" alt="Croissants">
                    <div class="card-body">
                        <h5 class="card-title text-marron fw-bold">Croissants</h5>
                        <p class="card-text text-gris-oscuro">Hojaldre francés auténtico, mantequillosos y perfectamente dorados.</p>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div>
                <div class="card h-100 rounded-3 shadow-sm">
                    <img src="{{ asset('images/muffins1.jpg') }}" class="card-img-top rounded-top-3 card-img-custom" alt="Muffins">
                    <div class="card-body">
                        <h5 class="card-title text-marron fw-bold">Muffins</h5>
                        <p class="card-text text-gris-oscuro">Esponjosos y llenos de sabor, perfectos para acompañar tu café.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="about-section bg-white py-5 my-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 animate__animated animate__fadeInLeft order-2 order-md-1">
                    <h1 class="text-gris-oscuro mb-4 font-weight-bold">La mejor calidad directo a tu casa</h1>
                    <p class="text-gris-oscuro lead">
                        En El Castillo del Pan, la calidad es nuestro compromiso diario. Seleccionamos los mejores ingredientes y aplicamos técnicas artesanales tradicionales. Cada producto lleva el sello de la excelencia, llevando el auténtico sabor de la panadería artesanal directamente a tu mesa, sin intermediarios.
                    </p>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight d-flex justify-content-center order-1 order-md-2 mb-4 mb-md-0">
                    <img src="{{ asset('images/panadero.jpg') }}" alt="Panadero" class="img-fluid rounded-3 shadow-sm" style="max-width: 325px;">
                </div>
            </div>
        </div>
    </section>

<section class="about-section bg-crema py-5 my-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 animate__animated animate__fadeInLeft d-flex justify-content-center mb-4 mb-md-0">
                    <img src="{{ asset('images/preparacion.jpg') }}" alt="Panadero" class="img-fluid rounded-3 shadow-sm" style="max-width: 350px;">
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight">
                    <h1 class="text-gris-oscuro mb-4 font-weight-bold">Nuestros productos son 100% frescos</h1>
                    <p class="text-gris-oscuro lead">
                    La frescura es nuestra promesa. Cada mañana horneamos antes del amanecer para ofrecerte pan recién hecho. Sin conservantes ni aditivos artificiales, solo ingredientes de calidad y proceso artesanal que garantizan sabor, textura y aroma natural desde el horno hasta tus manos.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="about-section bg-white py-5 my-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 animate__animated animate__fadeInLeft order-2 order-md-1">
                    <h1 class="text-gris-oscuro mb-4 font-weight-bold">Frescura que se siente en tu paladar</h1>
                    <p class="text-gris-oscuro lead">
                        La diferencia se nota al primer bocado. Corteza crujiente, miga esponjosa, aroma irresistible: eso es frescura real. Elaboramos cada producto el mismo día para que disfrutes texturas y sabores auténticos. Más que comer pan, vives una experiencia que despierta recuerdos y crea momentos especiales.
                    </p>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight d-flex justify-content-center order-1 order-md-2 mb-4 mb-md-0">
                    <img src="{{ asset('images/productoab.jpg') }}" alt="Panadero" class="img-fluid rounded-3 shadow-sm" style="max-width: 325px;">
                </div>
            </div>
        </div>
    </section>

    <!-- App Mobile Card Compacta -->
    <section class="container my-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h5 class="text-gris-oscuro mb-2 fw-bold">
                            <i class="fas fa-mobile-alt text-marron me-2"></i>
                            ¡Descarga nuestra App Móvil!
                        </h5>
                        <p class="text-gris-oscuro mb-2 small">
                            Haz pedidos, consulta el menú y promociones desde tu celular.
                        </p>
                        <a class="btn btn-link text-marron p-0 text-decoration-none small" data-bs-toggle="collapse" href="#appDetails" role="button" aria-expanded="false" aria-controls="appDetails">
                            <i class="fas fa-chevron-down me-1"></i> Ver más información
                        </a>
                    </div>
                    <button class="btn btn-marron btn-sm ms-3 text-white">
                        <i class="fas fa-download me-1"></i> Descargar
                    </button>
                </div>
                <div class="collapse mt-3" id="appDetails">
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/appmobile.jpg') }}" alt="App Móvil" class="img-fluid rounded-3 shadow-sm" style="max-width: 200px;">
                    </div>
                    <p class="text-gris-oscuro mb-0 small text-center">
                        Por esta razón es que hemos creado una Aplicación Móvil para nuestros clientes, desde el confort de sus celulares podrán hacer sus pedidos, ver nuestro menú, promociones y mucho más. ¡Descarga nuestra app y mantente conectado con El Castillo del Pan!
                    </p>
                </div>
            </div>
        </div>
    </section>
        </div>
    </section>
</main>

@include('partials.footer')
@endsection

@push('scripts')
@include('partials.auth-scripts')
<script>
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
@endpush
