<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos a Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../css/stylepedidos.css">

</head>
<body>

<!-- NAVBAR -->
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
<!-- FIN NAVBAR -->

<div class="container">
    <h1 class="text-primary text-center my-4">Gestión de Pedidos a Proveedores.</h1>

    <?php 
    // Muestra el mensaje de éxito o error (Create, Update, Delete)
    if (isset($mensaje) && !empty($mensaje)) {
        echo '<div class="alert alert-info" role="alert">' . $mensaje . '</div>'; 
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Lista de Pedidos a Proveedores Existentes</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($pedidosProveedores) && is_array($pedidosProveedores)) {
                if (!empty($pedidosProveedores)) {
                    echo '<ul class="list-group list-group-flush">';
                    foreach ($pedidosProveedores as $pedido) {
                        $id_pedido = htmlspecialchars($pedido['id_PEDIDO_PROV'] ?? '');
                        $id_proveedor = htmlspecialchars($pedido['id_PROVEEDOR'] ?? '');
                        $numero_pedido = htmlspecialchars($pedido['numero_PEDIDO'] ?? '');
                        $estado_pedido = htmlspecialchars($pedido['estado_PEDIDO'] ?? '');
                        $fecha_pedido = htmlspecialchars($pedido['fecha_PEDIDO'] ?? '');

                        echo '<li class="list-group-item">';
                        echo '<div>';
                        echo '<p class="mb-1"><strong>ID Pedido:</strong> ' . $id_pedido . '</p>';
                        echo '<p class="mb-1"><strong>ID Proveedor:</strong> ' . $id_proveedor . '</p>';
                        echo '<p class="mb-1"><strong>Número de Pedido:</strong> ' . $numero_pedido . '</p>';
                        echo '<p class="mb-1"><strong>Estado:</strong> ' . $estado_pedido . '</p>';
                        echo '<p class="mb-0"><strong>Fecha:</strong> ' . $fecha_pedido . '</p>';
                        echo '</div>';

                        echo '<div class="d-flex">';
                        
                        // Formulario para ELIMINAR
                        echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar el pedido ' . $id_pedido . '?\');">';
                        echo '<input type="hidden" name="id_PEDIDO" value="' . $id_pedido . '">';
                        echo '<input type="hidden" name="_method" value="DELETE">'; 
                        echo '<button type="submit" class="btn btn-danger btn-sm">Eliminar</button>';
                        echo '</form>';
                        
                        echo '</div>'; 
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="text-muted">No hay pedidos a proveedores registrados.</p>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al obtener los pedidos a proveedores.</div>';
            }
            ?>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Agregar Nuevo Pedido a Proveedor</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_PROVEEDOR_crear" class="form-label">ID del Proveedor:</label>
                    <input type="number" name="id_PROVEEDOR" id="id_PROVEEDOR_crear" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="numero_PEDIDO_crear" class="form-label">Número de Pedido:</label>
                    <input type="number" name="numero_PEDIDO" id="numero_PEDIDO_crear" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="estado_PEDIDO_crear" class="form-label">Estado del Pedido:</label>
                    <input type="text" name="estado_PEDIDO" id="estado_PEDIDO_crear" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Pedido</button>
            </form>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Actualizar Pedido a Proveedor</h2>
        </div>
        <div class="card-body">
            <form method="POST"> 
                <input type="hidden" name="_method" value="PUT"> 
                <div class="mb-3">
                    <label for="id_PEDIDO_update" class="form-label">ID del Pedido a Actualizar:</label>
                    <input type="number" name="id_PEDIDO" id="id_PEDIDO_update" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="id_PROVEEDOR_update" class="form-label">Nuevo ID del Proveedor:</label>
                    <input type="number" name="id_PROVEEDOR" id="id_PROVEEDOR_update" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="numero_PEDIDO_update" class="form-label">Nuevo Número de Pedido:</label>
                    <input type="number" name="numero_PEDIDO" id="numero_PEDIDO_update" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="estado_PEDIDO_update" class="form-label">Nuevo Estado del Pedido:</label>
                    <input type="text" name="estado_PEDIDO" id="estado_PEDIDO_update" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
