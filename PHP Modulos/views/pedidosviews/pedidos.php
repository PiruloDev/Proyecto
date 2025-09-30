<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos (CRUD) con Bootstrap</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="../../css/pedidos-custom.css">
    
</head>
<body>

    <!-- ✅ NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gris-oscuro">
        <div class="container-fluid">
            <a class="navbar-brand text-dorado" href="PedidosIndex.php?ruta=pedidos">
                <i class="fas fa-clipboard-list me-2"></i> Módulo de Pedidos
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavPedido" aria-controls="navbarNavPedido" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNavPedido">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $ruta_actual = strtolower($_GET['ruta'] ?? 'pedidos'); ?>

                    <li class="nav-item">
                        <a class="nav-link <?php echo ($ruta_actual == 'pedidos') ? 'active text-white' : ''; ?>" 
                           href="PedidosIndex.php?ruta=pedidos">
                           <i class="fas fa-list"></i> Pedidos de Clientes
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($ruta_actual == 'estados') ? 'active text-white' : ''; ?>" 
                           href="PedidosIndex.php?ruta=estados">
                           <i class="fas fa-check-circle"></i> Estados de Pedido
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($ruta_actual == 'pedidosproveedores') ? 'active text-white' : ''; ?>" 
                           href="PedidosIndex.php?ruta=pedidosproveedores">
                           <i class="fas fa-truck-moving"></i> Pedidos a Proveedores
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ✅ CONTENIDO -->
    <div class="container">
        <h1 class="text-primary text-center my-4">Gestión de Pedidos</h1>

        <?php 
        if (isset($mensaje) && !empty($mensaje)) {
            echo '<div class="alert alert-info" role="alert">' . $mensaje . '</div>'; 
        }
        ?>

        <!-- Lista -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Lista de Pedidos Existentes</h2>
            </div>
            <div class="card-body">
                <?php
                if (isset($pedidos) && is_array($pedidos)) {
                    if (!empty($pedidos)) {
                        echo '<ul class="list-group list-group-flush">';
                        foreach ($pedidos as $pedido) {
                            $id_pedido = htmlspecialchars($pedido['id_PEDIDO'] ?? '');
                            $id_cliente = htmlspecialchars($pedido['id_CLIENTE'] ?? '');
                            $id_empleado = htmlspecialchars($pedido['id_EMPLEADO'] ?? ''); 
                            $id_estado = htmlspecialchars($pedido['id_ESTADO_PEDIDO'] ?? ''); 
                            $total = htmlspecialchars($pedido['total_PRODUCTO'] ?? '');
                            $fecha_ingreso = htmlspecialchars($pedido['fecha_INGRESO'] ?? ''); 
                            $fecha_entrega = htmlspecialchars($pedido['fecha_ENTREGA'] ?? ''); 
                            
                            echo '<li class="list-group-item">';
                            echo '<div>';
                            echo "<p><strong>ID:</strong> $id_pedido</p>";
                            echo "<p><strong>Cliente ID:</strong> $id_cliente</p>";
                            echo "<p><strong>Empleado ID:</strong> $id_empleado</p>";
                            echo "<p><strong>Estado ID:</strong> $id_estado</p>";
                            echo "<p><strong>Total:</strong> $$total</p>";
                            echo "<p><strong>Fecha de Ingreso:</strong> $fecha_ingreso</p>";
                            echo "<p><strong>Fecha de Entrega:</strong> $fecha_entrega</p>";
                            echo '</div>';
                            
                            echo '<div class="d-flex">';
                            echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Seguro deseas eliminar el pedido ' . $id_pedido . '?\');">';
                            echo '<input type="hidden" name="id" value="' . $id_pedido . '">';
                            echo '<input type="hidden" name="_method" value="DELETE">'; 
                            echo '<button type="submit" class="btn btn-danger btn-sm">Eliminar</button>';
                            echo '</form>';
                            echo '</div>'; 
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p class="text-muted">No hay pedidos registrados.</p>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Error al obtener los pedidos.</div>';
                }
                ?>
            </div>
        </div>

        <!-- Form Crear -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Agregar Nuevo Pedido</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="id_CLIENTE_crear" class="form-label">ID del Cliente</label>
                        <input type="number" name="id_CLIENTE" id="id_CLIENTE_crear" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_EMPLEADO_crear" class="form-label">ID del Empleado</label>
                        <input type="number" name="id_EMPLEADO" id="id_EMPLEADO_crear" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_ESTADO_PEDIDO_crear" class="form-label">ID Estado</label>
                        <input type="number" name="id_ESTADO_PEDIDO" id="id_ESTADO_PEDIDO_crear" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_PRODUCTO_crear" class="form-label">Total del Pedido</label>
                        <input type="text" name="total_PRODUCTO" id="total_PRODUCTO_crear" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Pedido</button>
                </form>
            </div>
        </div>

        <!-- Form Update -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Actualizar Pedido Existente</h2>
            </div>
            <div class="card-body">
                <form method="POST"> 
                    <input type="hidden" name="_method" value="PUT"> 
                    <div class="mb-3">
                        <label for="id_pedido_update" class="form-label">ID del Pedido</label>
                        <input type="number" name="id" id="id_pedido_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_CLIENTE_update" class="form-label">Nuevo ID Cliente</label>
                        <input type="number" name="id_CLIENTE" id="id_CLIENTE_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_EMPLEADO_update" class="form-label">Nuevo ID Empleado</label>
                        <input type="number" name="id_EMPLEADO" id="id_EMPLEADO_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_ESTADO_PEDIDO_update" class="form-label">Nuevo ID Estado</label>
                        <input type="number" name="id_ESTADO_PEDIDO" id="id_ESTADO_PEDIDO_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_PRODUCTO_update" class="form-label">Nuevo Total</label>
                        <input type="text" name="total_PRODUCTO" id="total_PRODUCTO_update" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
                </form>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
