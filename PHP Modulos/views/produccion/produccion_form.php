<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Producción</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8C5A37;
            --primary-hover: #6E462A;
            --text-dark: #3D2C21;
            --text-light: #6B5B51;
            --border-color: #D3C5BC;
            --container-bg: rgba(255, 255, 255, 0.75);
            --crema: #f5f5dc;
            --bg-light: #FEFEF8;
            --success: #4CAF50;
            --warning: #FF9800;
            --danger: #F44336;
            --info: #2196F3;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: #FFF8E1;
            background-image: linear-gradient(135deg, #FFF8E1 0%, #F5F5DC 100%);
        }

        .container-main {
            max-width: 1200px;
            background: var(--container-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: var(--text-dark);
        }

        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(140, 90, 55, 0.3);
        }

        .section-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(140, 90, 55, 0.1);
        }

        input[type="number"] {
            border: 2px solid var(--border-color);
            background-color: rgba(255, 255, 255, 0.8);
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        input[type="number"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(140, 90, 55, 0.1);
        }

        .table-row:hover {
            background-color: var(--crema);
        }

        .modal-backdrop {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="min-h-screen p-4 sm:p-8">

    <div class="container-main mx-auto shadow-xl rounded-2xl p-6 md:p-10">
        
        <h1 class="text-4xl font-extrabold mb-6 border-b-4 pb-2" style="border-color: var(--primary-color);">
            🥖 Gestión de Producción de Panadería
        </h1>

        <!-- SECCIÓN DE MENSAJES -->
        <div id="alert-message" class="hidden p-4 mb-6 border-l-4 rounded-lg shadow-md font-medium" role="alert">
            <span id="alert-text"></span>
        </div>

        <!-- SECCIÓN 1: REGISTRO DE NUEVA PRODUCCIÓN -->
        <div class="mb-10 p-6 section-card">
            <h2 class="text-2xl font-bold mb-4" style="color: var(--primary-color);">Registrar Nueva Producción</h2>
            
            <form id="formRegistrar" class="space-y-4">
                
                <div>
                    <label for="idProducto" class="block text-sm font-medium" style="color: var(--text-dark);">ID Producto</label>
                    <input type="number" name="idProducto" id="idProducto" required min="1" 
                           class="mt-1 block w-full rounded-md shadow-sm p-3" 
                           placeholder="Ej: 1 (Pan Francés)">
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-medium" style="color: var(--text-dark);">Cantidad Producida</label>
                    <input type="number" name="cantidad" id="cantidad" required min="1" 
                           class="mt-1 block w-full rounded-md shadow-sm p-3" 
                           placeholder="Ej: 100">
                </div>

                <button type="submit" class="w-full sm:w-auto px-6 py-3 btn-primary text-white font-semibold rounded-lg shadow-md">
                    ➕ Registrar Producción
                </button>
            </form>
        </div>

        <!-- SECCIÓN 2: CONSULTA DE RECETA -->
        <div class="mb-10 p-6 section-card" style="background-color: #FFF8E1;">
            <h2 class="text-2xl font-bold mb-4" style="color: var(--primary-color);">Consultar Receta por Producto</h2>
            
            <form id="formReceta" class="flex flex-col sm:flex-row gap-4">
                
                <input type="number" name="idProducto" id="idProductoReceta" required min="1" 
                       class="flex-grow rounded-md shadow-sm p-3" 
                       placeholder="Ingrese ID del Producto para ver receta (Ej: 1)">

                <button type="submit" class="w-full sm:w-auto px-6 py-3 text-white font-semibold rounded-lg shadow-md" 
                        style="background-color: var(--warning);">
                    🔍 Ver Receta
                </button>
            </form>

            <div id="recetaResult" class="hidden mt-6 p-4 rounded-lg" style="background-color: white; border: 2px solid var(--border-color);">
                <h3 class="font-bold text-lg" style="color: var(--primary-color);">Receta del Producto</h3>
                <pre id="recetaContent" class="mt-2 p-2 rounded overflow-auto text-sm" style="background-color: var(--crema); color: var(--text-dark);"></pre>
            </div>
        </div>
        
        <!-- SECCIÓN 3: HISTORIAL DE PRODUCCIÓN -->
        <h2 class="text-3xl font-bold mb-6 mt-12 border-b pb-2" style="color: var(--text-dark); border-color: var(--border-color);">Historial de Producción</h2>

        <div id="historialContainer" class="overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full divide-y" style="background-color: white;">
                <thead style="background-color: var(--crema);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">ID Prod.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">ID Reg.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">Fecha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: var(--text-dark);">Acciones</th>
                    </tr>
                </thead>
                <tbody id="historialBody" class="divide-y" style="background-color: white; border-color: var(--border-color);">
                    <!-- Los registros se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>

    </div>

    <!-- MODAL PARA EDICIÓN -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 modal-backdrop hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md" style="border: 3px solid var(--primary-color);">
            <h3 class="text-xl font-bold mb-4 border-b pb-2" style="color: var(--text-dark); font-family: 'Playfair Display', serif;">Editar Registro de Producción</h3>
            
            <div class="space-y-4">
                
                <!-- Formulario PUT -->
                <form id="formPut" class="p-4 border rounded-lg" style="background-color: #E3F2FD; border-color: var(--info);">
                    <input type="hidden" name="idProduccion" id="modalIdProduccionPut">
                    <h4 class="font-semibold mb-2" style="color: var(--info);">Actualización Completa (PUT)</h4>
                    
                    <div>
                        <label for="modalIdProductoPut" class="block text-sm font-medium" style="color: var(--text-dark);">Nuevo ID Producto</label>
                        <input type="number" name="idProducto" id="modalIdProductoPut" required min="1" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label for="modalCantidadPut" class="block text-sm font-medium mt-2" style="color: var(--text-dark);">Nueva Cantidad</label>
                        <input type="number" name="cantidad" id="modalCantidadPut" required min="1" class="mt-1 block w-full p-2 border rounded-md">
                    </div>
                    <button type="submit" class="mt-4 w-full px-4 py-2 text-white rounded-lg" style="background-color: var(--info);">
                        💾 Actualizar Completamente (PUT)
                    </button>
                </form>

                <!-- Formulario PATCH -->
                <form id="formPatch" class="p-4 border rounded-lg" style="background-color: #F3E5F5; border-color: #9C27B0;">
                    <input type="hidden" name="idProduccion" id="modalIdProduccionPatch">
                    <h4 class="font-semibold mb-2" style="color: #9C27B0;">Actualización Parcial (PATCH)</h4>
                    
                    <p class="text-sm mb-2" style="color: #7B1FA2;">Deje vacío el campo que no quiera modificar.</p>
                    
                    <div>
                        <label for="modalCantidadPatch" class="block text-sm font-medium" style="color: var(--text-dark);">Nueva Cantidad (opcional)</label>
                        <input type="number" name="cantidad" id="modalCantidadPatch" min="1" class="mt-1 block w-full p-2 border rounded-md" placeholder="Solo cambia la cantidad">
                    </div>
                    
                    <button type="submit" class="mt-4 w-full px-4 py-2 text-white rounded-lg" style="background-color: #9C27B0;">
                        ⚡ Actualizar Parcial (PATCH)
                    </button>
                </form>

            </div>

            <button onclick="closeModal()" class="mt-6 w-full px-4 py-2 rounded-lg" style="background-color: var(--border-color); color: var(--text-dark);">
                Cerrar
            </button>
        </div>
    </div>

    <script>
        // Datos de ejemplo para demostración
        let historial = [
            { idProducto: 1, idProduccion: 101, cantidadProducida: 150, fechaProduccion: '2025-10-01' },
            { idProducto: 2, idProduccion: 102, cantidadProducida: 200, fechaProduccion: '2025-10-01' },
            { idProducto: 1, idProduccion: 103, cantidadProducida: 180, fechaProduccion: '2025-10-02' }
        ];

        function showAlert(message, type = 'success') {
            const alert = document.getElementById('alert-message');
            const alertText = document.getElementById('alert-text');
            
            alertText.textContent = message;
            alert.className = 'p-4 mb-6 border-l-4 rounded-lg shadow-md font-medium';
            
            if (type === 'success') {
                alert.style.backgroundColor = '#C8E6C9';
                alert.style.color = '#2E7D32';
                alert.style.borderColor = '#4CAF50';
            } else if (type === 'error') {
                alert.style.backgroundColor = '#FFCDD2';
                alert.style.color = '#C62828';
                alert.style.borderColor = '#F44336';
            }
            
            alert.classList.remove('hidden');
            
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.classList.add('hidden');
                    alert.style.opacity = '1';
                }, 500);
            }, 5000);
        }

        function renderHistorial() {
            const tbody = document.getElementById('historialBody');
            tbody.innerHTML = historial.map(registro => `
                <tr class="table-row transition duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: var(--text-dark);">${registro.idProducto}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-light);">${registro.idProduccion}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-light);">${registro.cantidadProducida}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-light);">${registro.fechaProduccion}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex space-x-2 justify-center">
                            <button onclick="openModal(${registro.idProduccion}, ${registro.cantidadProducida}, ${registro.idProducto})" 
                                    class="p-2 rounded-full transition duration-150" 
                                    style="color: var(--info);" 
                                    onmouseover="this.style.backgroundColor='#E3F2FD'" 
                                    onmouseout="this.style.backgroundColor='transparent'">
                                ✏️ Editar
                            </button>
                            <button onclick="deleteRegistro(${registro.idProduccion})" 
                                    class="p-2 rounded-full transition duration-150" 
                                    style="color: var(--danger);" 
                                    onmouseover="this.style.backgroundColor='#FFEBEE'" 
                                    onmouseout="this.style.backgroundColor='transparent'">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        document.getElementById('formRegistrar').addEventListener('submit', (e) => {
            e.preventDefault();
            const idProducto = document.getElementById('idProducto').value;
            const cantidad = document.getElementById('cantidad').value;
            
            const newId = Math.max(...historial.map(r => r.idProduccion)) + 1;
            historial.push({
                idProducto: parseInt(idProducto),
                idProduccion: newId,
                cantidadProducida: parseInt(cantidad),
                fechaProduccion: new Date().toISOString().split('T')[0]
            });
            
            renderHistorial();
            showAlert('✅ Producción registrada exitosamente', 'success');
            e.target.reset();
        });

        document.getElementById('formReceta').addEventListener('submit', (e) => {
            e.preventDefault();
            const idProducto = document.getElementById('idProductoReceta').value;
            
            const recetaResult = document.getElementById('recetaResult');
            const recetaContent = document.getElementById('recetaContent');
            
            recetaContent.textContent = `Receta para Producto ID: ${idProducto}\n\nIngredientes:\n- Harina: 500g\n- Agua: 300ml\n- Levadura: 10g\n- Sal: 8g`;
            recetaResult.classList.remove('hidden');
            
            showAlert('✅ Receta consultada correctamente', 'success');
        });

        function openModal(idProduccion, cantidad, idProducto) {
            const modal = document.getElementById('editModal');
            
            document.getElementById('modalIdProduccionPut').value = idProduccion;
            document.getElementById('modalIdProduccionPatch').value = idProduccion;
            document.getElementById('modalCantidadPut').value = cantidad;
            document.getElementById('modalIdProductoPut').value = idProducto;
            document.getElementById('modalCantidadPatch').value = '';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('formPut').addEventListener('submit', (e) => {
            e.preventDefault();
            const idProduccion = parseInt(document.getElementById('modalIdProduccionPut').value);
            const idProducto = parseInt(document.getElementById('modalIdProductoPut').value);
            const cantidad = parseInt(document.getElementById('modalCantidadPut').value);
            
            const index = historial.findIndex(r => r.idProduccion === idProduccion);
            if (index !== -1) {
                historial[index].idProducto = idProducto;
                historial[index].cantidadProducida = cantidad;
                renderHistorial();
                showAlert('✅ Registro actualizado completamente (PUT)', 'success');
                closeModal();
            }
        });

        document.getElementById('formPatch').addEventListener('submit', (e) => {
            e.preventDefault();
            const idProduccion = parseInt(document.getElementById('modalIdProduccionPatch').value);
            const cantidad = document.getElementById('modalCantidadPatch').value;
            
            const index = historial.findIndex(r => r.idProduccion === idProduccion);
            if (index !== -1 && cantidad) {
                historial[index].cantidadProducida = parseInt(cantidad);
                renderHistorial();
                showAlert('✅ Registro actualizado parcialmente (PATCH)', 'success');
                closeModal();
            }
        });

        function deleteRegistro(idProduccion) {
            const confirmed = window.prompt(`¿Está seguro de eliminar el registro de producción ID ${idProduccion}? Escriba 'ELIMINAR' para confirmar.`);
            if (confirmed === 'ELIMINAR') {
                historial = historial.filter(r => r.idProduccion !== idProduccion);
                renderHistorial();
                showAlert('✅ Registro eliminado exitosamente', 'success');
            }
        }

        renderHistorial();
    </script>
</body>
</html>