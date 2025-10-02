# Sistema de Actualización en Tiempo Real para Panadería

## Resumen
Se ha implementado un sistema completo de API REST y Server-Sent Events (SSE) para mantener sincronizados los precios y stock entre el dashboard del administrador y el menú del cliente.

## Archivos Creados/Modificados

### 1. Base de Datos
- **crear_tabla_logs.sql**: Tabla para logging de cambios en productos
- **actualizar_producto_stock_precio.php**: Actualizado para incluir logging

### 2. API REST
- **api_productos_admin.php**: API REST completa para gestión de productos

### 3. Server-Sent Events
- **sse_menu_updates.php**: Endpoint SSE para notificaciones en tiempo real

### 4. JavaScript del Cliente
- **js/menu-updater.js**: Cliente para recibir actualizaciones SSE en el menú
- **js/admin-products.js**: Interfaz de administración de productos

## Pasos de Implementación

### 1. Ejecutar Script de Base de Datos
```sql
-- Ejecutar en MySQL/phpMyAdmin
source crear_tabla_logs.sql;
```

### 2. Incluir JavaScript en las Páginas

#### En el Menú del Cliente (menu.php, dashboard_cliente.php)
```html
<script src="js/menu-updater.js"></script>
```

#### En el Dashboard del Administrador (dashboard_admin.php)
```html
<script src="js/admin-products.js"></script>
```

### 3. Estructura HTML Requerida

#### Para el Menú del Cliente
```html
<div class="producto-card" data-producto-id="1">
    <h3>Nombre del Producto</h3>
    <p class="precio">$5000</p>
    <p class="stock">Stock: 10</p>
</div>
```

#### Para el Dashboard del Admin
```html
<div id="productos-container">
    <!-- Los productos se cargarán dinámicamente -->
</div>
<div id="paginacion-container">
    <!-- La paginación se cargará dinámicamente -->
</div>
```

## Funcionalidades Implementadas

### API REST Endpoints

#### GET `/api_productos_admin.php`
- Obtener todos los productos (con paginación)
- Obtener producto específico: `?id=1`
- Incluir logs: `?id=1&incluir_logs=1`
- Filtros: `?categoria=1&buscar=pan&activos_solo=1`

#### POST `/api_productos_admin.php`
- Crear nuevo producto
```json
{
    "nombre_producto": "Pan Baguette",
    "precio_producto": 2500,
    "stock_min": 20,
    "fecha_vencimiento": "2025-07-10",
    "tipo_producto_marca": "Artesanal",
    "id_categoria_producto": 1
}
```

#### PATCH `/api_productos_admin.php`
- Actualizar precio: `{"id": 1, "campo": "precio", "valor": 3000}`
- Actualizar stock: `{"id": 1, "campo": "stock", "valor": 15}`
- Activar/desactivar: `{"id": 1, "campo": "activo", "valor": 1}`

#### PUT `/api_productos_admin.php`
- Actualizar producto completo

#### DELETE `/api_productos_admin.php`
- Soft delete: `{"id": 1}`
- Hard delete: `{"id": 1, "forzar": true}`

### Server-Sent Events

#### Conexión
```javascript
// Automática al cargar la página
// Manual:
const updater = new MenuUpdater({
    onMenuUpdate: function(data) {
        console.log('Actualización recibida:', data);
    }
});
```

#### Eventos Recibidos
- `connected`: Conexión establecida
- `menu_update`: Cambios en productos detectados
- `heartbeat`: Verificación de conexión (cada 30s)
- `disconnected`: Conexión cerrada
- `error`: Error en el servidor

## Configuración del Servidor

### Requisitos
- PHP 7.4+
- MySQL 5.7+
- Extensiones: mysqli, json

### Configuración Recomendada
```ini
; php.ini
max_execution_time = 300
memory_limit = 256M
```

### Apache/Nginx
Asegurar que SSE no sea bloqueado por proxy/cache:
```apache
# .htaccess
<Files "sse_menu_updates.php">
    Header always set Cache-Control "no-cache, no-store, must-revalidate"
    Header always set Pragma "no-cache"
    Header always set Expires "0"
</Files>
```

## Seguridad

### Validaciones Implementadas
- Verificación de sesión de administrador
- Validación de tipos de datos
- Sanitización de entradas
- Logging de cambios con IP y usuario

### Headers de Seguridad
```php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
```

## Monitoreo y Logs

### Tabla productos_logs
- Registra todos los cambios en productos
- Incluye IP del usuario y timestamp
- Tipos: precio, stock, activacion, desactivacion

### Consultas Útiles
```sql
-- Ver cambios recientes
SELECT * FROM productos_logs 
ORDER BY fecha_cambio DESC 
LIMIT 50;

-- Cambios por producto
SELECT pl.*, p.NOMBRE_PRODUCTO 
FROM productos_logs pl
JOIN Productos p ON pl.producto_id = p.ID_PRODUCTO
WHERE pl.producto_id = 1;
```

## Resolución de Problemas

### SSE No Funciona
1. Verificar que el navegador soporte EventSource
2. Revisar configuración de proxy/firewall
3. Comprobar logs de PHP por errores
4. Verificar permisos de archivo

### API No Responde
1. Verificar sesión de administrador
2. Comprobar conexión a base de datos
3. Revisar logs de errores de PHP
4. Validar formato JSON de las peticiones

### Actualizations No Se Ven
1. Verificar estructura HTML (data-producto-id)
2. Comprobar consola del navegador por errores
3. Verificar que el trigger de base de datos esté activo
4. Comprobar que FECHA_ULTIMA_MODIFICACION se actualice

## Próximas Mejoras

1. **Websockets**: Para comunicación bidireccional
2. **Push Notifications**: Para notificaciones móviles
3. **Cache Redis**: Para mejor rendimiento
4. **Métricas**: Dashboard de analytics de cambios
5. **Backup Automático**: Antes de cambios críticos
