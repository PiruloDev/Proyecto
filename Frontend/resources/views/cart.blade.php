@extends('layouts.app')

@section('title', 'Carrito de Compras - El Castillo del Pan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylehomepage.css') }}">
<style>
    body {
        background-color: #bb9467 !important;
    }
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .cart-item {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .cart-summary {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: sticky;
        top: 20px;
    }
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .quantity-controls button {
        width: 35px;
        height: 35px;
        border: none;
        background: #bb9467;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }
    .quantity-controls input {
        width: 60px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<header>
    <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logoprincipal.jpg') }}" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
                <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-marron fw-semibold" href="{{ url('/') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-marron fw-semibold" href="{{ route('menu') }}">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-marron fw-semibold active" href="#">Carrito</a>
                    </li>
                </ul>
                @auth
                    <div class="dropdown">
                        <a class="btn btn-user-glass dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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

<!-- Main Content -->
<main class="py-5">
    <div class="cart-container">
        <h1 class="text-center text-white mb-5">
            <i class="fas fa-shopping-cart me-2"></i>Mi Carrito de Compras
        </h1>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                @forelse($cartItems ?? [] as $item)
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="{{ asset('images/' . $item->producto_imagen) }}" 
                                 class="img-fluid rounded" 
                                 alt="{{ $item->producto_nombre }}"
                                 onerror="this.src='{{ asset('images/pan-rtzqhi1ok4k1bxlo.jpg') }}'">
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1">{{ $item->producto_nombre }}</h5>
                            <p class="text-muted mb-0">{{ $item->producto_descripcion }}</p>
                        </div>
                        <div class="col-md-3">
                            <div class="quantity-controls">
                                <button class="btn-decrease" data-id="{{ $item->id }}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" 
                                       value="{{ $item->cantidad }}" 
                                       min="1" 
                                       class="form-control quantity-input"
                                       data-id="{{ $item->id }}"
                                       readonly>
                                <button class="btn-increase" data-id="{{ $item->id }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <h5 class="mb-0">${{ number_format($item->precio * $item->cantidad, 2) }}</h5>
                            <small class="text-muted">${{ number_format($item->precio, 2) }} c/u</small>
                        </div>
                        <div class="col-md-1 text-center">
                            <button class="btn btn-sm btn-danger btn-remove" data-id="{{ $item->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="cart-item text-center py-5">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd;"></i>
                    <h4 class="mt-3">Tu carrito está vacío</h4>
                    <p class="text-muted">¡Agrega algunos productos deliciosos!</p>
                    <a href="{{ route('menu') }}" class="btn btn-primary btn-rounded mt-3">
                        <i class="fas fa-shopping-bag me-2"></i>Ir al Menú
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="mb-4">Resumen del Pedido</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <strong>${{ number_format($subtotal ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>IVA (19%):</span>
                        <strong>${{ number_format(($subtotal ?? 0) * 0.19, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <h5>Total:</h5>
                        <h5 class="text-primary">${{ number_format(($subtotal ?? 0) * 1.19, 2) }}</h5>
                    </div>
                    
                    @if(isset($cartItems) && count($cartItems) > 0)
                    <button class="btn btn-primary w-100 btn-rounded mb-3" id="btnProcesarPedido">
                        <i class="fas fa-credit-card me-2"></i>Procesar Pedido
                    </button>
                    @endif
                    
                    <a href="{{ route('menu') }}" class="btn btn-outline-primary w-100 btn-rounded">
                        <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="py-5 bg-gris-oscuro text-white">
    <div class="container text-center">
        <p class="mb-0">&copy; 2024 El Castillo del Pan. Todos los derechos reservados.</p>
    </div>
</footer>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aumentar cantidad
        document.querySelectorAll('.btn-increase').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                updateQuantity(id, 1);
            });
        });
        
        // Disminuir cantidad
        document.querySelectorAll('.btn-decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                updateQuantity(id, -1);
            });
        });
        
        // Eliminar item
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                removeItem(id);
            });
        });
        
        // Procesar pedido
        const btnProcesar = document.getElementById('btnProcesarPedido');
        if (btnProcesar) {
            btnProcesar.addEventListener('click', function() {
                Swal.fire({
                    title: '¿Confirmar pedido?',
                    text: 'Se procesará tu pedido',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#bb9467',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí iría la lógica para procesar el pedido
                        window.location.href = "{{ url('/pedido/procesar') }}";
                    }
                });
            });
        }
        
        function updateQuantity(id, change) {
            // Aquí iría la lógica con fetch/axios para actualizar cantidad
            console.log('Actualizar cantidad:', id, change);
        }
        
        function removeItem(id) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Se quitará este producto del carrito',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría la lógica con fetch/axios para eliminar
                    console.log('Eliminar item:', id);
                }
            });
        }
    });
</script>
@endpush
