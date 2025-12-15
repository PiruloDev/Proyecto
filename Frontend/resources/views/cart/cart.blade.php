@extends('layouts.app')

@section('title', 'Carrito - El Castillo del Pan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylehomepage.css') }}">
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">
<style>
:root {
  --color-crema: #f5f5dc;
  --color-marron: #8b4513;
  --color-gris-oscuro: #2c3e50;
  --color-cafe-claro: #d2b48c;
  --panaderia-marron-principal: #8b6f47;
  --panaderia-cafe-claro: #bb9467;
  --panaderia-cafe-medio: #a67c52;
  --panaderia-beige-claro: #f5ede4;
  --panaderia-gris-texto: #5a5a5a;
}

body {
  font-family: 'Quicksand', sans-serif !important;
  background-color: #bb9467 !important;
  line-height: 1.6;
}

.bg-crema { background-color: var(--color-crema) !important; }
.text-marron { color: var(--color-marron) !important; }
.border-marron { border-color: var(--color-marron) !important; }

.navbar {
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  position: sticky !important;
  top: 0;
  z-index: 1050 !important;
}

.logo {
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.nav-link {
  position: relative;
  transition: color 0.3s ease;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--color-marron);
  transition: width 0.3s ease;
}

.nav-link:hover::after {
  width: 100%;
}

.btn-user-glass {
  background: rgba(255, 255, 255, 0.9);
  border: 2px solid var(--color-marron);
  color: var(--color-marron);
  border-radius: 25px;
  padding: 8px 20px;
  transition: all 0.3s ease;
  font-weight: 600;
}

.btn-user-glass:hover {
  background: var(--color-marron);
  color: white;
}

