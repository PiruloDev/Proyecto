@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4 text-center">🛒 Carrito de Compras</h2>

    <!-- Tabla del carrito -->
    <div class="table-responsive">
        <table class="table table-bordered" id="tablaCarrito">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Precio (COP)</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tbodyCarrito">
                <tr>
                    <td colspan="6" class="text-center">Cargando...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Total -->
    <div class="text-end mt-3">
        <h4>Total: <span id="totalGeneral">0</span> COP</h4>
    </div>

    <!-- Botón checkout -->
    <div class="text-end mt-2">
        <button class="btn btn-success" onclick="checkout()">
            Finalizar Pedido
        </button>
    </div>

</div>
@endsection

@section('scripts')
<script>
// ==========================
// CARGAR CARRITO
// ==========================
async function cargarCarrito() {
    const res = await fetch("/api/carrito");
    const data = await res.json();

    const tbody = document.getElementById("tbodyCarrito");
    tbody.innerHTML = ""; // Limpiar tabla

    if (data.carrito.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">El carrito está vacío</td>
            </tr>`;
        document.getElementById("totalGeneral").innerText = "0";
        return;
    }

    data.carrito.forEach(item => {
        let subtotal = (item.precio * item.cantidad).toFixed(2);

        tbody.innerHTML += `
            <tr id="fila-${item.id}">
                <td>${item.id}</td>
                <td>${item.nombre}</td>
                <td>${item.precio} COP</td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                    <span class="mx-2">${item.cantidad}</span>
                    <button class="btn btn-sm btn-primary" onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                </td>
                <td>${subtotal} COP</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="remover(${item.id})">
                        Eliminar
                    </button>
                </td>
            </tr>
        `;
    });

    document.getElementById("totalGeneral").innerText = data.total;
}

// ==========================
// ACTUALIZAR CANTIDAD
// ==========================
async function actualizarCantidad(id, cantidad) {
    if (cantidad < 0) return;

    const res = await fetch("/api/carrito/actualizar", {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, cantidad })
    });

    const data = await res.json();

    if (data.success) {
        cargarCarrito();
    }
}

// ==========================
// REMOVER PRODUCTO
// ==========================
async function remover(id) {
    const res = await fetch(`/api/carrito/remover/${id}`, {
        method: "DELETE"
    });

    const data = await res.json();

    if (data.success) {
        cargarCarrito();
    }
}

// ==========================
// CHECKOUT
// ==========================
async function checkout() {
    const res = await fetch("/api/carrito/checkout", { method: "POST" });
    const data = await res.json();

    if (data.success) {
        alert("Pedido realizado con éxito 🎉");
        cargarCarrito();
    } else {
        alert("Error: " + data.message);
    }
}

// Cargar carrito al iniciar
document.addEventListener("DOMContentLoaded", cargarCarrito);
</script>
@endsection
