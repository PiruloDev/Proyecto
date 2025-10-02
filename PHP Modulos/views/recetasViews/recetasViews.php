<?php
// C:\xampp\htdocs\REbase\PHP Modulos\Recetasindex.php (Vista PHP Pura con Ingredientes Dinámicos)

// --------------------------------------------------------------------------------------
// INICIO DEL CONTROLADOR Y LÓGICA PHP (Ejecutado al principio)
// --------------------------------------------------------------------------------------

// La ruta del servicio es relativa a la raíz del proyecto.
require_once __DIR__ . '../../../services/inventarioservices/RecetasService.php'; 

// Inicialización
$recetasService = new RecetasService();

// La URL de acción y redirección es el propio archivo que se está ejecutando.
$selfUrl = basename(__FILE__); // Obtiene 'Recetasindex.php'

// Inicializar variables para la vista
$recetasAgrupadas = [];
$errorMessage = null;
$successMessage = null;


// --- FUNCIÓN CENTRAL: Reestructurar los datos de POST al formato RecetaRequest ---
/**
 * Transforma el array plano de $_POST['ingredientes'] en el formato JSON esperado por la API de Java.
 */
function transformarPostADataApi(int $idProducto, array $ingredientesPost): array {
    $ingredientesFormateados = [];
    foreach ($ingredientesPost as $ing) {
        // Validación y casteos de tipo
        if (!empty($ing['idIngrediente']) && !empty($ing['cantidadNecesaria'])) {
            $ingredientesFormateados[] = [
                'idIngrediente' => (int)($ing['idIngrediente']),
                'cantidadNecesaria' => (float)($ing['cantidadNecesaria']),
                'unidadMedida' => $ing['unidadMedida'] ?? ''
            ];
        }
    }

    return [
        'idProducto' => $idProducto,
        'ingredientes' => $ingredientesFormateados
    ];
}


// --- 1. MANEJO DE ACCIONES POST (LÓGICA CRUD sin AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? null;
    $idProducto = (int)($_POST['idProducto'] ?? 0); 
    $ingredientesPost = $_POST['ingredientes'] ?? []; // Array estructurado de ingredientes
    $response = ['success' => false, 'error' => 'Acción no reconocida.'];
    
    try {
        switch ($action) {
            case 'create':
                $data = transformarPostADataApi($idProducto, $ingredientesPost);
                if (empty($data['ingredientes'])) {
                     throw new Exception('Debe añadir al menos un ingrediente.');
                }
                $response = $recetasService->crearReceta($data);
                $successMessage = 'Receta creada exitosamente.';
                break;

            case 'update':
                $data = transformarPostADataApi($idProducto, $ingredientesPost);
                $response = $recetasService->actualizarReceta($idProducto, $data);
                $successMessage = 'Receta actualizada exitosamente.';
                break;
            
            case 'delete':
                $response = $recetasService->eliminarReceta($idProducto);
                $successMessage = 'Receta eliminada exitosamente.';
                break;
            
            default:
                throw new Exception('Acción de formulario no válida: ' . $action);
        }

        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Error desconocido del servicio.');
        }

        // REDIRECCIÓN (POST-Redirect-GET)
        header("Location: " . $selfUrl . "?status=success&msg=" . urlencode($successMessage));
        exit();

    } catch (Exception $e) {
        header("Location: " . $selfUrl . "?status=error&msg=" . urlencode($e->getMessage()));
        exit();
    }
}


// --- 2. MANEJO DE MENSAJES DE ESTADO (Despues de redirección GET) ---
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $successMessage = htmlspecialchars($_GET['msg'] ?? 'Operación exitosa.');
    } else if ($_GET['status'] === 'error') {
        $errorMessage = htmlspecialchars($_GET['msg'] ?? 'Ha ocurrido un error.');
    }
}


// --- 3. OBTENCIÓN DE DATOS PARA EL LISTADO (GET inicial) ---
$responseList = $recetasService->listarRecetas();

if ($responseList['success']) {
    $detallesReceta = $responseList['response'] ?? [];
    
    // Agrupar los detalles de la receta por ID_PRODUCTO para la vista
    foreach ($detallesReceta as $detalle) {
        $idProducto = $detalle['idProducto'];
        if (!isset($recetasAgrupadas[$idProducto])) {
            $recetasAgrupadas[$idProducto] = [
                'idProducto' => $idProducto,
                'ingredientes' => []
            ];
        }
        $recetasAgrupadas[$idProducto]['ingredientes'][] = $detalle;
    }
} else {
    $errorMessage = $responseList['error'] ?? 'Error al cargar la lista de recetas.';
}

