<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos (CRUD) con Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #ffffffff;
        }
        .container {
            margin-top: 30px;
        }
        .card-header {
            background-color: #825013ff;
            color: white;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="text-primary text-center my-4">Gestión de Pedidos.</h1>

        <?php 
        // Muestra el mensaje de éxito o error (Create, Update, Delete)
        if (isset($mensaje) && !empty($mensaje)) {
            echo '<div class="alert alert-info" role="alert">' . $mensaje . '</div>'; 
        }
        ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Lista de Pedidos Existentes</h2>
            </div>
            <div class="card-body">
                <?php
                // Se verifica si la variable $pedidos es un array y si hay datos
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
                            echo '<p class="mb-1"><strong>ID:</strong> ' . $id_pedido . '</p>';
                            echo '<p class="mb-1"><strong>Cliente ID:</strong> ' . $id_cliente . '</p>';
                            echo '<p class="mb-1"><strong>Empleado ID:</strong> ' . $id_empleado . '</p>';
                            echo '<p class="mb-1"><strong>Estado ID:</strong> ' . $id_estado . '</p>';
                            echo '<p class="mb-1"><strong>Total:</strong> $' . $total . '</p>';
                            echo '<p class="mb-1"><strong>Fecha de Ingreso:</strong> ' . $fecha_ingreso . '</p>';
                            echo '<p class="mb-0"><strong>Fecha de Entrega:</strong> ' . $fecha_entrega . '</p>';
                            echo '</div>';
                            
                            echo '<div class="d-flex">';
                           
                            
                            // Formulario para ELIMINAR
                            echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'¿Estás seguro de que quieres eliminar el pedido ' . $id_pedido . '?\');">';
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
                    echo '<div class="alert alert-danger" role="alert">Error al obtener los pedidos o la lista está vacía.</div>';
                }
                ?>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Agregar Nuevo Pedido</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="id_CLIENTE_crear" class="form-label">ID del Cliente:</label>
                        <input type="number" name="id_CLIENTE" id="id_CLIENTE_crear" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_EMPLEADO_crear" class="form-label">ID del Empleado:</label>
                        <input type="number" name="id_EMPLEADO" id="id_EMPLEADO_crear" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_ESTADO_PEDIDO_crear" class="form-label">ID Estado:</label>
                        <input type="number" name="id_ESTADO_PEDIDO" id="id_ESTADO_PEDIDO_crear" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_PRODUCTO_crear" class="form-label">Total del Pedido:</label>
                        <input type="text" name="total_PRODUCTO" id="total_PRODUCTO_crear" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Pedido</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Actualizar Pedido Existente</h2>
            </div>
            <div class="card-body">
                <form method="POST"> 
                    <input type="hidden" name="_method" value="PUT"> 
                    <div class="mb-3">
                        <label for="id_pedido_update" class="form-label">ID del Pedido a Actualizar:</label>
                        <input type="number" name="id" id="id_pedido_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_CLIENTE_update" class="form-label">Nuevo ID del Cliente:</label>
                        <input type="number" name="id_CLIENTE" id="id_CLIENTE_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_EMPLEADO_update" class="form-label">Nuevo ID del Empleado:</label>
                        <input type="number" name="id_EMPLEADO" id="id_EMPLEADO_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_ESTADO_PEDIDO_update" class="form-label">Nuevo ID Estado:</label>
                        <input type="number" name="id_ESTADO_PEDIDO" id="id_ESTADO_PEDIDO_update" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_PRODUCTO_update" class="form-label">Nuevo Total del Pedido:</label>
                        <input type="text" name="total_PRODUCTO" id="total_PRODUCTO_update" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>