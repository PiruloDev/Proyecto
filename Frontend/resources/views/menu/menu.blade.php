@extends('layouts.app')

@section('title', 'El Castillo del Pan - Menú')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylehomepage.css') }}">
<link rel="stylesheet" href="{{ asset('css/stylemenu.css') }}">
<style>
    body {
        background-color: #bb9467 !important;
    }
    .product-card {
        background-color: white !important;
        border: 1px solid #e0e0e0 !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .card {
        background-color: white !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<header>
    <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm animate__animated animate__fadeInDown">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logoprincipal.jpg') }}" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
                <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-marron fw-semibold" href="{{ route('menu') }}" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ¡Explorar!
                        </a>
                        <ul class="dropdown-menu bg-crema shadow rounded-3 border-0 mt-2" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item text-marron fw-semibold py-2" href="{{ route('menu') }}">Ver Menú</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-marron fw-semibold" href="#">Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-marron fw-semibold" href="#">Contáctanos</a>
                    </li>
                </ul>
                @auth
                    <div class="dropdown">
                        <a class="btn btn-user-glass dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-glass">
                            <li><a class="dropdown-item" href="{{ route('dashboard.client') }}">
                                <i class="fas fa-user-circle me-2"></i>Mi Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-rounded fw-bold ms-3">Acceder</a>
                @endauth
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
                <img src="{{ asset('images/Pan1.jpg') }}" class="img-fluid" alt="Imagen representativa de un pan">
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
    @forelse($productos ?? [] as $producto)
        <div class="col-lg-4 col-md-6 mb-4 product-card-item" 
             data-nombre="{{ strtolower($producto->NOMBRE_PRODUCTO) }}">

            <div class="product-card card h-100 shadow-sm border-0 card-hover">
                <div class="card-img-container position-relative">

                    <img src="{{ asset('images/' . $producto->imagen) }}" 
                         class="card-img-top product-image" 
                         alt="{{ $producto->NOMBRE_PRODUCTO }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/pan-rtzqhi1ok4k1bxlo.jpg') }}';">

                    <div class="price-badge">
                        ${{ number_format($producto->PRECIO_PRODUCTO, 0) }}
                    </div>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-marron fw-bold mb-2">
                        {{ $producto->NOMBRE_PRODUCTO }}
                    </h5>

                    <p class="card-text text-muted flex-grow-1">
                        {{ $producto->DESCRIPCION_PRODUCTO ?? 'Producto fresco y delicioso' }}
                    </p>

                    <div class="product-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="availability-badge">
                                <i class="fas fa-check-circle me-1"></i>Disponible
                            </span>
                            <small class="text-muted stock-info">
                                Stock: {{ $producto->STOCK_ACTUAL }}
                            </small>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-agregar-pedido"
                                data-producto-id="{{ $producto->ID_PRODUCTO }}"
                                data-producto-nombre="{{ $producto->NOMBRE_PRODUCTO }}"
                                data-producto-precio="{{ $producto->PRECIO_PRODUCTO }}">

                            <i class="fas fa-plus-circle me-2"></i>Agregar pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center">
            <div class="alert alert-info">
                <h4 class="alert-heading">¡No hay productos!</h4>
                <p>Actualmente no tenemos productos disponibles en el menú. Por favor, vuelve a intentarlo más tarde.</p>
            </div>
        </div>
    @endforelse
</div>

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
                    <li><a href="{{ url('/') }}" class="text-light text-decoration-none">Inicio</a></li>
                    <li><a href="{{ route('menu') }}" class="text-light text-decoration-none">Menú</a></li>
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
@endsection

@push('scripts')
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
                
                @guest
                    Swal.fire({
                        icon: 'warning',
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para agregar productos al carrito',
                        confirmButtonColor: '#bb9467',
                        confirmButtonText: 'Ir a login'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                @endguest
                
                // Aquí iría la lógica para agregar al carrito con fetch/axios
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado!',
                    text: `${productName} ha sido agregado a tu carrito`,
                    confirmButtonColor: '#bb9467',
                    showCancelButton: true,
                    confirmButtonText: 'Ver carrito',
                    cancelButtonText: 'Continuar comprando'
                });
            });
        });
    });
</script>
@endpush
