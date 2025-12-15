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
@include('partials.navbar')

<!-- Sección de Productos por Categorías -->
<section class="py-5">
    <div class="container">
        <!-- Barra de Búsqueda -->
        <div class="row mb-5 justify-content-center">
            <div class="col-lg-8">
                <div class="search-container text-center">
                    <h3 class="text-white fw-bold mb-4">
                        <i class="fas fa-search me-2"></i>Tengo ganas de...
                    </h3>
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" id="productSearchInput" class="form-control" placeholder="Buscar productos..." aria-label="Buscar productos">
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
                    @php
                        // Intentar obtener la imagen del producto de varias formas
                        $imagenProducto = null;

                        // Prioridad 1: Campo 'imagen' o 'IMAGEN' del producto
                        if (isset($producto->imagen) && !empty($producto->imagen)) {
                            $imagenProducto = $producto->imagen;
                        } elseif (isset($producto->IMAGEN) && !empty($producto->IMAGEN)) {
                            $imagenProducto = $producto->IMAGEN;
                        }
                        // Prioridad 2: Campo 'IMAGEN_PRODUCTO' o 'imagen_producto'
                        elseif (isset($producto->IMAGEN_PRODUCTO) && !empty($producto->IMAGEN_PRODUCTO)) {
                            $imagenProducto = $producto->IMAGEN_PRODUCTO;
                        } elseif (isset($producto->imagen_producto) && !empty($producto->imagen_producto)) {
                            $imagenProducto = $producto->imagen_producto;
                        }
                        // Prioridad 3: Usar imagen por defecto basada en categoría o nombre
                        else {
                            $imagenProducto = 'jugo.jpg'; // Imagen por defecto
                        }

                        // Construir la ruta completa de la imagen
                        $rutaImagen = asset('images/' . $imagenProducto);
                        $imagenDefault = asset('images/jugo.jpg');
                    @endphp

                    <img src="{{ $rutaImagen }}"
                         class="card-img-top product-image"
                         alt="{{ $producto->NOMBRE_PRODUCTO }}"
                         onerror="this.onerror=null;this.src='{{ $imagenDefault }}';"
                         loading="lazy">

                    <div class="price-badge">
                        ${{ number_format($producto->PRECIO_PRODUCTO, 0) }}
                    </div>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-marron fw-bold mb-2">
                        {{ $producto->NOMBRE_PRODUCTO }}
                    </h5>

                    <p class="card-text text-muted flex-grow-1">
                        {{ $producto->DESCRIPCION_PRODUCTO ?? 'Producto fresco y delicioso de nuestra panadería artesanal' }}
                    </p>

                    <div class="product-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            @if(isset($producto->STOCK_ACTUAL) && $producto->STOCK_ACTUAL > 0)
                                <span class="availability-badge">
                                    <i class="fas fa-check-circle me-1"></i>Disponible
                                </span>
                                <small class="text-muted stock-info">
                                    Stock: {{ $producto->STOCK_ACTUAL }}
                                </small>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times-circle me-1"></i>Sin stock
                                </span>
                                <small class="text-muted">No disponible</small>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid">
                        @if(isset($producto->STOCK_ACTUAL) && $producto->STOCK_ACTUAL > 0)
                            <button class="btn btn-agregar-pedido"
                                    data-producto-id="{{ $producto->ID_PRODUCTO }}"
                                    data-producto-nombre="{{ $producto->NOMBRE_PRODUCTO }}"
                                    data-producto-precio="{{ $producto->PRECIO_PRODUCTO }}">
                                <i class="fas fa-plus-circle me-2"></i>Agregar pedido
                            </button>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-ban me-2"></i>No disponible
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center">
            <div class="alert alert-info shadow-sm">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h4 class="alert-heading">¡No hay productos disponibles!</h4>
                <p class="mb-0">Actualmente no tenemos productos en el menú. Por favor, vuelve a intentarlo más tarde.</p>
            </div>
        </div>
    @endforelse
</div>
    </div>
</section>

<!-- Sección de Llamada a la Acción -->
<section class="py-5 bg-success text-white text-center">
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

@include('partials.footer')
@endsection

@push('scripts')
@include('partials.auth-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Estilo simple para manejar la ocultación en la búsqueda */
.product-card-item.hidden {display: none;}
</style>
<script>
    // Función centralizada para manejar la adición al carrito
    async function agregarProductoAlCarrito(idProducto, nombreProducto) {
        try {
            // 1. Iniciar la llamada API para agregar a la sesión de Laravel
            const response = await fetch(`/api/carrito/agregar/${idProducto}`, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            // 2. Intentar parsear la respuesta JSON (puede fallar si hay errores de red o servidor)
            const data = await response.json();

            if (data.success) {
                // Éxito: Muestra una notificación rápida (Toast)
                Swal.fire({
                    icon: 'success',
                    title: '¡Producto Agregado!',
                    text: `${nombreProducto} ha sido añadido al carrito. Redirigiendo...`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                });

                // 3. Redirección CORREGIDA: Apunta a la vista del carrito (ruta index del CarritoController)
                setTimeout(() => {
                    // CAMBIAR 'nombre-ruta-del-carrito' por la ruta real que apunta a CarritoController@index
                    window.location.href = "{{ route('carrito.index') }}";
                }, 1500);

            } else {
                // Manejo de errores de la API (ej: producto no encontrado, error de conexión al backend de Java, etc.)
                Swal.fire({
                    icon: 'error',
                    title: 'Error al agregar',
                    text: data.message || 'Ocurrió un error desconocido al agregar el producto.',
                    confirmButtonColor: '#bb9467',
                });
            }
        } catch (error) {
            // Error de red
            console.error('Error de red al agregar producto:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo contactar con el servidor. Verifica tu backend y conexión.',
                confirmButtonColor: '#bb9467',
            });
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        // Lógica de búsqueda (sin cambios)
        const searchInput = document.getElementById('productSearchInput');
        const productContainer = document.getElementById('product-container');
        const productCards = productContainer.getElementsByClassName('product-card-item');

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

        // Event listener para el botón "Agregar pedido" (sin cambios, llama a la función corregida)
        const addToCartButtons = document.querySelectorAll('.btn-agregar-pedido');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productoId;
                const productName = this.dataset.productoNombre;

                // Llama a la función que ahora agrega y redirige al carrito
                agregarProductoAlCarrito(productId, productName);
            });
        });
    });
</script>
@endpush
