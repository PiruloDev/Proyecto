@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4 text-center">🛒 Carrito de Compras</h2>

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

    <div class="text-end mt-3">
        <h4>Total: <span id="totalGeneral">0</span> COP</h4>
    </div>

    <div class="text-end mt-2">
        <button class="btn btn-success" onclick="checkout()">
            Finalizar Pedido
        </button>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Obtener el token CSRF de la meta etiqueta para usarlo en las peticiones
    const CSRF_TOKEN = '{{ csrf_token() }}';

// ==========================
// CARGAR CARRITO (GET)
// ==========================
async function cargarCarrito() {
    try {
        // GET /api/carrito no necesita token CSRF si no estás modificando la sesión, pero es buena práctica de flujo.
        const res = await fetch("/api/carrito");
        
        if (!res.ok) {
            throw new Error(`Error en la carga: ${res.status}`);
        }

        const data = await res.json();
        const tbody = document.getElementById("tbodyCarrito");
        tbody.innerHTML = ""; // Limpiar tabla

        if (data.carrito.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">¡El carrito está vacío!</td>
                </tr>`;
            document.getElementById("totalGeneral").innerText = "0";
            return;
        }

        // --- Rellenar Tabla ---
        data.carrito.forEach(item => {
            // Aseguramos el formateo de números
            const precioFormatted = new Intl.NumberFormat('es-CO').format(item.precio);
            const subtotal = (item.precio * item.cantidad);
            const subtotalFormatted = new Intl.NumberFormat('es-CO').format(subtotal.toFixed(2));
            
            tbody.innerHTML += `
                <tr id="fila-${item.id}">
                    <td>${item.id}</td>
                    <td>${item.nombre}</td>
                    <td>$${precioFormatted}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                        <span class="mx-2">${item.cantidad}</span>
                        <button class="btn btn-sm btn-primary" onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                    </td>
                    <td>$${subtotalFormatted}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="remover(${item.id})">
                            Eliminar
                        </button>
                    </td>
                </tr>
            `;
        });

        // Actualizar Total General
        const totalFormatted = new Intl.NumberFormat('es-CO').format(parseFloat(data.total));
        document.getElementById("totalGeneral").innerText = totalFormatted;

    } catch (error) {
        console.error('Error al cargar el carrito:', error);
        document.getElementById("tbodyCarrito").innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">Error al cargar el carrito.</td>
            </tr>`;
    }
}

// ==========================
// ACTUALIZAR CANTIDAD (PATCH)
// ==========================
async function actualizarCantidad(id, cantidad) {
    if (cantidad < 0) return; // Previene cantidades negativas

    try {
        const res = await fetch("/api/carrito/actualizar", {
            method: "PATCH",
            headers: { 
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN // AÑADIDO: Token CSRF para peticiones que modifican datos
            },
            // CORREGIDO: Aseguramos el envío del objeto JSON
            body: JSON.stringify({ id: id, cantidad: cantidad })
        });

        const data = await res.json();

        if (data.success) {
            // Recargar el carrito para actualizar la tabla y el total
            cargarCarrito();
            if (data.removed) {
                 Swal.fire('Eliminado', 'Producto removido por alcanzar cantidad 0.', 'warning');
            }
        } else {
             Swal.fire('Error', data.message || 'Error al actualizar la cantidad.', 'error');
        }
    } catch (error) {
        console.error('Error de red al actualizar:', error);
         Swal.fire('Error de Conexión', 'No se pudo contactar con el servidor.', 'error');
    }
}

// ==========================
// REMOVER PRODUCTO (DELETE)
// ==========================
async function remover(id) {
    try {
        if (!confirm('¿Estás seguro de que quieres eliminar este producto?')) return;

        const res = await fetch(`/api/carrito/remover/${id}`, {
            method: "DELETE",
            headers: {
                 "X-CSRF-TOKEN": CSRF_TOKEN // AÑADIDO: Token CSRF para DELETE
            }
        });

        const data = await res.json();

        if (data.success) {
            Swal.fire('Eliminado', data.message, 'success');
            cargarCarrito();
        } else {
             Swal.fire('Error', data.message || 'Error al remover el producto.', 'error');
        }
    } catch (error) {
        console.error('Error de red al remover:', error);
        Swal.fire('Error de Conexión', 'No se pudo contactar con el servidor.', 'error');
    }
}

// ==========================
// CHECKOUT (POST)
// ==========================
async function checkout() {
    try {
        const res = await fetch("/api/carrito/checkout", { 
            method: "POST",
            headers: {
                 "X-CSRF-TOKEN": CSRF_TOKEN // AÑADIDO: Token CSRF para POST
            }
        });
        
        const data = await res.json();

        if (data.success) {
            Swal.fire('¡Pedido Exitoso!', data.message, 'success');
            cargarCarrito(); // Vuelve a cargar el carrito (debería quedar vacío)
        } else {
            Swal.fire('Error al Finalizar', data.message, 'error');
        }
    } catch (error) {
        console.error('Error de red en checkout:', error);
        Swal.fire('Error de Conexión', 'No se pudo contactar con el servicio de Pedidos.', 'error');
    }
}

// Cargar carrito al iniciar
document.addEventListener("DOMContentLoaded", cargarCarrito);
</script>
@endsection