// --------------------------------------------------------------------------------------
// FIN DE LA LÓGICA PHP / COMIENZO DEL HTML
// --------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Recetas (PHP Puro Dinámico)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table thead { background-color: #007bff; color: white; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .btn { padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; text-decoration: none; display: inline-block; margin-right: 5px;}
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 60%; border-radius: 8px; }
        .modal-header { padding: 10px; border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        input[type="text"], input[type="number"] { padding: 8px; margin: 5px 0 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .ingrediente-group { display: flex; gap: 10px; margin-bottom: 10px; border: 1px solid #eee; padding: 10px; border-radius: 4px;}
        .ingrediente-group input:nth-child(1) { flex: 1; }
        .ingrediente-group input:nth-child(2) { flex: 1; }
        .ingrediente-group input:nth-child(3) { flex: 0.5; }
        .ingrediente-group button { flex: 0.2; height: 38px; align-self: center; }
        .full-width { width: 100%; }
        .btn-add { background-color: #007bff; color: white; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestión de Recetas (PHP Puro Dinámico)</h1>

    <?php if ($successMessage): ?>
        <div class="alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert-danger">Error: **<?php echo $errorMessage; ?>**</div>
    <?php endif; ?>

    <button class="btn btn-success" onclick="document.getElementById('modalCrearReceta').style.display='block'">
        Crear Nueva Receta
    </button>
    
    <table class="table" id="tablaRecetas">
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Número de Ingredientes</th>
                <th>Primer Ingrediente (Ej.)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recetasAgrupadas)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No hay recetas registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recetasAgrupadas as $idProducto => $receta): 
                    $primerIngrediente = reset($receta['ingredientes']);
                    $idIngrediente = htmlspecialchars($primerIngrediente['idIngrediente'] ?? 'N/A');
                    $cantidad = htmlspecialchars($primerIngrediente['cantidadRequerida'] ?? '0');
                    $unidad = htmlspecialchars($primerIngrediente['unidadMedida'] ?? '');
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($idProducto); ?></td>
                        <td><?php echo count($receta['ingredientes']); ?></td>
                        <td><?php echo "$idIngrediente ($cantidad $unidad)"; ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="alert('La edición de recetas complejas requiere AJAX. Puede usar Eliminar y Crear para probar.');">
                                Editar
                            </button>
                            
                            <form action="<?php echo $selfUrl; ?>" method="POST" style="display: inline;" onsubmit="return confirm('¿Confirma que desea eliminar la receta del Producto ID <?php echo $idProducto; ?>?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalCrearReceta" class="modal">
    <div class="modal-content">
        <div class="modal-header" style="background-color: #28a745; color: white;">
            <span class="close" onclick="document.getElementById('modalCrearReceta').style.display='none'">&times;</span>
            <h5 class="modal-title">Crear Receta (Con Ingredientes)</h5>
        </div>
        <form action="<?php echo $selfUrl; ?>" method="POST" style="padding: 10px;">
            <input type="hidden" name="action" value="create"> 
            
            <label for="idProductoCrear">ID Producto Final (Lo que se va a producir)</label>
            <input type="number" class="full-width" id="idProductoCrear" name="idProducto" required>
            
            <hr>
            <h4>Ingredientes Requeridos:</h4>
            <div id="ingredientes-container">
                </div>
            
            <button type="button" class="btn btn-add full-width" onclick="addIngredient('ingredientes-container')">
                + Añadir Ingrediente
            </button>
            <hr>

            <button type="button" class="btn" style="background-color: #6c757d; color: white;" onclick="document.getElementById('modalCrearReceta').style.display='none'">Cerrar</button>
            <button type="submit" class="btn btn-success">Guardar Receta</button>
        </form>
    </div>
</div>

<script>
    let ingredientCount = 0;

    // Función para generar la estructura HTML de un ingrediente
    function createIngredientRow(index) {
        return `
            <div class="ingrediente-group" id="ingrediente-row-${index}">
                <input type="number" name="ingredientes[${index}][idIngrediente]" placeholder="ID Ingrediente" required>
                <input type="number" step="0.01" name="ingredientes[${index}][cantidadNecesaria]" placeholder="Cantidad" required>
                <input type="text" name="ingredientes[${index}][unidadMedida]" placeholder="Unidad" required>
                <button type="button" class="btn btn-danger" onclick="removeIngredient(${index})">X</button>
            </div>
        `;
    }

    // Función para añadir un nuevo ingrediente al formulario
    function addIngredient(containerId) {
        const container = document.getElementById(containerId);
        const newRow = document.createElement('div');
        newRow.innerHTML = createIngredientRow(ingredientCount);
        container.appendChild(newRow.firstChild);
        ingredientCount++;
    }

    // Función para eliminar un ingrediente
    function removeIngredient(index) {
        const row = document.getElementById(`ingrediente-row-${index}`);
        if (row) {
            row.remove();
        }
    }

    // Inicializar el formulario con un ingrediente al cargar
    document.addEventListener('DOMContentLoaded', () => {
        // Asegurarse de que el modal de creación se inicialice con al menos un ingrediente
        addIngredient('ingredientes-container');
    });

    // Función para cargar datos en el modal de Edición (Mantenida por si se usa)
    function cargarDatosEdicion(button) {
        // ... (Esta función es compleja para arrays, por lo que se mantiene simple y se deshabilita la edición)
        // Se deja el código simple anterior si se necesitara:
        // const idProducto = button.getAttribute('data-id');
        // document.getElementById('idProductoEditarHidden').value = idProducto;
    }
</script>

</body>
</html>