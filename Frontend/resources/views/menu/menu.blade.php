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


<section class="py-5">
    <div class="container">
        
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

       
<div class="row" id="product-container">
    @forelse($productos ?? [] as $producto)
        <div class="col-lg-4 col-md-6 mb-4 product-card-item"
             data-nombre="{{ strtolower($producto->NOMBRE_PRODUCTO) }}">

            <div class="product-card card h-100 shadow-sm border-0 card-hover">
                <div class="card-img-container position-relative">
                    @php
                       
                        $imagenProducto = null;

                        
                        if (isset($producto->imagen) && !empty($producto->imagen)) {
                            $imagenProducto = $producto->imagen;
                        } elseif (isset($producto->IMAGEN) && !empty($producto->IMAGEN)) {
                            $imagenProducto = $producto->IMAGEN;
                        }
                        
                        elseif (isset($producto->IMAGEN_PRODUCTO) && !empty($producto->IMAGEN_PRODUCTO)) {
                            $imagenProducto = $producto->IMAGEN_PRODUCTO;
                        } elseif (isset($producto->imagen_producto) && !empty($producto->imagen_producto)) {
                            $imagenProducto = $producto->imagen_producto;
                        }
                        
                        else {
                            $imagenProducto = 'jugo.jpg'; 
                        }

                        
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
@include('partials.footer')
@endsection

@push('scripts')
@include('partials.auth-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>

.product-card-item.hidden {display: none;}
</style>
<script>
    
    async function agregarProductoAlCarrito(idProducto, nombreProducto) {
        try {
            const response = await fetch(`/api/carrito/agregar/${idProducto}`, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {

            Swal.fire({
                    icon: 'success',
                    title: '¡Añadido al pedido!',
                    text: `Has agregado "${nombreProducto}" con éxito.`,
                    showCancelButton: true,
                    confirmButtonColor: '#bb9467', 
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-shopping-cart"></i> Ir al carrito',
                    cancelButtonText: 'Seguir comprando',
                    reverseButtons: true
                }).then((result) => {
                    
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('carrito.index') }}";
                    }
                   
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al agregar',
                    text: data.message || 'Ocurrió un error al intentar agregar el producto.',
                    confirmButtonColor: '#bb9467',
                });
            }
        } catch (error) {
            console.error('Error de red:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo contactar con el servidor.',
                confirmButtonColor: '#bb9467',
            });
        }
    
    }


    document.addEventListener('DOMContentLoaded', function() {
       
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

        const addToCartButtons = document.querySelectorAll('.btn-agregar-pedido');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productoId;
                const productName = this.dataset.productoNombre;

                agregarProductoAlCarrito(productId, productName);
            });
        });
    });
</script>
@endpush
