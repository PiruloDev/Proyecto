<?php
include 'conexion.php';

// Verificar conexión
if (!$conexion) {
    die('<div class="mensaje-error">❌ Error de conexión a la base de datos</div>');
}

// Consultar todos los productos para mostrarlos en la tabla
$consulta = "SELECT 
    ID_PRODUCTO, 
    NOMBRE_PRODUCTO, 
    PRECIO_PRODUCTO, 
    PRODUCTO_STOCK_MIN, 
    TIPO_PRODUCTO_MARCA, 
    FECHA_VENCIMIENTO_PRODUCTO,
    COALESCE(ACTIVO, 1) as ACTIVO
    FROM Productos 
    ORDER BY ID_PRODUCTO DESC";

$resultado = $conexion->query($consulta);

if (!$resultado) {
    die('<div class="mensaje-error">❌ Error en la consulta: ' . $conexion->error . '</div>');
}
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Panadería</title>
    <link rel="stylesheet" href="styleproductostabla.css">
</head>
<body>
    <div class="container">
        <div class="header-section">
            <a href="dashboard_admin.php" class="btn-regresar">
                <span class="material-symbols-outlined">arrow_back</span>
                Regresar
            </a>
            <h1>Gestión de Productos</h1>
        </div>
        
        <?php
        // Mostrar mensajes de éxito o error para el CRUD de productos
        if (isset($_GET['success'])) {
            $success = $_GET['success'];
            if ($success == 'producto_agregado') {
                echo '<div class="mensaje-exito">✅ Producto agregado exitosamente</div>';
            }
        }
        
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            $mensaje_error = '';
            switch ($error) {
                case 'datos_incompletos':
                    $mensaje_error = 'Por favor complete todos los campos obligatorios.';
                    break;
                case 'error_bd':
                    $mensaje_error = 'Error en la base de datos. Intente nuevamente.';
                    break;
                default:
                    $mensaje_error = 'Error desconocido.';
            }
            echo '<div class="mensaje-error">❌ ' . $mensaje_error . '</div>';
        }
        ?>
        
        <div class="formulario-container">
            <h2>Añadir Nuevo Producto</h2>
            <button id="btn-abrir-modal" class="btn-modal-trigger">
                <span class="material-symbols-outlined">add_circle</span>
                Agregar Nuevo Producto
            </button>
        </div>

        <div class="tabla-section">
            <div class="tabla-header">
                <h2>Inventario Actual</h2>
                <div class="filtro-busqueda">
                    <span class="material-symbols-outlined">
                        search
                    </span>
                    <input type="text" id="filtro-productos" placeholder="Buscar productos...">
                </div>
            </div>
            
            <div class="tabla-contenedor">
                <table id="tabla-productos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Marca/Tipo</th>
                <th>Fecha Vencimiento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['ID_PRODUCTO']); ?></td>
                        <td><?php echo htmlspecialchars($fila['NOMBRE_PRODUCTO']); ?></td>
                        <td class="precio">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span id="precio-tabla-<?php echo $fila['ID_PRODUCTO']; ?>" style="font-weight: 600;">
                                    $<?php echo number_format($fila['PRECIO_PRODUCTO'], 0, ',', '.'); ?>
                                </span>
                                <button onclick="editarPrecioTabla(<?php echo $fila['ID_PRODUCTO']; ?>, <?php echo $fila['PRECIO_PRODUCTO']; ?>)" 
                                        class="btn-edit-precio"
                                        title="Editar precio"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 4px 6px; border-radius: 15px; cursor: pointer; display: flex; align-items: center; font-size: 0.7em; transition: all 0.3s ease;"
                                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)';"
                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">edit</span>
                                </button>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($fila['PRODUCTO_STOCK_MIN']); ?></td>
                        <td><?php echo htmlspecialchars($fila['TIPO_PRODUCTO_MARCA']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fila['FECHA_VENCIMIENTO_PRODUCTO'])); ?></td>
                        <td>
                            <?php if (isset($fila['ACTIVO']) && $fila['ACTIVO'] == 1): ?>
                                <span class="estado-activo">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="estado-inactivo">
                                    <span class="material-symbols-outlined">cancel</span>
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="acciones">
                            <button onclick="toggleProducto(<?php echo $fila['ID_PRODUCTO']; ?>, <?php echo isset($fila['ACTIVO']) ? $fila['ACTIVO'] : 1; ?>, '<?php echo addslashes($fila['NOMBRE_PRODUCTO']); ?>')"
                            class="btn-action btn-toggle"
                            title="<?php echo (isset($fila['ACTIVO']) && $fila['ACTIVO'] == 1) ? 'Desactivar' : 'Activar'; ?>">
                            <span class="material-symbols-outlined">
                                <?php echo (isset($fila['ACTIVO']) && $fila['ACTIVO'] == 1) ? 'pause' : 'play_arrow'; ?>
                            </span>
                            </button>
                            <button onclick="abrirModalGestion(<?php echo $fila['ID_PRODUCTO']; ?>)"
                            class="btn-action btn-gestionar"
                            title="Gestionar">
                            <span class="material-symbols-outlined">
                                visibility
                            </span>
                            </button>
                            <button onclick="eliminarProducto(<?php echo $fila['ID_PRODUCTO']; ?>, '<?php echo addslashes($fila['NOMBRE_PRODUCTO']); ?>')"
                            class="btn-action btn-eliminar"
                            title="Eliminar">
                            <span class="material-symbols-outlined">
                                delete
                            </span>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        No hay productos registrados en el inventario.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
        </div>
    </div>

    <!-- Modal para gestionar producto -->
    <div id="modal-gestion" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2 id="gestion-titulo">Gestionar Producto</h2>
                <button class="modal-close" onclick="cerrarModalGestion()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="gestion-contenido">
                    <!-- El contenido se cargará dinámicamente -->
                    <div style="text-align: center; padding: 40px;">
                        <span class="material-symbols-outlined" style="font-size: 48px; color: #667eea;">hourglass_empty</span>
                        <p>Cargando información del producto...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar producto -->
    <div id="modal-agregar" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Producto</h2>
                <button class="modal-close" onclick="cerrarModalAgregar()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-agregar-producto" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre del Producto:</label>
                            <input type="text" id="nombre" name="nombre_producto" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label for="precio">Precio:</label>
                            <input type="number" step="0.01" id="precio" name="precio_producto" required min="0.01" max="99999.99">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock_min">Stock:</label>
                            <input type="number" id="stock_min" name="stock_min" required min="0" max="9999">
                        </div>
                        <div class="form-group">
                            <label for="marca_tipo">Categoría:</label>
                            <input type="text" id="marca_tipo" name="tipo_producto_marca" required maxlength="100">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" required 
                            min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                        <div class="form-group">
                            <!-- Espacio para mantener el layout -->
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="modal-btn secondary" onclick="cerrarModalAgregar()">Cancelar</button>
                        <button type="submit" class="modal-btn primary">
                            <span class="material-symbols-outlined">add_circle</span>
                            Agregar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales para acciones -->
    <div id="modal-resultado" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-titulo">Resultado</h2>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modal-icono" class="modal-icon">✓</div>
                <div id="modal-mensaje" class="modal-message"></div>
                <div id="modal-acciones" class="modal-actions">
                    <button class="modal-btn primary" onclick="cerrarModal()">Aceptar</button>
                </div>
                <div id="modal-countdown" class="countdown-info" style="display: none;">
                    ⏱️ Esta ventana se cerrará automáticamente en <span id="countdown-segundos">5</span> segundos.
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales para el modal
        let modalCountdown;
        let modalCountdownTime = 5;

        // Función para mostrar modal
        function mostrarModal(tipo, titulo, mensaje, acciones = null, autoClose = true) {
            const modal = document.getElementById('modal-resultado');
            const modalTitulo = document.getElementById('modal-titulo');
            const modalIcono = document.getElementById('modal-icono');
            const modalMensaje = document.getElementById('modal-mensaje');
            const modalAcciones = document.getElementById('modal-acciones');
            const modalCountdownDiv = document.getElementById('modal-countdown');
            
            // Configurar contenido
            modalTitulo.textContent = titulo;
            modalMensaje.innerHTML = mensaje;
            
            // Configurar icono según tipo
            modalIcono.className = `modal-icon ${tipo}`;
            switch(tipo) {
                case 'success':
                    modalIcono.innerHTML = '<span class="material-symbols-outlined">check_circle</span>';
                    break;
                case 'error':
                    modalIcono.innerHTML = '<span class="material-symbols-outlined">error</span>';
                    break;
                case 'warning':
                    modalIcono.innerHTML = '<span class="material-symbols-outlined">warning</span>';
                    break;
            }
            
            // Configurar acciones
            if (acciones) {
                modalAcciones.innerHTML = acciones;
            } else {
                modalAcciones.innerHTML = '<button class="modal-btn primary" onclick="cerrarModal()">Aceptar</button>';
            }
            
            // Mostrar modal
            modal.style.display = 'block';
            
            // Configurar auto-close
            if (autoClose) {
                modalCountdownDiv.style.display = 'block';
                iniciarCountdownModal();
            } else {
                modalCountdownDiv.style.display = 'none';
            }
        }

        // Función para cerrar modal
        function cerrarModal() {
            const modal = document.getElementById('modal-resultado');
            modal.style.display = 'none';
            
            if (modalCountdown) {
                clearInterval(modalCountdown);
            }
            
            // Recargar la página para actualizar la tabla
            location.reload();
        }

        // Función para iniciar countdown del modal
        function iniciarCountdownModal() {
            const countdownElement = document.getElementById('countdown-segundos');
            modalCountdownTime = 5;
            
            modalCountdown = setInterval(function() {
                modalCountdownTime--;
                countdownElement.textContent = modalCountdownTime;
                
                if (modalCountdownTime <= 0) {
                    clearInterval(modalCountdown);
                    cerrarModal();
                }
            }, 1000);
        }

        // Función para toggle de producto
        function toggleProducto(id, estado, nombre) {
            const accion = estado == 1 ? 'desactivar' : 'activar';
            const confirmacion = confirm(`¿Está seguro que desea ${accion} el producto "${nombre}"?`);
            
            if (confirmacion) {
                // Realizar petición AJAX con POST
                fetch('toggle_producto_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&estado=${estado}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarModal('success', 'Producto Actualizado', data.mensaje);
                        } else {
                            mostrarModal('error', 'Error', data.mensaje);
                        }
                    })
                    .catch(error => {
                        mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
                    });
            }
        }

        // Función para eliminar producto
        function eliminarProducto(id, nombre) {
            // Realizar petición AJAX para verificar referencias con POST
            fetch('eliminar_producto_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.hasReferences) {
                        const acciones = `
                            <button class="modal-btn secondary" onclick="cerrarModal()">Cancelar</button>
                            <button class="modal-btn danger" onclick="eliminarForzado(${id}, '${nombre}')">Eliminar Forzado</button>
                        `;
                        mostrarModal('warning', 'Producto con Referencias', data.mensaje, acciones, false);
                    } else {
                        if (data.success) {
                            mostrarModal('success', 'Producto Eliminado', data.mensaje);
                        } else {
                            mostrarModal('error', 'Error', data.mensaje);
                        }
                    }
                })
                .catch(error => {
                    mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
                });
        }

        // Función para eliminación forzada
        function eliminarForzado(id, nombre) {
            const confirmacion = confirm(`⚠️ ELIMINACIÓN FORZADA ⚠️\n\nEsto eliminará PERMANENTEMENTE el producto: ${nombre}\n\n🚨 ADVERTENCIA: Si este producto está en pedidos, se ROMPERÁN las referencias de la base de datos.\n\n❌ Esta acción NO se puede deshacer.\n\n¿Estás ABSOLUTAMENTE SEGURO de continuar?`);
            
            if (confirmacion) {
                fetch('eliminar_forzado_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarModal('success', 'Producto Eliminado', data.mensaje);
                        } else {
                            mostrarModal('error', 'Error', data.mensaje);
                        }
                    })
                    .catch(error => {
                        mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
                    });
            }
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modal-resultado');
            const modalAgregar = document.getElementById('modal-agregar');
            const modalGestion = document.getElementById('modal-gestion');
            
            if (event.target == modal) {
                cerrarModal();
            }
            
            if (event.target == modalAgregar) {
                cerrarModalAgregar();
            }
            
            if (event.target == modalGestion) {
                cerrarModalGestion();
            }
        }

        // Funciones para el modal de agregar
        function abrirModalAgregar() {
            document.getElementById('modal-agregar').style.display = 'block';
        }

        function cerrarModalAgregar() {
            document.getElementById('modal-agregar').style.display = 'none';
            // Limpiar formulario
            document.getElementById('form-agregar-producto').reset();
        }

        // Configurar botón para abrir modal
        document.getElementById('btn-abrir-modal').addEventListener('click', abrirModalAgregar);

        // Funciones para el modal de gestión
        function abrirModalGestion(idProducto) {
            const modal = document.getElementById('modal-gestion');
            const contenido = document.getElementById('gestion-contenido');
            const titulo = document.getElementById('gestion-titulo');
            
            // Mostrar modal con loading
            modal.style.display = 'block';
            contenido.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <span class="material-symbols-outlined" style="font-size: 48px; color: #667eea;">hourglass_empty</span>
                    <p>Cargando información del producto...</p>
                </div>
            `;
            
            // Cargar información del producto via AJAX
            fetch('obtener_producto_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${idProducto}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    titulo.textContent = data.producto.nombre;
                    contenido.innerHTML = generarContenidoGestion(data.producto, data.referencias);
                } else {
                    contenido.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <span class="material-symbols-outlined" style="font-size: 48px; color: #dc3545;">error</span>
                            <p style="color: #dc3545;">Error al cargar el producto: ${data.mensaje}</p>
                            <button class="modal-btn secondary" onclick="cerrarModalGestion()">Cerrar</button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                contenido.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <span class="material-symbols-outlined" style="font-size: 48px; color: #dc3545;">wifi_off</span>
                        <p style="color: #dc3545;">Error de conexión. Intente nuevamente.</p>
                        <button class="modal-btn secondary" onclick="cerrarModalGestion()">Cerrar</button>
                    </div>
                `;
            });
        }

        function cerrarModalGestion() {
            document.getElementById('modal-gestion').style.display = 'none';
        }

        function generarContenidoGestion(producto, referencias) {
            const estadoBadge = producto.activo == 1 
                ? '<span class="status-badge status-activo"><span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span> Activo</span>'
                : '<span class="status-badge status-inactivo"><span class="material-symbols-outlined" style="font-size: 16px;">cancel</span> Inactivo</span>';
            
            let contenido = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 30px;">
                    <div style="background:rgb(255, 255, 255); border-radius: 12px; padding: 25px; border-left: 4px solidrgb(235, 100, 22);">
                        <h3 style="color: #333; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <span class="material-symbols-outlined">info</span>
                            Información General
                        </h3>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee;">
                            <span style="font-weight: 600; color: #555;">Precio:</span>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span id="precio-display-${producto.id}" style="font-size: 1.2em; font-weight: 700; color: #28a745;">$${formatearPrecio(producto.precio)}</span>
                                <button onclick="editarPrecio(${producto.id}, ${producto.precio})" 
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 6px 8px; border-radius: 20px; cursor: pointer; display: flex; align-items: center; font-size: 0.8em; transition: all 0.3s ease;"
                                        title="Editar precio"
                                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';"
                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                            <span style="font-weight: 600; color: #555;">Stock:</span>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span id="stock-display-${producto.id}" style="font-size: 1.2em; font-weight: 700; color: #17a2b8;">${producto.stock} unidades</span>
                                <button onclick="editarStock(${producto.id}, ${producto.stock})" 
                                        style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 8px; border-radius: 20px; cursor: pointer; display: flex; align-items: center; font-size: 0.8em; transition: all 0.3s ease;"
                                        title="Editar stock"
                                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(23, 162, 184, 0.3)';"
                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                            <span style="font-weight: 600; color: #555;">Categoría:</span>
                            <span>${producto.categoria}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                            <span style="font-weight: 600; color: #555;">Estado:</span>
                            ${estadoBadge}
                        </div>
                    </div>
                    
                    <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; border-left: 4px solidrgb(255, 255, 255);">
                        <h3 style="color: black; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <span class="material-symbols-outlined">schedule</span>
                            Fechas y Referencias
                        </h3>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                            <span style="font-weight: 600; color: #555;">Fecha de Vencimiento:</span>
                            <span>${producto.fecha_vencimiento}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                            <span style="font-weight: 600; color: #555;">Referencias en Pedidos:</span>
                            <span style="color: ${referencias > 0 ? '#e74c3c' : '#27ae60'}; font-weight: 600;">
                                ${referencias > 0 ? referencias + ' pedido(s)' : 'Sin referencias'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
            // Botones de acción
            contenido += `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 30px;">
            `;

            // Solo mostrar eliminar si no hay referencias
            if (referencias == 0) {
                contenido += `
                    <div style="text-align: center;">
                        <button onclick="eliminarProductoGestion(${producto.id}, '${producto.nombre.replace(/'/g, "\\'")}')" 
                                style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border: none; padding: 12px 20px; border-radius: 25px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; width: 100%; justify-content: center;">
                            <span class="material-symbols-outlined">delete</span>
                            Eliminar Producto
                        </button>
                    </div>
                `;
            }

            contenido += `
                    <div style="text-align: center;">
                        <button onclick="verHistorialProducto(${producto.id})" 
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px 20px; border-radius: 25px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; width: 100%; justify-content: center;">
                            <span class="material-symbols-outlined">visibility</span>
                            Ver Historial
                        </button>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    <button onclick="cerrarModalGestion()" 
                            style="background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%); color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Volver a la Lista
                    </button>
                </div>
            `;

            return contenido;
        }

        function eliminarProductoGestion(id, nombre) {
            // Cerrar modal de gestión primero
            cerrarModalGestion();
            
            // Usar la función de eliminación existente
            eliminarProducto(id, nombre);
        }

        function verHistorialProducto(id) {
            // Cerrar modal de gestión y mostrar mensaje
            cerrarModalGestion();
            mostrarModal('info', 'Historial de Producto', 
                'La funcionalidad de historial se abrirá próximamente en una nueva ventana.', 
                '<button class="modal-btn primary" onclick="cerrarModal()">Entendido</button>', 
                false
            );
        }

        // Función para agregar producto
        function agregarProducto(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const submitButton = event.target.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Mostrar indicador de carga
            submitButton.innerHTML = '<span class="material-symbols-outlined">hourglass_empty</span>Procesando...';
            submitButton.disabled = true;
            
            fetch('agregar_producto_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botón
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                // Cerrar modal de agregar
                cerrarModalAgregar();
                
                if (data.success) {
                    // Mostrar detalles del producto agregado
                    const detallesHtml = `
                        <div style="background-color: var(--light-gray); padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left;">
                            <h4 style="color: var(--primary-color); margin-bottom: 15px; text-align: center;">Detalles del Producto Agregado:</h4>
                            <p><strong>ID:</strong> ${data.detalles.id}</p>
                            <p><strong>Nombre:</strong> ${data.detalles.nombre}</p>
                            <p><strong>Precio:</strong> $${data.detalles.precio}</p>
                            <p><strong>Stock:</strong> ${data.detalles.stock}</p>
                            <p><strong>Categoría:</strong> ${data.detalles.categoria}</p>
                            <p><strong>Fecha de Vencimiento:</strong> ${data.detalles.fecha_vencimiento}</p>
                            <p><strong>Fecha de Ingreso:</strong> ${data.detalles.fecha_ingreso}</p>
                        </div>
                    `;
                    
                    const acciones = `
                        <button class="modal-btn success" onclick="abrirModalAgregar(); cerrarModal();">Agregar Otro</button>
                        <button class="modal-btn primary" onclick="cerrarModal()">Aceptar</button>
                    `;
                    
                    mostrarModal('success', 'Producto Agregado Exitosamente', data.mensaje + detallesHtml, acciones);
                } else {
                    mostrarModal('error', 'Error al Agregar Producto', data.mensaje);
                }
            })
            .catch(error => {
                // Restaurar botón
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
            });
        }

        // Configurar formulario de agregar producto
        document.getElementById('form-agregar-producto').addEventListener('submit', agregarProducto);

        // Atajos de teclado
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                // Cerrar modal de resultado
                const modalResultado = document.getElementById('modal-resultado');
                if (modalResultado.style.display === 'block') {
                    cerrarModal();
                }
                
                // Cerrar modal de agregar
                const modalAgregar = document.getElementById('modal-agregar');
                if (modalAgregar.style.display === 'block') {
                    cerrarModalAgregar();
                }
                
                // Cerrar modal de gestión
                const modalGestion = document.getElementById('modal-gestion');
                if (modalGestion.style.display === 'block') {
                    cerrarModalGestion();
                }
            }
        });

        // Filtro de búsqueda en tiempo real
        document.getElementById('filtro-productos').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-productos');
            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < filas.length; i++) {
                const fila = filas[i];
                const celdas = fila.getElementsByTagName('td');
                let mostrar = false;
                
                // Si no hay filtro, mostrar todas las filas
                if (filtro === '') {
                    mostrar = true;
                } else {
                    // Buscar en nombre, precio, marca/tipo
                    for (let j = 1; j <= 4; j++) { // columnas 1-4 (nombre, precio, stock, marca)
                        if (celdas[j] && celdas[j].textContent.toLowerCase().includes(filtro)) {
                            mostrar = true;
                            break;
                        }
                    }
                }
                
                fila.style.display = mostrar ? '' : 'none';
            }
            
            // Mostrar mensaje si no hay resultados
            const filasVisibles = Array.from(filas).filter(fila => fila.style.display !== 'none');
            const mensajeNoResultados = document.getElementById('mensaje-no-resultados');
            
            if (filasVisibles.length === 0 && filtro !== '') {
                if (!mensajeNoResultados) {
                    const mensaje = document.createElement('tr');
                    mensaje.id = 'mensaje-no-resultados';
                    mensaje.innerHTML = '<td colspan="8" class="no-resultados">No se encontraron productos que coincidan con la búsqueda</td>';
                    tabla.getElementsByTagName('tbody')[0].appendChild(mensaje);
                }
            } else if (mensajeNoResultados) {
                mensajeNoResultados.remove();
            }
        });
        
        // Función para editar precio
        function editarPrecio(id, precioActual) {
            const precioDisplay = document.getElementById(`precio-display-${id}`);
            
            // Crear input para editar
            const inputContainer = document.createElement('div');
            inputContainer.style.cssText = 'display: flex; align-items: center; gap: 5px;';
            
            const input = document.createElement('input');
            input.type = 'number';
            input.step = '0.01';
            input.min = '0.01';
            input.max = '99999.99';
            input.value = precioActual;
            input.style.cssText = 'width: 100px; padding: 4px 8px; border: 2px solid #667eea; border-radius: 8px; font-size: 1.1em; font-weight: 700; color: #28a745; text-align: center;';
            
            const btnGuardar = document.createElement('button');
            btnGuardar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 16px;">check</span>';
            btnGuardar.style.cssText = 'background: #28a745; color: white; border: none; padding: 4px 6px; border-radius: 50%; cursor: pointer; display: flex; align-items: center;';
            btnGuardar.title = 'Guardar precio';
            
            const btnCancelar = document.createElement('button');
            btnCancelar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 16px;">close</span>';
            btnCancelar.style.cssText = 'background: #dc3545; color: white; border: none; padding: 4px 6px; border-radius: 50%; cursor: pointer; display: flex; align-items: center;';
            btnCancelar.title = 'Cancelar edición';
            
            inputContainer.appendChild(input);
            inputContainer.appendChild(btnGuardar);
            inputContainer.appendChild(btnCancelar);
            
            // Reemplazar el display del precio
            const parentDiv = precioDisplay.parentElement;
            parentDiv.innerHTML = '';
            parentDiv.appendChild(inputContainer);
            
            // Enfocar el input
            input.focus();
            input.select();
            
            // Función para guardar
            btnGuardar.onclick = function() {
                const nuevoPrecio = parseFloat(input.value);
                if (nuevoPrecio <= 0 || nuevoPrecio > 99999.99) {
                    mostrarModal('error', 'Error', 'El precio debe estar entre $0.01 y $99,999.99');
                    return;
                }
                
                actualizarPrecio(id, nuevoPrecio, precioActual);
            };
            
            // Función para cancelar
            btnCancelar.onclick = function() {
                restaurarDisplayPrecio(id, precioActual);
            };
            
            // Guardar con Enter
            input.onkeypress = function(e) {
                if (e.key === 'Enter') {
                    btnGuardar.click();
                }
            };
            
            // Cancelar con Escape
            input.onkeydown = function(e) {
                if (e.key === 'Escape') {
                    btnCancelar.click();
                }
            };
        }
        
        // Función para actualizar precio en la base de datos
        function actualizarPrecio(id, nuevoPrecio, precioAnterior) {
            // Mostrar indicador de carga
            const parentDiv = document.getElementById(`precio-display-${id}`).parentElement;
            parentDiv.innerHTML = '<span style="color: #667eea;"><span class="material-symbols-outlined">hourglass_empty</span> Actualizando...</span>';
            
            fetch('actualizar_producto_stock_precio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `producto_id=${id}&accion=actualizar_precio&nuevo_valor=${nuevoPrecio}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar display del precio
                    restaurarDisplayPrecio(id, nuevoPrecio);
                    
                    // Mostrar notificación de éxito
                    mostrarModal('success', 'Precio Actualizado', 
                        `El precio del producto se ha actualizado correctamente:<br><br>
                        <strong>Precio anterior:</strong> $${precioAnterior.toFixed(2)}<br>
                        <strong>Precio nuevo:</strong> $${nuevoPrecio.toFixed(2)}`
                    );
                    
                    // Actualizar también en la tabla si está visible
                    const filaTabla = document.querySelector(`tr[data-id="${id}"]`);
                    if (filaTabla) {
                        const celdaPrecio = filaTabla.cells[2]; // columna precio
                        celdaPrecio.textContent = `$${nuevoPrecio.toFixed(2)}`;
                    }
                } else {
                    // Error al actualizar
                    restaurarDisplayPrecio(id, precioAnterior);
                    mostrarModal('error', 'Error', `No se pudo actualizar el precio: ${data.message}`);
                }
            })
            .catch(error => {
                restaurarDisplayPrecio(id, precioAnterior);
                mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
            });
        }
        
        // Función para restaurar el display del precio
        function restaurarDisplayPrecio(id, precio) {
            const parentDiv = document.getElementById(`precio-display-${id}`).parentElement;
            parentDiv.innerHTML = `
                <span id="precio-display-${id}" style="font-size: 1.2em; font-weight: 700; color: #28a745;">$${formatearPrecio(precio)}</span>
                <button onclick="editarPrecio(${id}, ${precio})" 
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 6px 8px; border-radius: 20px; cursor: pointer; display: flex; align-items: center; font-size: 0.8em; transition: all 0.3s ease;"
                        title="Editar precio"
                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';"
                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                </button>
            `;
        }

        // Función para editar precio desde la tabla
        function editarPrecioTabla(id, precioActual) {
            const precioDisplay = document.getElementById(`precio-tabla-${id}`);
            const parentDiv = precioDisplay.parentElement;
            
            // Crear input para editar
            const inputContainer = document.createElement('div');
            inputContainer.style.cssText = 'display: flex; align-items: center; gap: 5px;';
            
            const input = document.createElement('input');
            input.type = 'number';
            input.step = '0.01';
            input.min = '0.01';
            input.max = '99999.99';
            input.value = precioActual;
            input.style.cssText = 'width: 90px; padding: 3px 6px; border: 2px solid #667eea; border-radius: 6px; font-size: 0.9em; font-weight: 600; text-align: center;';
            
            const btnGuardar = document.createElement('button');
            btnGuardar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 14px;">check</span>';
            btnGuardar.style.cssText = 'background: #28a745; color: white; border: none; padding: 3px 5px; border-radius: 50%; cursor: pointer; display: flex; align-items: center;';
            btnGuardar.title = 'Guardar precio';
            
            const btnCancelar = document.createElement('button');
            btnCancelar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 14px;">close</span>';
            btnCancelar.style.cssText = 'background: #dc3545; color: white; border: none; padding: 3px 5px; border-radius: 50%; cursor: pointer; display: flex; align-items: center;';
            btnCancelar.title = 'Cancelar edición';
            
            inputContainer.appendChild(input);
            inputContainer.appendChild(btnGuardar);
            inputContainer.appendChild(btnCancelar);
            
            // Reemplazar el contenido del div padre
            parentDiv.innerHTML = '';
            parentDiv.appendChild(inputContainer);
            
            // Enfocar el input
            input.focus();
            input.select();
            
            // Función para guardar
            btnGuardar.onclick = function() {
                const nuevoPrecio = parseFloat(input.value);
                if (nuevoPrecio <= 0 || nuevoPrecio > 99999.99) {
                    mostrarModal('error', 'Error', 'El precio debe estar entre $0.01 y $99,999.99');
                    return;
                }
                
                actualizarPrecioTabla(id, nuevoPrecio, precioActual);
            };
            
            // Función para cancelar
            btnCancelar.onclick = function() {
                restaurarDisplayPrecioTabla(id, precioActual);
            };
            
            // Guardar con Enter
            input.onkeypress = function(e) {
                if (e.key === 'Enter') {
                    btnGuardar.click();
                }
            };
            
            // Cancelar con Escape
            input.onkeydown = function(e) {
                if (e.key === 'Escape') {
                    btnCancelar.click();
                }
            };
        }
        
        // Función para actualizar precio desde la tabla
        function actualizarPrecioTabla(id, nuevoPrecio, precioAnterior) {
            const parentDiv = document.getElementById(`precio-tabla-${id}`).parentElement;
            parentDiv.innerHTML = '<span style="color: #667eea; font-size: 0.8em;"><span class="material-symbols-outlined">hourglass_empty</span> Actualizando...</span>';
            
            fetch('actualizar_precio_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&precio=${nuevoPrecio}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar display del precio en la tabla
                    restaurarDisplayPrecioTabla(id, nuevoPrecio);
                    
                    // Mostrar notificación de éxito más compacta
                    mostrarModal('success', 'Precio Actualizado', 
                        `Precio actualizado: $${formatearPrecio(precioAnterior)} → $${formatearPrecio(nuevoPrecio)}`
                    );
                } else {
                    // Restaurar precio anterior
                    restaurarDisplayPrecioTabla(id, precioAnterior);
                    mostrarModal('error', 'Error', data.mensaje || 'Error al actualizar el precio');
                }
            })
            .catch(error => {
                // Restaurar precio anterior
                restaurarDisplayPrecioTabla(id, precioAnterior);
                mostrarModal('error', 'Error', 'Error de conexión. Intente nuevamente.');
            });
        }
        
        // Función para restaurar el display del precio en la tabla
        function restaurarDisplayPrecioTabla(id, precio) {
            const parentDiv = document.getElementById(`precio-tabla-${id}`).parentElement;
            parentDiv.innerHTML = `
                <span id="precio-tabla-${id}" style="font-weight: 600;">
                    $${formatearPrecio(precio)}
                </span>
                <button onclick="editarPrecioTabla(${id}, ${precio})" 
                        class="btn-edit-precio"
                        title="Editar precio"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 4px 6px; border-radius: 15px; cursor: pointer; display: flex; align-items: center; font-size: 0.7em; transition: all 0.3s ease;"
                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)';"
                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                    <span class="material-symbols-outlined" style="font-size: 14px;">edit</span>
                </button>
            `;
        }
        
        // Función para editar stock
        function editarStock(id, stockActual) {
            const stockDisplay = document.getElementById(`stock-display-${id}`);
            
            // Crear contenedor para opciones de stock
            const stockContainer = document.createElement('div');
            stockContainer.style.cssText = 'display: flex; flex-direction: column; gap: 10px; min-width: 200px;';
            
            // Título
            const titulo = document.createElement('div');
            titulo.style.cssText = 'font-weight: bold; color: #333; font-size: 0.9em;';
            titulo.textContent = 'Gestionar Stock';
            stockContainer.appendChild(titulo);
            
            // Opción 1: Establecer stock específico
            const divEspecifico = document.createElement('div');
            divEspecifico.style.cssText = 'display: flex; align-items: center; gap: 5px; background: #f8f9fa; padding: 8px; border-radius: 8px;';
            
            const labelEspecifico = document.createElement('label');
            labelEspecifico.textContent = 'Stock:';
            labelEspecifico.style.cssText = 'font-size: 0.8em; min-width: 40px;';
            
            const inputEspecifico = document.createElement('input');
            inputEspecifico.type = 'number';
            inputEspecifico.min = '0';
            inputEspecifico.max = '9999';
            inputEspecifico.value = stockActual;
            inputEspecifico.style.cssText = 'width: 70px; padding: 4px; border: 1px solid #ddd; border-radius: 4px; text-align: center;';
            
            const btnEspecifico = document.createElement('button');
            btnEspecifico.innerHTML = '<span class="material-symbols-outlined" style="font-size: 14px;">check</span>';
            btnEspecifico.style.cssText = 'background: #28a745; color: white; border: none; padding: 4px 6px; border-radius: 4px; cursor: pointer;';
            btnEspecifico.title = 'Establecer stock específico';
            
            divEspecifico.appendChild(labelEspecifico);
            divEspecifico.appendChild(inputEspecifico);
            divEspecifico.appendChild(btnEspecifico);
            stockContainer.appendChild(divEspecifico);
            
            // Opción 2: Incrementar stock
            const divIncrementar = document.createElement('div');
            divIncrementar.style.cssText = 'display: flex; align-items: center; gap: 5px; background: #e8f5e8; padding: 8px; border-radius: 8px;';
            
            const labelIncrementar = document.createElement('label');
            labelIncrementar.textContent = '+';
            labelIncrementar.style.cssText = 'font-size: 0.8em; min-width: 10px; font-weight: bold; color: #28a745;';
            
            const inputIncrementar = document.createElement('input');
            inputIncrementar.type = 'number';
            inputIncrementar.min = '1';
            inputIncrementar.max = '999';
            inputIncrementar.value = 1;
            inputIncrementar.style.cssText = 'width: 70px; padding: 4px; border: 1px solid #28a745; border-radius: 4px; text-align: center;';
            
            const btnIncrementar = document.createElement('button');
            btnIncrementar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 14px;">add</span>';
            btnIncrementar.style.cssText = 'background: #28a745; color: white; border: none; padding: 4px 6px; border-radius: 4px; cursor: pointer;';
            btnIncrementar.title = 'Incrementar stock';
            
            divIncrementar.appendChild(labelIncrementar);
            divIncrementar.appendChild(inputIncrementar);
            divIncrementar.appendChild(btnIncrementar);
            stockContainer.appendChild(divIncrementar);
            
            // Opción 3: Decrementar stock
            const divDecrementar = document.createElement('div');
            divDecrementar.style.cssText = 'display: flex; align-items: center; gap: 5px; background: #ffeaa7; padding: 8px; border-radius: 8px;';
            
            const labelDecrementar = document.createElement('label');
            labelDecrementar.textContent = '-';
            labelDecrementar.style.cssText = 'font-size: 0.8em; min-width: 10px; font-weight: bold; color: #f39c12;';
            
            const inputDecrementar = document.createElement('input');
            inputDecrementar.type = 'number';
            inputDecrementar.min = '1';
            inputDecrementar.max = stockActual;
            inputDecrementar.value = 1;
            inputDecrementar.style.cssText = 'width: 70px; padding: 4px; border: 1px solid #f39c12; border-radius: 4px; text-align: center;';
            
            const btnDecrementar = document.createElement('button');
            btnDecrementar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 14px;">remove</span>';
            btnDecrementar.style.cssText = 'background: #f39c12; color: white; border: none; padding: 4px 6px; border-radius: 4px; cursor: pointer;';
            btnDecrementar.title = 'Decrementar stock';
            
            divDecrementar.appendChild(labelDecrementar);
            divDecrementar.appendChild(inputDecrementar);
            divDecrementar.appendChild(btnDecrementar);
            stockContainer.appendChild(divDecrementar);
            
            // Botones de acción
            const divBotones = document.createElement('div');
            divBotones.style.cssText = 'display: flex; gap: 5px; justify-content: flex-end; margin-top: 10px;';
            
            const btnCancelar = document.createElement('button');
            btnCancelar.innerHTML = '<span class="material-symbols-outlined" style="font-size: 16px;">close</span>';
            btnCancelar.style.cssText = 'background: #dc3545; color: white; border: none; padding: 6px 8px; border-radius: 20px; cursor: pointer; display: flex; align-items: center;';
            btnCancelar.title = 'Cancelar edición';
            
            divBotones.appendChild(btnCancelar);
            stockContainer.appendChild(divBotones);
            
            // Reemplazar el display del stock
            const parentDiv = stockDisplay.parentElement;
            parentDiv.innerHTML = '';
            parentDiv.appendChild(stockContainer);
            
            // Event listeners
            btnEspecifico.onclick = function() {
                const nuevoStock = parseInt(inputEspecifico.value);
                if (nuevoStock < 0 || nuevoStock > 9999) {
                    mostrarModal('error', 'Error', 'El stock debe estar entre 0 y 9999');
                    return;
                }
                actualizarStock(id, 'actualizar_stock', nuevoStock, stockActual);
            };
            
            btnIncrementar.onclick = function() {
                const incremento = parseInt(inputIncrementar.value);
                if (incremento <= 0 || incremento > 999) {
                    mostrarModal('error', 'Error', 'El incremento debe estar entre 1 y 999');
                    return;
                }
                actualizarStock(id, 'incrementar_stock', incremento, stockActual);
            };
            
            btnDecrementar.onclick = function() {
                const decremento = parseInt(inputDecrementar.value);
                if (decremento <= 0 || decremento > stockActual) {
                    mostrarModal('error', 'Error', `El decremento debe estar entre 1 y ${stockActual}`);
                    return;
                }
                actualizarStock(id, 'decrementar_stock', decremento, stockActual);
            };
            
            btnCancelar.onclick = function() {
                restaurarDisplayStock(id, stockActual);
            };
        }
        
        // Función para actualizar stock en la base de datos
        function actualizarStock(id, accion, valor, stockAnterior) {
            // Mostrar indicador de carga
            const parentDiv = document.getElementById(`stock-display-${id}`).parentElement;
            parentDiv.innerHTML = '<span style="color: #17a2b8;"><span class="material-symbols-outlined">hourglass_empty</span> Actualizando...</span>';
            
            fetch('actualizar_producto_stock_precio.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `producto_id=${id}&accion=${accion}&nuevo_valor=${valor}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar display del stock
                    restaurarDisplayStock(id, data.nuevo_stock);
                    
                    // Mostrar notificación de éxito
                    mostrarModal('success', 'Stock Actualizado', 
                        `${data.message}<br><br>
                        <strong>Stock anterior:</strong> ${stockAnterior} unidades<br>
                        <strong>Stock nuevo:</strong> ${data.nuevo_stock} unidades`
                    );
                    
                    // Actualizar también en la tabla si está visible
                    const filaTabla = document.querySelector(`tr[data-id="${id}"]`);
                    if (filaTabla) {
                        const celdaStock = filaTabla.cells[3]; // columna stock
                        celdaStock.textContent = data.nuevo_stock;
                    }
                } else {
                    // Error al actualizar
                    restaurarDisplayStock(id, stockAnterior);
                    mostrarModal('error', 'Error', `No se pudo actualizar el stock: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                restaurarDisplayStock(id, stockAnterior);
                mostrarModal('error', 'Error de Conexión', 'No se pudo conectar con el servidor. Intente nuevamente.');
            });
        }
        
        // Función para restaurar el display del stock
        function restaurarDisplayStock(id, stock) {
            const parentDiv = document.getElementById(`stock-display-${id}`).parentElement;
            parentDiv.innerHTML = `
                <span id="stock-display-${id}" style="font-size: 1.2em; font-weight: 700; color: #17a2b8;">${stock} unidades</span>
                <button onclick="editarStock(${id}, ${stock})" 
                        style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 8px; border-radius: 20px; cursor: pointer; display: flex; align-items: center; font-size: 0.8em; transition: all 0.3s ease;"
                        title="Editar stock"
                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(23, 162, 184, 0.3)';"
                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                </button>
            `;
        }
        
        // Función para formatear precios con separador de miles
        function formatearPrecio(precio) {
            return parseFloat(precio).toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    </script>
</body>
</html>
