<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    // Limpiar sesión si hay datos inconsistentes
    session_destroy();
    header('Location: login.php?error=session_invalid');
    exit();
}

// Obtener todos los pedidos con información del cliente
$query = "SELECT p.ID_PEDIDO as id, p.FECHA_INGRESO as fecha, p.TOTAL_PRODUCTO as total, 
                 ep.NOMBRE_ESTADO as estado, c.NOMBRE_CLI as cliente_nombre, 
                 c.EMAIL_CLI as cliente_email
          FROM Pedidos p
          JOIN Clientes c ON p.ID_CLIENTE = c.ID_CLIENTE
          JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
          ORDER BY p.FECHA_INGRESO DESC";
$result = mysqli_query($conexion, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styleadminds.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #bb9467;
            color: white;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-header h3 {
            color: white;
            margin-bottom: 30px;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-pendiente { background-color: #fef3c7; color: #92400e; }
        .status-preparacion { background-color: #dbeafe; color: #1e40af; }
        .status-listo { background-color: #dcfce7; color: #166534; }
        .status-entregado { background-color: #d1fae5; color: #065f46; }
        .status-cancelado { background-color: #fee2e2; color: #991b1b; }
        .btn-details {
            background: #bb9467;
            color: white;
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: background 0.3s;
        }
        .btn-details:hover {
            background: #a07c4a;
            color: white;
        }
        .modal-header {
            background: #bb9467;
            color: white;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <a href="homepage.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="../files/img/logoprincipal.jpg" alt="Logo" style="width: 40px; height: 40px; border-radius: 50%;">
                    <h4 style="margin: 0; color: white;">El Castillo del Pan</h4>
                </a>
            </div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard_admin.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="productostabla.php">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="empleados_tabla.php">
                    <i class="fas fa-users"></i>
                    <span>Empleados</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="clientes_tabla.php">
                    <i class="fas fa-user-friends"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="pedidos_historial.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pedidos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reportes_ventas.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn btn-outline-light">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-cart"></i> Historial de Pedidos</h2>
            <div class="text-muted">
                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['cliente_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['cliente_email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                    <td><strong>$<?php echo number_format($row['total'], 2); ?></strong></td>
                                    <td>
                                        <?php
                                        // Mapear clase CSS según el estado
                                        $status_class = '';
                                        switch(strtolower($row['estado'])) {
                                            case 'pendiente':
                                                $status_class = 'status-pendiente';
                                                break;
                                            case 'en preparación':
                                                $status_class = 'status-preparacion';
                                                break;
                                            case 'listo para entrega':
                                                $status_class = 'status-listo';
                                                break;
                                            case 'entregado':
                                                $status_class = 'status-entregado';
                                                break;
                                            case 'cancelado':
                                                $status_class = 'status-cancelado';
                                                break;
                                            default:
                                                $status_class = 'status-pendiente';
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($row['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-details btn-sm" onclick="verDetalles(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><button class="dropdown-item" onclick="cambiarEstado(<?php echo $row['id']; ?>, 1, 'Pendiente')">
                                                        <i class="fas fa-clock text-warning me-2"></i>Pendiente
                                                    </button></li>
                                                    <li><button class="dropdown-item" onclick="cambiarEstado(<?php echo $row['id']; ?>, 2, 'En Preparación')">
                                                        <i class="fas fa-hourglass-half text-info me-2"></i>En Preparación
                                                    </button></li>
                                                    <li><button class="dropdown-item" onclick="cambiarEstado(<?php echo $row['id']; ?>, 3, 'Listo para Entrega')">
                                                        <i class="fas fa-check-circle text-success me-2"></i>Listo para Entrega
                                                    </button></li>
                                                    <li><button class="dropdown-item" onclick="cambiarEstado(<?php echo $row['id']; ?>, 4, 'Entregado')">
                                                        <i class="fas fa-box text-primary me-2"></i>Entregado
                                                    </button></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><button class="dropdown-item text-danger" onclick="cambiarEstado(<?php echo $row['id']; ?>, 5, 'Cancelado')">
                                                        <i class="fas fa-times-circle text-danger me-2"></i>Cancelado
                                                    </button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No hay pedidos registrados</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles del pedido -->
    <div class="modal fade" id="detallesModal" tabindex="-1" aria-labelledby="detallesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detallesModalLabel">
                        <i class="fas fa-receipt"></i> Detalles del Pedido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detallesContent">
                    <!-- Aquí se cargará el contenido del pedido -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function verDetalles(pedidoId) {
            // Realizar una petición AJAX para obtener los detalles del pedido
            fetch(`obtener_detalles_pedido.php?id=${pedidoId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detallesContent').innerHTML = data;
                    document.getElementById('detallesModalLabel').innerHTML = 
                        `<i class="fas fa-receipt"></i> Detalles del Pedido #${pedidoId}`;
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('detallesModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los detalles del pedido',
                        confirmButtonColor: '#bb9467'
                    });
                });
        }

        function cambiarEstado(pedidoId, nuevoEstado, nombreEstado) {
            // Configurar el ícono según el estado
            let icon = 'question';
            let color = '#bb9467';
            
            switch(nuevoEstado) {
                case 1: icon = 'warning'; color = '#f59e0b'; break;
                case 2: icon = 'info'; color = '#3b82f6'; break;
                case 3: icon = 'success'; color = '#10b981'; break;
                case 4: icon = 'success'; color = '#059669'; break;
                case 5: icon = 'error'; color = '#ef4444'; break;
            }
            
            // Confirmar el cambio de estado con SweetAlert2
            Swal.fire({
                title: '¿Cambiar estado del pedido?',
                text: `¿Estás seguro de cambiar el estado del pedido #${pedidoId} a "${nombreEstado}"?`,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar indicador de carga
                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Cambiando estado del pedido',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    fetch('cambiar_estado_pedido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            pedido_id: pedidoId,
                            nuevo_estado: nuevoEstado
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Estado actualizado!',
                                text: data.message,
                                confirmButtonColor: '#bb9467'
                            }).then(() => {
                                // Recargar la página para mostrar los cambios
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al cambiar el estado: ' + data.message,
                                confirmButtonColor: '#bb9467'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cambiar el estado del pedido',
                            confirmButtonColor: '#bb9467'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
