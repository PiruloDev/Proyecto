// Variable para la instancia del modal de Bootstrap
let editModalInstance;

// Datos de ejemplo para demostración
let historial = [
    { idProducto: 1, idProduccion: 101, cantidadProducida: 150, fechaProduccion: '2025-10-01' },
    { idProducto: 2, idProduccion: 102, cantidadProducida: 200, fechaProduccion: '2025-10-01' },
    { idProducto: 1, idProduccion: 103, cantidadProducida: 180, fechaProduccion: '2025-10-02' }
];

/**
 * Muestra un mensaje de alerta temporal en la interfaz. (Adaptado a clases de Bootstrap)
 * @param {string} message - El mensaje a mostrar.
 * @param {'success'|'error'} type - El tipo de alerta.
 */
function showAlert(message, type = 'success') {
    const alert = document.getElementById('alert-message');
    const alertText = document.getElementById('alert-text');
    
    alertText.innerHTML = message;
    
    // Limpiar clases y aplicar las de Bootstrap
    alert.className = 'alert alert-dismissible fade show mb-4 border-start border-4';
    
    if (type === 'success') {
        alert.classList.add('alert-success', 'border-success');
    } else if (type === 'error') {
        alert.classList.add('alert-danger', 'border-danger');
    }
    
    alert.classList.remove('d-none'); // Mostrar la alerta
    
    // Ocultar la alerta después de 5 segundos
    setTimeout(() => {
        alert.classList.remove('show');
        alert.classList.add('d-none'); 
    }, 5000);
}

/**
 * Renderiza la tabla de historial de producción con los datos actuales.
 */
function renderHistorial() {
    const tbody = document.getElementById('historialBody');
    // Mapeo de los registros a filas HTML
    tbody.innerHTML = historial.map(registro => `
        <tr class="table-row">
            <td class="p-3 text-dark">${registro.idProducto}</td>
            <td class="p-3 text-muted">${registro.idProduccion}</td>
            <td class="p-3 text-muted">${registro.cantidadProducida}</td>
            <td class="p-3 text-muted">${registro.fechaProduccion}</td>
            <td class="p-3 text-center">
                <div class="d-flex justify-content-center gap-2">
                    <button onclick="openModal(${registro.idProduccion}, ${registro.cantidadProducida}, ${registro.idProducto})" 
                                class="btn btn-sm btn-outline-info rounded-circle" title="Editar">
                        ✏️
                    </button>
                    <button onclick="deleteRegistro(${registro.idProduccion})" 
                                class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar">
                        🗑️
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// =========================================================
// EVENT LISTENERS
// =========================================================

document.addEventListener('DOMContentLoaded', () => {
    renderHistorial();
    // 1. Inicializar la instancia del modal de Bootstrap al cargar
    const modalElement = document.getElementById('editModal');
    if (modalElement) {
        editModalInstance = new bootstrap.Modal(modalElement);
    }
});


// Evento para Registrar Nueva Producción (POST)
document.getElementById('formRegistrar').addEventListener('submit', (e) => {
    e.preventDefault();
    const idProducto = document.getElementById('idProducto').value;
    const cantidad = document.getElementById('cantidad').value;
    
    const newId = historial.length > 0 ? Math.max(...historial.map(r => r.idProduccion)) + 1 : 101; 
    
    historial.push({
        idProducto: parseInt(idProducto),
        idProduccion: newId,
        cantidadProducida: parseInt(cantidad),
        fechaProduccion: new Date().toISOString().split('T')[0]
    });
    
    renderHistorial();
    showAlert('✅ Producción registrada exitosamente', 'success');
    e.target.reset(); 
});

// Evento para Consultar Receta
document.getElementById('formReceta').addEventListener('submit', (e) => {
    e.preventDefault();
    const idProducto = document.getElementById('idProductoReceta').value;
    
    const recetaResult = document.getElementById('recetaResult');
    const recetaContent = document.getElementById('recetaContent');
    
    recetaContent.textContent = `Receta para Producto ID: ${idProducto}\n\nIngredientes:\n- Harina: 500g\n- Agua: 300ml\n- Levadura: 10g\n- Sal: 8g`;
    recetaResult.classList.remove('d-none');
    
    showAlert('✅ Receta consultada correctamente', 'success');
});

// Evento para Actualización Completa (PUT)
document.getElementById('formPut').addEventListener('submit', (e) => {
    e.preventDefault();
    const idProduccion = parseInt(document.getElementById('modalIdProduccionPut').value);
    const idProducto = parseInt(document.getElementById('modalIdProductoPut').value);
    const cantidad = parseInt(document.getElementById('modalCantidadPut').value);
    
    const index = historial.findIndex(r => r.idProduccion === idProduccion);
    if (index !== -1) {
        historial[index].idProducto = idProducto;
        historial[index].cantidadProducida = cantidad;
        renderHistorial();
        showAlert('✅ Registro actualizado completamente (PUT)', 'success');
        closeModal();
    }
});

// Evento para Actualización Parcial (PATCH)
document.getElementById('formPatch').addEventListener('submit', (e) => {
    e.preventDefault();
    const idProduccion = parseInt(document.getElementById('modalIdProduccionPatch').value);
    const cantidad = document.getElementById('modalCantidadPatch').value;
    
    const index = historial.findIndex(r => r.idProduccion === idProduccion);
    if (index !== -1 && cantidad) {
        historial[index].cantidadProducida = parseInt(cantidad);
        renderHistorial();
        showAlert('✅ Registro actualizado parcialmente (PATCH)', 'success');
        closeModal();
    }
});

// =========================================================
// FUNCIONES DE MODAL Y ELIMINACIÓN (Usando métodos de Bootstrap)
// =========================================================

/**
 * Abre el modal de edición y precarga los datos.
 */
function openModal(idProduccion, cantidad, idProducto) {
    if (!editModalInstance) return;

    // Llenar formularios del modal
    document.getElementById('modalIdProduccionPut').value = idProduccion;
    document.getElementById('modalIdProduccionPatch').value = idProduccion;
    document.getElementById('modalCantidadPut').value = cantidad;
    document.getElementById('modalIdProductoPut').value = idProducto;
    document.getElementById('modalCantidadPatch').value = ''; 
    
    editModalInstance.show(); // Usar método de Bootstrap para mostrar
}

/**
 * Cierra el modal de edición.
 */
function closeModal() {
    if (editModalInstance) {
        editModalInstance.hide(); // Usar método de Bootstrap para ocultar
    }
}

/**
 * Elimina un registro de producción.
 */
function deleteRegistro(idProduccion) {
    const confirmed = window.prompt(`¿Está seguro de eliminar el registro de producción ID ${idProduccion}? Escriba 'ELIMINAR' para confirmar.`);
    if (confirmed === 'ELIMINAR') {
        historial = historial.filter(r => r.idProduccion !== idProduccion);
        renderHistorial();
        showAlert('✅ Registro eliminado exitosamente', 'success');
    }
}