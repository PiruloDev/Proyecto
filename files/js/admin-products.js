/**
 * JavaScript para el Dashboard del Administrador
 * Integración con la API REST de productos
 */

class AdminProductManager {
    constructor() {
        this.apiUrl = 'api_productos_admin.php';
        this.initializeEventListeners();
        this.loadProductos();
    }
    
    /**
     * Inicializar event listeners
     */
    initializeEventListeners() {
        // Formulario de actualización rápida de precios
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-actualizar-precio')) {
                this.mostrarModalActualizarPrecio(e.target.dataset.productoId);
            }
            
            if (e.target.matches('.btn-actualizar-stock')) {
                this.mostrarModalActualizarStock(e.target.dataset.productoId);
            }
            
            if (e.target.matches('.btn-toggle-producto')) {
                this.toggleProducto(e.target.dataset.productoId);
            }
        });
        
        // Formulario de búsqueda
        const searchForm = document.getElementById('form-buscar-productos');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.buscarProductos();
            });
        }
    }
    
    /**
     * Cargar lista de productos
     */
    async loadProductos(page = 1, filtros = {}) {
        try {
            const params = new URLSearchParams({
                page: page,
                limit: 20,
                ...filtros
            });
            
            const response = await fetch(`${this.apiUrl}?${params}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderProductos(data.data);
                this.renderPaginacion(data.pagination);
            } else {
                this.showAlert('Error al cargar productos: ' + data.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error de conexión: ' + error.message, 'error');
        }
    }
    
    /**
     * Actualizar precio de producto
     */
    async actualizarPrecio(productoId, nuevoPrecio) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: productoId,
                    campo: 'precio',
                    valor: nuevoPrecio
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAlert(data.message, 'success');
                this.actualizarProductoEnDOM(productoId, 'precio', data.nuevo_valor);
            } else {
                this.showAlert('Error: ' + data.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error de conexión: ' + error.message, 'error');
        }
    }
    
    /**
     * Actualizar stock de producto
     */
    async actualizarStock(productoId, nuevoStock) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: productoId,
                    campo: 'stock',
                    valor: nuevoStock
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAlert(data.message, 'success');
                this.actualizarProductoEnDOM(productoId, 'stock', data.nuevo_valor);
            } else {
                this.showAlert('Error: ' + data.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error de conexión: ' + error.message, 'error');
        }
    }
    
    /**
     * Toggle estado activo/inactivo del producto
     */
    async toggleProducto(productoId) {
        try {
            const productoElement = document.querySelector(`[data-producto-id="${productoId}"]`);
            const isActive = !productoElement.classList.contains('producto-inactivo');
            
            const response = await fetch(this.apiUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: productoId,
                    campo: 'activo',
                    valor: isActive ? 0 : 1
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAlert(data.message, 'success');
                this.actualizarEstadoProductoEnDOM(productoId, data.nuevo_valor);
            } else {
                this.showAlert('Error: ' + data.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error de conexión: ' + error.message, 'error');
        }
    }
    
    /**
     * Crear nuevo producto
     */
    async crearProducto(datosProducto) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datosProducto)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAlert(data.message, 'success');
                this.loadProductos(); // Recargar lista
            } else {
                this.showAlert('Error: ' + data.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error de conexión: ' + error.message, 'error');
        }
    }
    
    /**
     * Mostrar modal para actualizar precio
     */
    mostrarModalActualizarPrecio(productoId) {
        const modal = this.createModal('Actualizar Precio', `
            <form id="form-actualizar-precio">
                <div class="form-group">
                    <label for="nuevo-precio">Nuevo Precio:</label>
                    <input type="number" step="0.01" min="0.01" id="nuevo-precio" name="nuevo_precio" required class="form-control">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        `);
        
        modal.querySelector('#form-actualizar-precio').addEventListener('submit', (e) => {
            e.preventDefault();
            const nuevoPrecio = parseFloat(e.target.nuevo_precio.value);
            this.actualizarPrecio(productoId, nuevoPrecio);
            modal.remove();
        });
        
        document.body.appendChild(modal);
        modal.querySelector('#nuevo-precio').focus();
    }
    
    /**
     * Mostrar modal para actualizar stock
     */
    mostrarModalActualizarStock(productoId) {
        const modal = this.createModal('Actualizar Stock', `
            <form id="form-actualizar-stock">
                <div class="form-group">
                    <label for="nuevo-stock">Nuevo Stock:</label>
                    <input type="number" min="0" id="nuevo-stock" name="nuevo_stock" required class="form-control">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        `);
        
        modal.querySelector('#form-actualizar-stock').addEventListener('submit', (e) => {
            e.preventDefault();
            const nuevoStock = parseInt(e.target.nuevo_stock.value);
            this.actualizarStock(productoId, nuevoStock);
            modal.remove();
        });
        
        document.body.appendChild(modal);
        modal.querySelector('#nuevo-stock').focus();
    }
    
    /**
     * Crear modal genérico
     */
    createModal(title, content) {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        
        modal.innerHTML = `
            <div class="modal-content" style="
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 400px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
            ">
                <h3 style="margin-top: 0;">${title}</h3>
                ${content}
            </div>
        `;
        
        // Cerrar al hacer clic fuera del modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        return modal;
    }
    
    /**
     * Actualizar producto en el DOM
     */
    actualizarProductoEnDOM(productoId, campo, valor) {
        const elemento = document.querySelector(`[data-producto-id="${productoId}"]`);
        if (elemento) {
            if (campo === 'precio') {
                const precioElement = elemento.querySelector('.precio');
                if (precioElement) {
                    precioElement.textContent = `$${parseFloat(valor).toLocaleString()}`;
                    this.highlightElement(precioElement);
                }
            } else if (campo === 'stock') {
                const stockElement = elemento.querySelector('.stock');
                if (stockElement) {
                    stockElement.textContent = `Stock: ${valor}`;
                    this.highlightElement(stockElement);
                }
            }
        }
    }
    
    /**
     * Actualizar estado del producto en el DOM
     */
    actualizarEstadoProductoEnDOM(productoId, activo) {
        const elemento = document.querySelector(`[data-producto-id="${productoId}"]`);
        if (elemento) {
            if (activo) {
                elemento.classList.remove('producto-inactivo');
                elemento.style.opacity = '1';
            } else {
                elemento.classList.add('producto-inactivo');
                elemento.style.opacity = '0.5';
            }
        }
    }
    
    /**
     * Resaltar elemento
     */
    highlightElement(element) {
        element.style.transition = 'background-color 0.3s ease';
        element.style.backgroundColor = '#28a745';
        element.style.color = 'white';
        
        setTimeout(() => {
            element.style.backgroundColor = '';
            element.style.color = '';
        }, 1500);
    }
    
    /**
     * Renderizar lista de productos
     */
    renderProductos(productos) {
        const container = document.getElementById('productos-container');
        if (!container) return;
        
        container.innerHTML = productos.map(producto => `
            <div class="producto-card ${producto.ACTIVO ? '' : 'producto-inactivo'}" data-producto-id="${producto.ID_PRODUCTO}">
                <h4>${producto.NOMBRE_PRODUCTO}</h4>
                <p class="categoria">${producto.categoria_nombre || 'Sin categoría'}</p>
                <p class="precio">$${parseFloat(producto.PRECIO_PRODUCTO).toLocaleString()}</p>
                <p class="stock">Stock: ${producto.PRODUCTO_STOCK_MIN}</p>
                <div class="acciones">
                    <button class="btn btn-sm btn-primary btn-actualizar-precio" data-producto-id="${producto.ID_PRODUCTO}">
                        Precio
                    </button>
                    <button class="btn btn-sm btn-warning btn-actualizar-stock" data-producto-id="${producto.ID_PRODUCTO}">
                        Stock
                    </button>
                    <button class="btn btn-sm ${producto.ACTIVO ? 'btn-danger' : 'btn-success'} btn-toggle-producto" data-producto-id="${producto.ID_PRODUCTO}">
                        ${producto.ACTIVO ? 'Desactivar' : 'Activar'}
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    /**
     * Renderizar paginación
     */
    renderPaginacion(pagination) {
        const container = document.getElementById('paginacion-container');
        if (!container) return;
        
        let html = '<div class="pagination">';
        
        for (let i = 1; i <= pagination.pages; i++) {
            html += `
                <button class="btn ${i === pagination.page ? 'btn-primary' : 'btn-outline-primary'}" 
                        onclick="adminManager.loadProductos(${i})">
                    ${i}
                </button>
            `;
        }
        
        html += '</div>';
        container.innerHTML = html;
    }
    
    /**
     * Mostrar alerta
     */
    showAlert(message, type = 'info') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10001;
            padding: 12px 16px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            max-width: 400px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        `;
        alert.textContent = message;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('productos-container')) {
        window.adminManager = new AdminProductManager();
    }
});

// CSS básico para las tarjetas de productos
const style = document.createElement('style');
style.textContent = `
    .producto-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
        transition: all 0.3s ease;
    }
    
    .producto-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .producto-inactivo {
        opacity: 0.5;
        filter: grayscale(50%);
    }
    
    .acciones {
        margin-top: 10px;
    }
    
    .acciones .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .pagination {
        text-align: center;
        margin: 20px 0;
    }
    
    .pagination .btn {
        margin: 0 2px;
    }
`;
document.head.appendChild(style);
