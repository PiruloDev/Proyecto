@extends('layouts.app')

@section('title', 'Carrito de Compras')

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

{{-- CORRECTO: Usar @push('scripts') --}}
@push('scripts') 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    console.log('--- 1. Script Carrito Iniciado ---');

    const CSRF_TOKEN = '{{ csrf_token() }}';
    
// ==========================
// CARGAR CARRITO (GET)
// ==========================
async function cargarCarrito() {
    try {
        const res = await fetch("/api/carrito");
        
        if (!res.ok) {
            const errorText = await res.text();
            console.error('Error del servidor al cargar:', errorText);
            throw new Error(`Error en la carga: ${res.status}`);
        }

        const data = await res.json();
        const items = data.items;

        const tbody = document.getElementById("tbodyCarrito");
        tbody.innerHTML = ""; 

        if (items.length === 0) { 
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">¡El carrito está vacío!</td>
                </tr>`;
            document.getElementById("totalGeneral").innerText = "0";
            return;
        }

        // --- Rellenar Tabla ---
        items.forEach(item => { 
            tbody.innerHTML += `
                <tr id="fila-${item.id}">
                    <td>${item.id}</td>
                    <td>${item.producto}</td>
                    <td>$${item.precio}</td> 
                    <td>
                        <div class="input-group input-group-sm">
                            <button class="btn btn-sm btn-secondary" onclick="actualizarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                            <span class="form-control text-center mx-1">${item.cantidad}</span>
                            <button class="btn btn-sm btn-primary" onclick="actualizarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                        </div>
                    </td>
                    <td>$${item.subtotal}</td> 
                    <td>
                        {{-- 🔥 LLAMADA CORREGIDA: Ahora llama a la función JS remover() --}}
                        <button class="btn btn-sm btn-danger" onclick="remover(${item.id})">
                            Eliminar
                        </button>
                    </td>
                </tr>
            `;
        });

        document.getElementById("totalGeneral").innerText = data.total;

    } catch (error) {
        console.error('Error al cargar el carrito (Catch):', error);
        document.getElementById("tbodyCarrito").innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">Error al cargar el carrito. Revisa la Consola (F12).</td>
            </tr>`;
    }
}

// ==========================
// ACTUALIZAR CANTIDAD (PATCH)
// ==========================
async function actualizarCantidad(id, cantidad) {
    if (cantidad < 0) {
        // Esto previene que se envíe cantidad negativa si el controlador no lo valida.
        return; 
    }

    try {
        const res = await fetch("/api/carrito/actualizar", {
            method: "POST", 
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": CSRF_TOKEN 
            },
            body: JSON.stringify({ 
                id: id, 
                cantidad: cantidad, 
                _method: 'PATCH' 
            }) 
        });

        const data = await res.json();
        
        // Si el controlador nos devuelve éxito, recargamos la tabla
        if (data.success) {
            // El controlador ya maneja la eliminación si cantidad es 0.
            if (cantidad === 0) {
                Swal.fire('Eliminado', data.message, 'success');
            }
            cargarCarrito();
        } else {
            Swal.fire('Error', data.message || 'Error al actualizar la cantidad.', 'error');
        }
    } catch (error) {
        console.error('Error de red al actualizar:', error);
        Swal.fire('Error de Conexión', 'No se pudo contactar con el servidor.', 'error');
    }
}

// ==========================
// REMOVER PRODUCTO (Llama a actualizarCantidad(id, 0))
// ==========================
async function remover(id) {
    try {
        const confirmResult = await Swal.fire({
            title: '¿Estás seguro?', text: "El producto se eliminará del carrito.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
        });

        if (!confirmResult.isConfirmed) return;

        // 🔥 CORRECCIÓN: Usamos el endpoint PATCH con cantidad 0, que ya funciona
        actualizarCantidad(id, 0); 

    } catch (error) {
        console.error('Error al solicitar la eliminación:', error);
        Swal.fire('Error', 'No se pudo iniciar el proceso de eliminación.', 'error');
    }
}

// ==========================
// CHECKOUT (POST)
// ==========================
async function checkout() {
    Swal.fire({
        title: 'Procesando Pedido',
        text: 'Por favor, espere mientras finalizamos su orden...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const res = await fetch("/api/carrito/checkout", { 
            method: "POST",
            headers: { "X-CSRF-TOKEN": CSRF_TOKEN }
        });
        
        const data = await res.json();
        if (data.success) {
            Swal.fire('¡Pedido Exitoso!', data.message, 'success');
            cargarCarrito(); 
        } else {
            Swal.fire('Error al Finalizar', data.message, 'error');
        }
    } catch (error) {
        console.error('Error de red en checkout:', error);
        Swal.fire('Error de Conexión', 'No se pudo contactar con el servicio de Pedidos.', 'error');
    }
}

// Cargar carrito al iniciar la página
document.addEventListener("DOMContentLoaded", cargarCarrito);
</script>
@endpush