.dropdown-menu-glass {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(139, 69, 19, 0.2);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-primary, .btn-rounded {
  background: linear-gradient(135deg, var(--color-marron), #a0522d) !important;
  border: none !important;
  border-radius: 25px !important;
  padding: 8px 20px !important;
  transition: all 0.3s ease !important;
  box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3) !important;
  color: white !important;
}

.btn-primary:hover, .btn-rounded:hover {
  background: linear-gradient(135deg, #a0522d, var(--color-marron)) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4) !important;
}

.cart-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 30px 20px;
}

.cart-header {
  margin-bottom: 3rem;
}

.cart-title {
  color: #fffef7;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.cart-subtitle {
  color: #f5ede4;
  font-size: 1.1rem;
  font-weight: 500;
}

.cart-item {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 4px 8px rgba(139, 111, 71, 0.15);
  transition: all 0.3s ease;
  border-left: 4px solid #a67c52;
  animation: slideIn 0.4s ease forwards;
}

.cart-item:hover {
  box-shadow: 0 8px 16px rgba(139, 111, 71, 0.2);
  transform: translateY(-2px);
}

.cart-item-image {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(139, 111, 71, 0.1);
  background: #f5ede4;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cart-item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.cart-item:hover .cart-item-image img {
  transform: scale(1.05);
}

.cart-item-title {
  color: #8b6f47;
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.cart-item-description {
  color: #5a5a5a;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  line-height: 1.5;
}

.price-label {
  color: #5a5a5a;
  font-size: 0.9rem;
  margin-right: 0.5rem;
}

.price-value {
  color: #a67c52;
  font-size: 1.1rem;
  font-weight: 700;
}

.quantity-label {
  color: #5a5a5a;
  font-size: 0.9rem;
  font-weight: 600;
  display: block;
  margin-bottom: 0.5rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.btn-quantity {
  width: 35px;
  height: 35px;
  border: none;
  background: linear-gradient(135deg, #bb9467, #a67c52);
  color: white;
  border-radius: 8px;
  font-weight: 700;
  font-size: 1.1rem;
  transition: all 0.2s ease;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-quantity:hover {
  background: linear-gradient(135deg, #a67c52, #8b6f47);
  transform: scale(1.1);
}

.quantity-input {
  width: 60px;
  text-align: center;
  border: 2px solid #bb9467;
  border-radius: 8px;
  padding: 0.5rem;
  font-weight: 600;
  color: #8b6f47;
  background: white;
}

.quantity-input:focus {
  outline: none;
  border-color: #8b6f47;
  box-shadow: 0 0 0 3px rgba(139, 111, 71, 0.1);
}

.cart-item-total {
  text-align: right;
}

.total-price {
  color: #8b6f47;
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
}

.unit-price {
  color: #a67c52;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

.btn-remove {
  background: #dc3545;
  color: white;
  border: none;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  font-size: 1.1rem;
  transition: all 0.2s ease;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-remove:hover {
  background: #c82333;
  transform: rotate(15deg) scale(1.1);
}

.cart-empty {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 8px rgba(139, 111, 71, 0.15);
}

.empty-icon {
  font-size: 5rem;
  color: #bb9467;
  margin-bottom: 1.5rem;
  opacity: 0.6;
}

.empty-title {
  color: #8b6f47;
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.empty-text {
  color: #5a5a5a;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.btn-explore {
  background: linear-gradient(135deg, #bb9467, #a67c52) !important;
  color: white !important;
  border: none !important;
  padding: 0.875rem 2rem !important;
  border-radius: 25px !important;
  font-weight: 600 !important;
  font-size: 1.1rem !important;
  transition: all 0.3s ease !important;
  text-decoration: none !important;
  display: inline-block !important;
}

.btn-explore:hover {
  background: linear-gradient(135deg, #a67c52, #8b6f47) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(139, 111, 71, 0.3) !important;
  color: white !important;
}

.cart-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-continue-shopping {
  background: #bb9467;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-block;
}

.btn-continue-shopping:hover {
  background: #a67c52;
  color: white;
  transform: translateY(-2px);
}

.btn-clear-cart {
  background: #dc3545;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-clear-cart:hover {
  background: #c82333;
  transform: translateY(-2px);
}

.cart-summary {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 8px 16px rgba(139, 111, 71, 0.2);
  position: sticky;
  top: 20px;
  border: 2px solid #bb9467;
}

.summary-title {
  color: #8b6f47;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f5ede4;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  color: #5a5a5a;
  font-size: 1rem;
}

.summary-row-label {
  font-weight: 600;
}

.summary-row-value {
  font-weight: 700;
  color: #a67c52;
}

.summary-divider {
  border: none;
  border-top: 1px solid #e8dfd5;
  margin: 1rem 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 0 0;
  border-top: 2px solid #bb9467;
  margin-top: 1rem;
}

.total-label-main {
  color: #8b6f47;
  font-size: 1.25rem;
  font-weight: 700;
}

.total-value {
  color: #8b6f47;
  font-size: 1.75rem;
  font-weight: 700;
}

.btn-checkout {
  width: 100%;
  background: linear-gradient(135deg, #8b6f47, #a67c52) !important;
  color: white !important;
  border: none !important;
  padding: 1rem !important;
  border-radius: 12px !important;
  font-weight: 700 !important;
  font-size: 1.1rem !important;
  margin-top: 1.5rem !important;
  transition: all 0.3s ease !important;
  cursor: pointer !important;
}

.btn-checkout:hover {
  background: linear-gradient(135deg, #6d5635, #8b6f47) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(139, 111, 71, 0.3) !important;
}

.trust-badges {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e8dfd5;
}

.badge-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: #f5ede4;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.badge-item:hover {
  background: #e8dfd5;
  transform: translateY(-2px);
}

.badge-item i {
  font-size: 1.5rem;
  color: #8b6f47;
  margin-bottom: 0.5rem;
}

.badge-item span {
  color: #5a5a5a;
  font-size: 0.75rem;
  font-weight: 600;
  text-align: center;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 991px) {
  .cart-title {
    font-size: 2rem;
  }

  .cart-summary {
    position: relative;
    top: 0;
    margin-top: 2rem;
  }
}

@media (max-width: 767px) {
  .cart-container {
    padding: 20px 15px;
  }

  .cart-title {
    font-size: 1.75rem;
  }

  .cart-item {
    padding: 1.25rem;
  }

  .cart-item-image {
    height: 120px;
    margin-bottom: 1rem;
  }

  .btn-remove {
    width: 100%;
    margin-top: 1rem;
    border-radius: 8px;
    height: auto;
    padding: 0.75rem;
  }

  .cart-actions {
    flex-direction: column;
  }

  .btn-continue-shopping,
  .btn-clear-cart {
    width: 100%;
  }

  .trust-badges {
    grid-template-columns: 1fr;
  }
}
</style>
@endpush

@section('content')
@include('partials.navbar')

<!-- Main Content -->
<main class="py-5">
    <div class="cart-container">
        <div class="cart-header text-center">
            <h1 class="cart-title">
                </i>Mis Compras
            </h1>
            <p class="cart-subtitle">¡Qué buen gusto tienes!</p>
        </div>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                @forelse($cartItems ?? [] as $item)
                <div class="cart-item" data-item-id="{{ $item->id }}">
                    <div class="row align-items-center">
                        <div class="col-md-3 col-sm-4">
                            <div class="cart-item-image">
                                @php
                                    $imagenProducto = $item->producto_imagen ?? 'jugo.jpg';
                                    $rutaImagen = asset('images/' . $imagenProducto);
                                    $imagenDefault = asset('images/jugo.jpg');
                                @endphp
                                <img src="{{ $rutaImagen }}"
                                     class="img-fluid"
                                     alt="{{ $item->producto_nombre }}"
                                     onerror="this.onerror=null;this.src='{{ $imagenDefault }}';"
                                     loading="lazy">
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-8">
                            <div class="row align-items-center">
                                <div class="col-lg-5 mb-3 mb-lg-0">
                                    <h5 class="cart-item-title">{{ $item->producto_nombre }}</h5>
                                    <p class="cart-item-description">{{ $item->producto_descripcion ?? 'Producto fresco y delicioso' }}</p>
                                    <div class="cart-item-price-mobile d-lg-none">
                                        <span class="price-label">Precio:</span>
                                        <span class="price-value">${{ number_format($item->precio, 0) }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6 mb-3 mb-lg-0">
                                    <label class="quantity-label">Cantidad:</label>
                                    <div class="quantity-controls">
                                        <button class="btn-quantity btn-decrease" data-id="{{ $item->id }}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number"
                                               value="{{ $item->cantidad }}"
                                               min="1"
                                               max="{{ $item->stock ?? 999 }}"
                                               class="quantity-input"
                                               data-id="{{ $item->id }}"
                                               readonly>
                                        <button class="btn-quantity btn-increase" data-id="{{ $item->id }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-6 mb-3 mb-lg-0">
                                    <div class="cart-item-total">
                                        <span class="total-label d-lg-none">Subtotal:</span>
                                        <h5 class="total-price" data-item-id="{{ $item->id }}">${{ number_format($item->precio * $item->cantidad, 0) }}</h5>
                                        <small class="unit-price d-none d-lg-block">${{ number_format($item->precio, 0) }} c/u</small>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-12 text-center text-lg-end">
                                    <button class="btn-remove" data-id="{{ $item->id }}" title="Eliminar producto">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="cart-empty">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="empty-title">Tu carrito está vacío</h3>
                    <p class="empty-text">¡Descubre nuestros deliciosos productos artesanales!</p>
                    <a href="{{ route('menu') }}" class="btn btn-explore">
                        <i class="fas fa-shopping-bag me-2"></i>Explorar Menú
                    </a>
                </div>
                @endforelse

                @if(isset($cartItems) && count($cartItems) > 0)
                <div class="cart-actions mt-4">
                    <a href="{{ route('menu') }}" class="btn btn-continue-shopping">
                        <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                    </a>
                    <button class="btn btn-clear-cart" id="btnClearCart">
                        <i class="fas fa-trash me-2"></i>Vaciar Carrito
                    </button>
                </div>
                @endif
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h3 class="summary-title">Resumen compra</h3>
                    <div class="summary-row">
                        <span class="summary-row-label">Subtotal:</span>
                        <span class="summary-row-value" id="subtotal-value">${{ number_format($subtotal ?? 0, 0) }}</span>
                    </div>

                    <hr class="summary-divider">
                    <div class="summary-total">
                        <span class="total-label-main">Total:</span>
                        <span class="total-value" id="total-value">${{ number_format($subtotal ?? 0, 0) }}</span>
                    </div>

                    @if(isset($cartItems) && count($cartItems) > 0)
                    <button class="btn-checkout" id="btnProcesarPedido">
                        <i class="fas fa-credit-card me-2"></i>Procesar Pedido
                    </button>
                    @endif
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
        document.querySelectorAll('.btn-increase').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                updateQuantity(id, 1);
            });
        });
        document.querySelectorAll('.btn-decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                updateQuantity(id, -1);
            });
        });
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                removeItem(id);
            });
        });

        const btnClearCart = document.getElementById('btnClearCart');
        if (btnClearCart) {
            btnClearCart.addEventListener('click', function() {
                Swal.fire({
                    title: '¿Vaciar carrito?',
                    text: 'Se eliminarán todos los productos del carrito',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, vaciar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('api.carrito.vaciar') }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Carrito vaciado',
                                    text: 'Se han eliminado todos los productos',
                                    confirmButtonColor: '#bb9467'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo vaciar el carrito',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                    }
                });
            });
        }

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
                        fetch('{{ route('api.carrito.checkout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Authorization': 'Bearer ' + localStorage.getItem('token')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Pedido realizado!',
                                    text: 'Tu pedido ha sido procesado exitosamente',
                                    confirmButtonColor: '#bb9467'
                                }).then(() => {
                                    window.location.href = '{{ route('menu') }}';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'No se pudo procesar el pedido',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un problema al procesar el pedido',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                    }
                });
            });
        }

        function updateQuantity(id, change) {
            const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
            if (input) {
                const currentValue = parseInt(input.value);
                const newValue = currentValue + change;
                const maxValue = parseInt(input.max) || 999;
                if (newValue >= 1 && newValue <= maxValue) {
                    input.value = newValue;
                    updateCartTotals();
                }
            }
        }

        function updateCartTotals() {
            let subtotal = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const input = item.querySelector('.quantity-input');
                const totalPriceEl = item.querySelector('.total-price');
                if (input && totalPriceEl) {
                    const quantity = parseInt(input.value);
                    const unitPriceText = item.querySelector('.unit-price')?.textContent || '0';
                    const unitPrice = parseFloat(unitPriceText.replace(/[^0-9.-]+/g, ''));
                    const itemTotal = unitPrice * quantity;
                    totalPriceEl.textContent = '$' + itemTotal.toLocaleString('es-CO', { minimumFractionDigits: 0 });
                    subtotal += itemTotal;
                }
            });

            document.getElementById('subtotal-value').textContent = '$' + subtotal.toLocaleString('es-CO', { minimumFractionDigits: 0 });
            document.getElementById('total-value').textContent = '$' + subtotal.toLocaleString('es-CO', { minimumFractionDigits: 0 });
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
                    const item = document.querySelector(`.cart-item[data-item-id="${id}"]`);
                    if (item) {
                        item.remove();
                        updateCartTotals();
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            location.reload();
                        }
                    }
                }
            });
        }
    });
</script>
@include('partials.auth-scripts')
@endpush
