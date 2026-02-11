@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">🛒 Carrito de Compras</h2>

    <div class="table-responsive">
        <table class="table table-bordered shadow-sm" id="tablaCarrito">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Precio (COP)</th>
                    <th style="width: 150px;">Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tbodyCarrito">
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status"></div>
                        <br>Cargando productos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-end mt-3">
        <h4>Total: <span id="totalGeneral" class="fw-bold">0</span> COP</h4>
    </div>

    <div class="text-end mt-2">
        <a href="{{ route('menu') }}" class="btn btn-outline-secondary me-2">Seguir Comprando</a>
        <button class="btn btn-success btn-lg" id="btnCheckout" onclick="checkout()">
            <i class="fas fa-check-circle me-2"></i>Finalizar Pedido
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    async function cargarCarrito() {
        try {
            const res = await fetch("/api/carrito", {
                headers: { "Accept": "application/json" }
            });
            
            if (!res.ok) throw new Error('Error al conectar con el servidor');

            const data = await res.json();
            const items = data.items || [];
            const tbody = document.getElementById("tbodyCarrito");
            const totalGeneral = document.getElementById("totalGeneral");
            
            tbody.innerHTML = "";

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">¡Tu carrito está vacío! 🥖</td></tr>';
                totalGeneral.innerText = "0";
                document.getElementById("btnCheckout").disabled = true;
                return;
            }

            document.getElementById("btnCheckout").disabled = false;

            items.forEach(item => {
                tbody.innerHTML += `
                    <tr class="align-middle">
                        <td>${item.id}</td>
                        <td class="fw-bold">${item.producto}</td>
                        <td>$${Number(item.precio).toLocaleString('es-CO')}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                                <span class="form-control text-center bg-light">${item.cantidad}</span>
                                <button class="btn btn-outline-primary" onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                            </div>
                        </td>
                        <td class="fw-bold text-primary">$${Number(item.subtotal).toLocaleString('es-CO')}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="remover(${item.id})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>`;
            });

            totalGeneral.innerText = Number(data.total).toLocaleString('es-CO');

        } catch (error) {
            console.error(error);
            document.getElementById("tbodyCarrito").innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar. Reintenta.</td></tr>';
        }
    }

    async function actualizarCantidad(id, cantidad) {
        if (cantidad < 1) {
            remover(id);
            return;
        }

        try {
            const res = await fetch("/api/carrito/actualizar", {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ id, cantidad })
            });

            const data = await res.json();
            if (data.success) {
                cargarCarrito(); 
            } else {
                Swal.fire('Atención', data.message || 'Error al actualizar', 'warning');
            }
        } catch (e) {
            Swal.fire('Error', 'No se pudo actualizar la cantidad', 'error');
        }
    }

    async function remover(id) {
        const result = await Swal.fire({
            title: '¿Eliminar del carrito?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const res = await fetch(`/api/carrito/remover/${id}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": CSRF_TOKEN, "Accept": "application/json" }
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ title: '¡Quitado!', icon: 'success', timer: 1000, showConfirmButton: false });
                    cargarCarrito();
                }
            } catch (e) {
                Swal.fire('Error', 'No se pudo eliminar', 'error');
            }
        }
    }

    async function checkout() {
        Swal.fire({
            title: 'Procesando Pedido',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false
        });

        try {
            const res = await fetch("/api/carrito/checkout", {
                method: "POST",
                headers: { 
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Accept": "application/json"
                }
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire('¡Éxito!', 'Tu pedido ha sido enviado.', 'success')
                    .then(() => window.location.href = '/menu');
            } else {
                Swal.fire('Error', data.message || 'Error en el pago', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Fallo de conexión', 'error');
        }
    }

    document.addEventListener("DOMContentLoaded", cargarCarrito);
</script>
@endpush
