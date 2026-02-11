@extends('layouts.app')

@section('title', 'Carrito - El Castillo del Pan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylehomepage.css') }}">
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">
<style>
    :root {
        --panaderia-marron: #8b6f47;
        --panaderia-cafe-claro: #bb9467;
        --panaderia-beige: #f5ede4;
    }

    body {
        font-family: 'Quicksand', sans-serif !important;
        background-color: #bb9467 !important;
    }

    .cart-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    
    .cart-item-row {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border: none;
        transition: transform 0.2s;
    }

    .cart-item-row:hover { transform: translateY(-3px); }

    .cart-item-image img {
        border-radius: 10px;
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        padding: 5px 10px;
        border-radius: 10px;
        display: inline-flex;
    }

    .btn-quantity {
        background: #a67c52;
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 5px;
        cursor: pointer;
    }

    .quantity-input {
        width: 50px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-weight: bold;
    }

    .cart-summary {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        position: sticky;
        top: 100px;
    }

    .btn-checkout {
        background: #2c2c2c;
        color: white;
        border: none;
        width: 100%;
        padding: 15px;
        border-radius: 10px;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-checkout:hover { background: #000; }

    /* Estilo para el botón eliminar circular */
    .btn-remove-circle {
        background-color: #dc3545;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: 0.3s;
    }
    .btn-remove-circle:hover { background-color: #a71d2a; transform: scale(1.1); }
</style>
@endpush

@section('content')
@include('partials.navbar')

<main class="py-5">
    <div class="cart-container">
        <h1 class="text-center text-white fw-bold mb-2">Mis Compras</h1>
        <p class="text-center text-white mb-5">¡Qué buen gusto tienes!</p>

        <div class="row">
            <div class="col-lg-8">
                @forelse($cartItems ?? [] as $item)
                <div class="cart-item-row" data-item-id="{{ $item->id }}">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="cart-item-image">
                                <img src="{{ asset('images/' . ($item->producto_imagen ?? 'jugo.jpg')) }}" 
                                     alt="{{ $item->producto_nombre }}"
                                     onerror="this.src='{{ asset('images/jugo.jpg') }}'">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h5 class="fw-bold mb-1">{{ $item->producto_nombre }}</h5>
                            <p class="item-price text-muted mb-0" data-price="{{ $item->precio }}">
                                Precio unitario: ${{ number_format($item->precio, 0, ',', '.') }}
                            </p>
                            <div class="mt-3">
                                <button class="btn-remove-circle btn-remove" data-id="{{ $item->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3 text-center">
                            <label class="d-block small fw-bold mb-2">Cantidad</label>
                            <div class="quantity-controls">
                                <button class="btn-quantity btn-decrease" data-id="{{ $item->id }}">-</button>
                                <input type="number" value="{{ $item->cantidad }}" 
                                       min="1" max="{{ $item->stock ?? 99 }}" 
                                       class="quantity-input" data-id="{{ $item->id }}" readonly>
                                <button class="btn-quantity btn-increase" data-id="{{ $item->id }}">+</button>
                            </div>
                        </div>

                        <div class="col-md-2 text-end">
                            <label class="d-block small fw-bold mb-1">Subtotal</label>
                            <h4 class="row-total-display fw-bold" style="color: #8b6f47;">
                                ${{ number_format($item->precio * $item->cantidad, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center bg-white p-5 rounded-4 shadow">
                    <i class="fas fa-shopping-basket fa-4x mb-3" style="color: #ddd"></i>
                    <h3>Tu carrito está vacío</h3>
                    <a href="{{ route('menu') }}" class="btn btn-warning mt-3 px-4">Ir al Menú</a>
                </div>
                @endforelse

                @if(count($cartItems ?? []) > 0)
                <div class="d-flex justify-content-between">
                    <a href="{{ route('menu') }}" class="btn btn-outline-light px-4">← Seguir Comprando</a>
                    <button id="btnClearCart" class="btn btn-danger px-4">Vaciar Carrito</button>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h3 class="fw-bold mb-4">Resumen</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal:</span>
                        <span id="subtotal-value" class="fw-bold fs-5">${{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h4 fw-bold">Total:</span>
                        <span id="total-value" class="h4 fw-bold text-success">${{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <button class="btn-checkout" id="btnProcesarPedido">
                        PROCESAR PEDIDO
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    // Actualizar visualmente los precios sin recargar
    function updateCartTotals() {
        let totalGeneral = 0;
        document.querySelectorAll('.cart-item-row').forEach(row => {
            const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').dataset.price) || 0;
            const subtotal = qty * price;
            
            row.querySelector('.row-total-display').innerText = '$' + subtotal.toLocaleString('es-CO');
            totalGeneral += subtotal;
        });
        
        document.getElementById('subtotal-value').innerText = '$' + totalGeneral.toLocaleString('es-CO');
        document.getElementById('total-value').innerText = '$' + totalGeneral.toLocaleString('es-CO');
    }

    // Escuchar todos los clicks
    document.addEventListener('click', function(e) {
        const btnIncrease = e.target.closest('.btn-increase');
        const btnDecrease = e.target.closest('.btn-decrease');
        const btnRemove = e.target.closest('.btn-remove');
        const btnClear = e.target.closest('#btnClearCart');
        const btnCheckout = e.target.closest('#btnProcesarPedido');

        if (btnIncrease) handleUpdate(btnIncrease.dataset.id, 1);
        if (btnDecrease) handleUpdate(btnDecrease.dataset.id, -1);
        
        if (btnRemove) {
            if(confirm('¿Eliminar este producto?')) {
                fetch(`/api/carrito/remover/${btnRemove.dataset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                }).then(res => res.json()).then(data => { if(data.success) location.reload(); });
            }
        }

        if (btnClear) {
            if(confirm('¿Vaciar todo el carrito?')) {
                fetch('{{ route("api.carrito.vaciar") }}', {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                }).then(() => location.reload());
            }
        }

        if (btnCheckout) {
            btnCheckout.disabled = true;
            btnCheckout.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            fetch('{{ route("api.carrito.checkout") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('¡Pedido exitoso!');
                    window.location.href = '/';
                } else {
                    alert('Error: ' + data.message);
                    btnCheckout.disabled = false;
                    btnCheckout.innerText = 'PROCESAR PEDIDO';
                }
            }).catch(() => {
                btnCheckout.disabled = false;
                btnCheckout.innerText = 'PROCESAR PEDIDO';
            });
        }
    });

    function handleUpdate(id, change) {
        const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
        const newVal = parseInt(input.value) + change;
        if (newVal < 1 || newVal > (parseInt(input.max) || 99)) return;

        fetch('{{ route("api.carrito.actualizar") }}', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ id: id, cantidad: newVal })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = newVal;
                updateCartTotals();
            }
        });
    }
});
</script>
@endpush

