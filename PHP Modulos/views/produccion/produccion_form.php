<?php 
// /app/Views/produccion/produccion_form.php

// Recuperar mensajes de sesión (si usas sesiones en tu MVC)
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registro de Producción</title>
</head>
<body>

    <h2>Registro de Producción</h2>

    <?php if ($success): ?><p style='color: green;'><?php echo $success; ?></p><?php endif; ?>
    <?php if ($error): ?><p style='color: red;'><?php echo $error; ?></p><?php endif; ?>

    <form id="form-produccion" method="POST" action="/produccion/registrar">
        
        <label for="id_producto">Producto a Producir:</label>
        <select name="id_producto" id="id_producto" required onchange="cargarReceta()" oninput="calcularIngredientes()">
            <option value="">-- Seleccione un producto --</option>
            <?php foreach ($productos as $producto): // $productos viene del controlador ?>
                <option value="<?php echo $producto['id']; ?>">
                    <?php echo htmlspecialchars($producto['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        
        <label for="cantidad_producida">Cantidad a Producir (Unidades):</label>
        <input type="number" id="cantidad_producida" name="cantidad_producida" min="1" value="1" required oninput="calcularIngredientes()">
        
        <h3>Ingredientes Requeridos (Cálculo Automático)</h3>
        <p>Asegúrese de que el listado es correcto antes de registrar.</p>

        <div id="ingredientes_listado">
            <p>Seleccione un producto e ingrese la cantidad a producir.</p>
        </div>
        
        <br>
        <button type="submit" id="btn-registrar" disabled>Registrar Producción</button>
    </form>

<script>
    // Almacenará la receta del producto seleccionado (Cant. Requerida por unidad)
    let recetaActual = [];

    /**
     * 1. Llama al controlador PHP para obtener la receta de la API de Spring Boot.
     */
    function cargarReceta() {
        const idProducto = document.getElementById('id_producto').value;
        const listadoDiv = document.getElementById('ingredientes_listado');
        listadoDiv.innerHTML = '<p>Cargando receta...</p>';
        recetaActual = [];
        
        if (!idProducto) {
            listadoDiv.innerHTML = '<p>Seleccione un producto.</p>';
            document.getElementById('btn-registrar').disabled = true;
            return;
        }

        // Llamada AJAX al método getReceta del controlador PHP
        fetch(`/produccion/getReceta?id_producto=${idProducto}`) // Ajusta la ruta a tu framework
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudo obtener la receta del servidor.');
                }
                return response.json();
            })
            .then(data => {
                // Los datos contienen la lista de RecetaProducto de Spring Boot
                recetaActual = data;
                calcularIngredientes(); // Una vez cargada la receta, realiza el cálculo inicial
            })
            .catch(error => {
                listadoDiv.innerHTML = `<p style='color: red;'>Error: ${error.message}</p>`;
                document.getElementById('btn-registrar').disabled = true;
            });
    }

    /**
     * 2. Calcula la cantidad total de ingredientes y actualiza la vista.
     */
    function calcularIngredientes() {
        const cantidadProducida = parseInt(document.getElementById('cantidad_producida').value);
        const listadoDiv = document.getElementById('ingredientes_listado');
        let html = '';
        let esValido = true;

        if (recetaActual.length === 0 || isNaN(cantidadProducida) || cantidadProducida <= 0) {
            listadoDiv.innerHTML = (recetaActual.length === 0 && document.getElementById('id_producto').value !== '') 
                ? '<p>Receta cargada, ingrese la cantidad a producir.</p>'
                : '<p>Seleccione un producto y la cantidad a producir.</p>';
            document.getElementById('btn-registrar').disabled = true;
            return;
        }

        recetaActual.forEach(ingrediente => {
            // Cálculo: Cantidad Total = Cantidad Requerida * Cantidad Producida
            const cantidadTotal = parseFloat(ingrediente.cantidadRequerida) * cantidadProducida;
            
            // Genera los campos de input ocultos que se enviarán al controlador PHP
            html += `
                <div style="padding: 5px 0;">
                    <strong>${ingrediente.nombreIngrediente || 'Ingrediente ID ' + ingrediente.idIngrediente}:</strong> 
                    ${cantidadTotal.toFixed(3)} ${ingrediente.unidadMedida}
                    
                    <input type="hidden" name="ingredientes[${ingrediente.idIngrediente}]" value="${cantidadTotal.toFixed(3)}">
                </div>
            `;
        });

        listadoDiv.innerHTML = html;
        document.getElementById('btn-registrar').disabled = !esValido;
    }

    // Llama a cargarReceta al inicio si el formulario tiene un valor preseleccionado
    document.addEventListener('DOMContentLoaded', cargarReceta);
</script>
</body>
</html>