<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Producción</title>
    <!-- Carga de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos personalizados si son necesarios, aunque Tailwind debería manejar la mayoría */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }
        .container-main {
            max-width: 1200px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8">

    <div class="container-main mx-auto bg-white shadow-xl rounded-2xl p-6 md:p-10">
        
        <h1 class="text-4xl font-extrabold text-indigo-700 mb-6 border-b-4 border-indigo-200 pb-2">
            Gestión de Producción de Panadería
        </h1>

        <!-- SECCIÓN DE MENSAJES DE SESIÓN -->
        <?php if (!empty($mensaje)): ?>
            <?php 
                // Define la clase CSS basada en el tipo de mensaje (éxito, error, advertencia)
                $alertClass = (strpos($mensaje, '✅') !== false) ? 'bg-green-100 text-green-800 border-green-500' : 
                              ((strpos($mensaje, '❌') !== false) ? 'bg-red-100 text-red-800 border-red-500' : 'bg-yellow-100 text-yellow-800 border-yellow-500');
            ?>
            <div id="alert-message" class="p-4 mb-6 border-l-4 rounded-lg shadow-md font-medium <?= $alertClass; ?>" role="alert">
                <?= htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- SECCIÓN 1: REGISTRO DE NUEVA PRODUCCIÓN -->
        <div class="mb-10 p-6 bg-indigo-50 border border-indigo-200 rounded-xl shadow-inner">
            <h2 class="text-2xl font-bold text-indigo-800 mb-4">Registrar Nueva Producción</h2>
            
            <!-- El action apunta al controlador actual y usa la acción 'registrar' -->
            <form action="?accion=registrar" method="POST" class="space-y-4">
                
                <!-- ID Producto -->
                <div>
                    <label for="idProducto" class="block text-sm font-medium text-gray-700">ID Producto</label>
                    <input type="number" name="idProducto" id="idProducto" required min="1" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" 
                           placeholder="Ej: 1 (Pan Francés)">
                </div>

                <!-- Cantidad Producida -->
                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad Producida</label>
                    <input type="number" name="cantidad" id="cantidad" required min="1" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" 
                           placeholder="Ej: 100">
                </div>

                <!-- Botón de Registro -->
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    ➕ Registrar Producción
                </button>
            </form>
        </div>

        <!-- SECCIÓN 2: CONSULTA DE RECETA (Otras acciones GET) -->
        <div class="mb-10 p-6 bg-yellow-50 border border-yellow-200 rounded-xl shadow-inner">
            <h2 class="text-2xl font-bold text-yellow-800 mb-4">Consultar Receta por Producto</h2>
            
            <form action="" method="GET" class="flex flex-col sm:flex-row gap-4">
                <input type="hidden" name="accion" value="verReceta">
                
                <input type="number" name="idProducto" id="idProductoReceta" required min="1" 
                       class="flex-grow rounded-md border-gray-300 shadow-sm p-3 focus:border-yellow-500 focus:ring-yellow-500 transition duration-150 ease-in-out" 
                       placeholder="Ingrese ID del Producto para ver receta (Ej: 1)" value="<?= htmlspecialchars($idProducto ?? '') ?>">

                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-yellow-600 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    🔍 Ver Receta
                </button>
            </form>

            <!-- Muestra la Receta (Resultado de la consulta) -->
            <?php if (!empty($receta)): ?>
                <div class="mt-6 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                    <h3 class="font-bold text-lg text-yellow-800">Receta del Producto ID: <?= htmlspecialchars($idProducto) ?></h3>
                    <pre class="mt-2 p-2 bg-yellow-50 text-yellow-900 rounded overflow-auto text-sm">
                        <?= print_r($receta, true) ?>
                    </pre>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- SECCIÓN 3: HISTORIAL DE PRODUCCIÓN (LISTADO Y CRUD AVANZADO) -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6 mt-12 border-b border-gray-300 pb-2">Historial de Producción</h2>

        <?php if (empty($historial)): ?>
            <div class="p-6 bg-gray-100 text-gray-600 rounded-lg text-center">
                No se encontraron registros de producción.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Prod.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Reg.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($historial as $registro): ?>
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($registro['idProducto'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($registro['idProduccion'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($registro['cantidadProducida'] ?? 'N/A') ?></td>
                                <!-- Asumimos que la API devuelve un campo de fecha, si es otro nombre, hay que ajustarlo -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= htmlspecialchars($registro['fechaProduccion'] ?? 'Sin fecha') ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2 justify-center">
                                        <!-- Botón de Editar (abre modal o formulario) -->
                                        <button onclick="openModal(<?= htmlspecialchars($registro['idProduccion'] ?? 0) ?>, <?= htmlspecialchars($registro['cantidadProducida'] ?? 0) ?>)" 
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-100 transition duration-150">
                                            ✏️ Editar
                                        </button>

                                        <!-- Formulario de Eliminación (Requiere POST con _method=DELETE) -->
                                        <form action="?accion=eliminar" method="POST" onsubmit="return confirmDelete(<?= htmlspecialchars($registro['idProduccion'] ?? 0) ?>)" class="inline">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="idProduccion" value="<?= htmlspecialchars($registro['idProduccion'] ?? 0) ?>">
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 transition duration-150">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

    <!-- MODAL PARA EDICIÓN (PUT/PATCH) -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Editar Registro de Producción</h3>
            
            <!-- Botones de Acción dentro del Modal -->
            <div class="space-y-4">
                
                <!-- Formulario PUT (Actualización Completa) -->
                <form id="formPut" action="?accion=actualizar" method="POST" class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="idProduccion" id="modalIdProduccionPut">
                    <h4 class="font-semibold text-blue-700 mb-2">Actualización Completa (PUT)</h4>
                    
                    <div>
                        <label for="modalIdProductoPut" class="block text-sm font-medium text-gray-700">Nuevo ID Producto</label>
                        <input type="number" name="idProducto" id="modalIdProductoPut" required min="1" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label for="modalCantidadPut" class="block text-sm font-medium text-gray-700 mt-2">Nueva Cantidad</label>
                        <input type="number" name="cantidad" id="modalCantidadPut" required min="1" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <button type="submit" class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                        💾 Actualizar Completamente (PUT)
                    </button>
                </form>

                <!-- Formulario PATCH (Actualización Parcial) -->
                <form id="formPatch" action="?accion=parcial" method="POST" class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="idProduccion" id="modalIdProduccionPatch">
                    <h4 class="font-semibold text-purple-700 mb-2">Actualización Parcial (PATCH)</h4>
                    
                    <p class="text-sm text-purple-600 mb-2">Deje vacío el campo que no quiera modificar.</p>
                    
                    <div>
                        <label for="modalCantidadPatch" class="block text-sm font-medium text-gray-700">Nueva Cantidad (opcional)</label>
                        <input type="number" name="cantidad" id="modalCantidadPatch" min="1" class="mt-1 block w-full p-2 border rounded-md" placeholder="Solo cambia la cantidad">
                    </div>
                    
                    <button type="submit" class="mt-4 w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150">
                        ⚡ Actualizar Parcial (PATCH)
                    </button>
                </form>

            </div>

            <!-- Botón de Cerrar Modal -->
            <button onclick="closeModal()" class="mt-6 w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150">
                Cerrar
            </button>
        </div>
    </div>


    <!-- SCRIPT PARA INTERACTIVIDAD -->
    <script>
        // Función para mostrar el modal de edición
        function openModal(idProduccion, cantidad) {
            const modal = document.getElementById('editModal');
            
            // Asignar ID al formulario PUT y PATCH
            document.getElementById('modalIdProduccionPut').value = idProduccion;
            document.getElementById('modalIdProduccionPatch').value = idProduccion;

            // Pre-rellenar campos PUT con los valores actuales (asumiendo que solo tenemos Cantidad e ID)
            // Nota: Aquí se asume que el ID Producto no cambia o se debe re-ingresar.
            document.getElementById('modalCantidadPut').value = cantidad;
            document.getElementById('modalIdProductoPut').value = 1; // Valor por defecto o el que corresponda

            // Limpiar campo PATCH
            document.getElementById('modalCantidadPatch').value = ''; 
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Función para ocultar el modal
        function closeModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Función de confirmación para eliminar
        function confirmDelete(id) {
            // Usamos un modal simulado en lugar de alert/confirm() por restricciones del entorno
            const confirmed = window.prompt(`¿Está seguro de eliminar el registro de producción ID ${id}? Escriba 'ELIMINAR' para confirmar.`);
            return confirmed === 'ELIMINAR';
        }

        // Auto-cierre de mensaje de alerta después de 5 segundos
        const alertMessage = document.getElementById('alert-message');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.style.transition = 'opacity 0.5s ease-out';
                alertMessage.style.opacity = '0';
                setTimeout(() => alertMessage.remove(), 500); // Remover después de la transición
            }, 5000);
        }
    </script>
</body>
</html>
