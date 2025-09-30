<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estados de Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f0f2f5;
        }
        .container {
            margin-top: 30px;
        }
        .card-header {
            background-color: #fd910dff;
            color: white;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* Clases personalizadas */
        .bg-gris-oscuro {
            background-color: #343a40;
        }
        .text-dorado {
            color: #fd910dff !important;
        }
    </style>
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
    <h1 class="text-primary text-center my-4">Gestión de Estados de Pedido.</h1>

    <?php 
    if (isset($mensaje) && !empty($mensaje)) {
        echo '<div class="alert alert-info" role="alert">' . $mensaje . '</div>'; 
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Lista de Estados Existentes</h2>
        </div>
        <div class="card-body">
            <?php
            if (isset($estadosPedidos) && is_array($estadosPedidos)) {
                if (!empty($estadosPedidos)) {
                    echo '<ul class="list-group list-group-flush">';
                    foreach ($estadosPedidos as $estado) {
                        $id_estado = htmlspecialchars($estado['id_ESTADO_PEDIDO'] ?? '');
                        $nombre_estado = htmlspecialchars($estado['nombre_ESTADO'] ?? '');
                        
                        echo '<li class="list-group-item">';
                        echo '<div>';
                        echo '<p class="mb-1"><strong>ID:</strong> ' . $id_estado . '</p>';
                        echo '<p class="mb-0"><strong>Nombre:</strong> ' . $nombre_estado . '</p>';
                        echo '</div>';
                        
                        echo '<div class="d-flex">';
                        
                        echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar el estado ' . $nombre_estado . ' (ID: ' . $id_estado . ')?\');">';
                        echo '<input type="hidden" name="id" value="' . $id_estado . '">';
                        echo '<input type="hidden" name="_method" value="DELETE">'; 
                        echo '<button type="submit" class="btn btn-danger btn-sm">Eliminar</button>';
                        echo '</form>';
                        
                        echo '</div>'; 
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="text-muted">No hay estados de pedido registrados.</p>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al obtener los estados de pedido o la lista está vacía.</div>';
            }
            ?>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Agregar Nuevo Estado</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="nombre_ESTADO_crear" class="form-label">Nombre del Estado:</label>
                    <input type="text" name="nombre_ESTADO" id="nombre_ESTADO_crear" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Estado</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Actualizar Estado Existente</h2>
        </div>
        <div class="card-body">
            <form method="POST"> 
                <input type="hidden" name="_method" value="PUT"> 
                <div class="mb-3">
                    <label for="id_estado_update" class="form-label">ID del Estado a Actualizar:</label>
                    <input type="number" name="id" id="id_estado_update" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="nombre_ESTADO_update" class="form-label">Nuevo Nombre del Estado:</label>
                    <input type="text" name="nombre_ESTADO" id="nombre_ESTADO_update" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Estado</button>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
