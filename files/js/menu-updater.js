/**
 * Cliente JavaScript para Server-Sent Events
 * Escucha actualizaciones en tiempo real del menú
 */

class MenuUpdater {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '';
        this.eventSource = null;
        this.lastCheck = options.lastCheck || Math.floor(Date.now() / 1000);
        this.reconnectInterval = options.reconnectInterval || 5000;
        this.maxReconnectAttempts = options.maxReconnectAttempts || 10;
        this.reconnectAttempts = 0;
        this.isConnected = false;
        
        // Callbacks
        this.onMenuUpdate = options.onMenuUpdate || this.defaultMenuUpdateHandler;
        this.onConnected = options.onConnected || this.defaultConnectedHandler;
        this.onError = options.onError || this.defaultErrorHandler;
        this.onDisconnected = options.onDisconnected || this.defaultDisconnectedHandler;
        
        // Elementos del DOM
        this.notificationContainer = null;
        this.createNotificationContainer();
        
        this.init();
    }
    
    /**
     * Inicializar la conexión SSE
     */
    init() {
        if (typeof(EventSource) === "undefined") {
            console.error("Tu navegador no soporta Server-Sent Events");
            this.showNotification("Tu navegador no soporta actualizaciones en tiempo real", "warning");
            return;
        }
        
        this.connect();
    }
    
    /**
     * Conectar al servidor SSE
     */
    connect() {
        if (this.eventSource) {
            this.disconnect();
        }
        
        const url = `${this.baseUrl}sse_menu_updates.php?last_check=${this.lastCheck}`;
        console.log("Conectando a SSE:", url);
        
        this.eventSource = new EventSource(url);
        
        // Evento de conexión exitosa
        this.eventSource.addEventListener('connected', (event) => {
            this.isConnected = true;
            this.reconnectAttempts = 0;
            const data = JSON.parse(event.data);
            console.log("Conectado a SSE:", data);
            this.onConnected(data);
        });
        
        // Evento de actualización del menú
        this.eventSource.addEventListener('menu_update', (event) => {
            const data = JSON.parse(event.data);
            console.log("Actualización del menú recibida:", data);
            this.lastCheck = data.timestamp;
            this.onMenuUpdate(data);
        });
        
        // Evento heartbeat
        this.eventSource.addEventListener('heartbeat', (event) => {
            const data = JSON.parse(event.data);
            console.log("Heartbeat recibido:", data.timestamp);
        });
        
        // Evento de desconexión
        this.eventSource.addEventListener('disconnected', (event) => {
            const data = JSON.parse(event.data);
            console.log("Desconectado del SSE:", data);
            this.onDisconnected(data);
        });
        
        // Evento de error
        this.eventSource.addEventListener('error', (event) => {
            let errorData = { message: "Error de conexión" };
            try {
                errorData = JSON.parse(event.data);
            } catch (e) {
                // Si no se puede parsear, usar mensaje por defecto
            }
            console.error("Error en SSE:", errorData);
            this.onError(errorData);
        });
        
        // Manejar errores de conexión
        this.eventSource.onerror = (event) => {
            console.error("Error en EventSource:", event);
            this.isConnected = false;
            
            if (this.eventSource.readyState === EventSource.CLOSED) {
                this.attemptReconnect();
            }
        };
    }
    
    /**
     * Desconectar del servidor SSE
     */
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
            this.isConnected = false;
        }
    }
    
    /**
     * Intentar reconexión automática
     */
    attemptReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error("Máximo número de intentos de reconexión alcanzado");
            this.showNotification("No se pudo conectar para actualizaciones en tiempo real", "error");
            return;
        }
        
        this.reconnectAttempts++;
        console.log(`Intento de reconexión ${this.reconnectAttempts}/${this.maxReconnectAttempts} en ${this.reconnectInterval}ms`);
        
        setTimeout(() => {
            this.connect();
        }, this.reconnectInterval);
    }
    
    /**
     * Handler por defecto para actualizaciones del menú
     */
    defaultMenuUpdateHandler(data) {
        this.showNotification(`Menú actualizado: ${data.total_cambios} cambios detectados`, "info");
        
        // Actualizar productos en el DOM si están presentes
        this.updateProductsInDOM(data.productos);
        
        // Mostrar notificación detallada
        this.showDetailedUpdateNotification(data);
    }
    
    /**
     * Handler por defecto para conexión exitosa
     */
    defaultConnectedHandler(data) {
        this.showNotification("Conectado para actualizaciones en tiempo real", "success");
    }
    
    /**
     * Handler por defecto para errores
     */
    defaultErrorHandler(data) {
        this.showNotification(`Error: ${data.message}`, "error");
    }
    
    /**
     * Handler por defecto para desconexión
     */
    defaultDisconnectedHandler(data) {
        this.showNotification("Desconectado de actualizaciones en tiempo real", "warning");
    }
    
    /**
     * Actualizar productos en el DOM
     */
    updateProductsInDOM(productos) {
        productos.forEach(producto => {
            // Buscar elementos del producto en el DOM por ID
            const precioElement = document.querySelector(`[data-producto-id="${producto.ID_PRODUCTO}"] .precio`);
            const stockElement = document.querySelector(`[data-producto-id="${producto.ID_PRODUCTO}"] .stock`);
            const productoElement = document.querySelector(`[data-producto-id="${producto.ID_PRODUCTO}"]`);
            
            if (precioElement) {
                precioElement.textContent = `$${parseFloat(producto.PRECIO_PRODUCTO).toLocaleString()}`;
                this.highlightElement(precioElement);
            }
            
            if (stockElement) {
                stockElement.textContent = `Stock: ${producto.PRODUCTO_STOCK_MIN}`;
                this.highlightElement(stockElement);
            }
            
            // Manejar productos desactivados
            if (productoElement) {
                if (producto.ACTIVO == 0) {
                    productoElement.style.opacity = '0.5';
                    productoElement.style.filter = 'grayscale(50%)';
                } else {
                    productoElement.style.opacity = '1';
                    productoElement.style.filter = 'none';
                }
            }
        });
    }
    
    /**
     * Resaltar elemento actualizado
     */
    highlightElement(element) {
        element.style.transition = 'background-color 0.3s ease';
        element.style.backgroundColor = '#ffeb3b';
        
        setTimeout(() => {
            element.style.backgroundColor = '';
        }, 2000);
    }
    
    /**
     * Mostrar notificación detallada de actualización
     */
    showDetailedUpdateNotification(data) {
        if (data.logs && data.logs.length > 0) {
            const cambios = data.logs.map(log => {
                let mensaje = `${log.NOMBRE_PRODUCTO}: `;
                switch (log.tipo_cambio) {
                    case 'precio':
                        mensaje += `Precio cambiado de $${log.valor_anterior} a $${log.valor_nuevo}`;
                        break;
                    case 'stock':
                        mensaje += `Stock cambiado de ${log.valor_anterior} a ${log.valor_nuevo}`;
                        break;
                    case 'activacion':
                        mensaje += 'Producto activado';
                        break;
                    case 'desactivacion':
                        mensaje += 'Producto desactivado';
                        break;
                    default:
                        mensaje += `${log.tipo_cambio}: ${log.valor_anterior} → ${log.valor_nuevo}`;
                }
                return mensaje;
            });
            
            this.showNotification(
                `Cambios recientes:\n${cambios.slice(0, 3).join('\n')}${cambios.length > 3 ? '\n...' : ''}`,
                "info",
                5000
            );
        }
    }
    
    /**
     * Crear contenedor de notificaciones
     */
    createNotificationContainer() {
        if (document.getElementById('sse-notifications')) {
            return;
        }
        
        const container = document.createElement('div');
        container.id = 'sse-notifications';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
        `;
        document.body.appendChild(container);
        this.notificationContainer = container;
    }
    
    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 12px 16px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            white-space: pre-line;
            animation: slideIn 0.3s ease-out;
            cursor: pointer;
        `;
        
        notification.textContent = message;
        
        // Agregar animación CSS
        if (!document.getElementById('sse-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'sse-notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Hacer clic para cerrar
        notification.addEventListener('click', () => {
            this.removeNotification(notification);
        });
        
        this.notificationContainer.appendChild(notification);
        
        // Auto-remover después del tiempo especificado
        if (duration > 0) {
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
        }
    }
    
    /**
     * Remover notificación con animación
     */
    removeNotification(notification) {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
    
    /**
     * Obtener color según tipo de notificación
     */
    getNotificationColor(type) {
        const colors = {
            success: '#4caf50',
            error: '#f44336',
            warning: '#ff9800',
            info: '#2196f3'
        };
        return colors[type] || colors.info;
    }
    
    /**
     * Obtener estado de conexión
     */
    isConnectedToSSE() {
        return this.isConnected && this.eventSource && this.eventSource.readyState === EventSource.OPEN;
    }
    
    /**
     * Destructor
     */
    destroy() {
        this.disconnect();
        if (this.notificationContainer && this.notificationContainer.parentNode) {
            this.notificationContainer.parentNode.removeChild(this.notificationContainer);
        }
    }
}

// Inicializar automáticamente si se está en una página de menú
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en una página que necesita actualizaciones de menú
    const menuElements = document.querySelectorAll('[data-producto-id], .producto-card, .menu-item');
    
    if (menuElements.length > 0) {
        console.log("Página de menú detectada, iniciando MenuUpdater...");
        
        window.menuUpdater = new MenuUpdater({
            onMenuUpdate: function(data) {
                console.log("Actualización personalizada del menú:", data);
                
                // Llamar al handler por defecto
                this.defaultMenuUpdateHandler(data);
                
                // Código personalizado adicional aquí
                // Por ejemplo, recargar carrito, actualizar totales, etc.
                if (typeof updateCartTotals === 'function') {
                    updateCartTotals();
                }
            }.bind(window.menuUpdater)
        });
        
        // Agregar evento para limpiar al salir de la página
        window.addEventListener('beforeunload', function() {
            if (window.menuUpdater) {
                window.menuUpdater.destroy();
            }
        });
    }
});

// Exportar para uso manual
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MenuUpdater;
}
