<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Producción (Bootstrap)</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/produccion-bootstrap.css">
</head>
<body class="min-vh-100 p-4">

    <div class="container container-main mx-auto shadow-lg rounded-4 p-5">
        
        <h1 class="text-4xl font-weight-bold mb-4 border-bottom pb-2 title-primary">
            🥖 Gestión de Producción de Panadería
        </h1>

        <div id="alert-message" class="alert alert-dismissible fade d-none" role="alert">
            <span id="alert-text"></span>
        </div>

        <div class="mb-5 p-4 section-card">
            <h2 class="h4 font-weight-bold mb-3 title-primary">Registrar Nueva Producción</h2>
            
            <form id="formRegistrar" class="row g-3">
                
                <div class="col-md-6">
                    <label for="idProducto" class="form-label text-dark">ID Producto</label>
                    <input type="number" name="idProducto" id="idProducto" required min="1" 
                            class="form-control form-control-lg input-custom" 
                            placeholder="Ej: 1 (Pan Francés)">
                </div>

                <div class="col-md-6">
                    <label for="cantidad" class="form-label text-dark">Cantidad Producida</label>
                    <input type="number" name="cantidad" id="cantidad" required min="1" 
                            class="form-control form-control-lg input-custom" 
                            placeholder="Ej: 100">
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-primary-custom shadow-sm">
                        ➕ Registrar Producción
                    </button>
                </div>
            </form>
        </div>

        <div class="mb-5 p-4 section-card-light">
            <h2 class="h4 font-weight-bold mb-3 title-primary">Consultar Receta por Producto</h2>
            
            <form id="formReceta" class="d-flex flex-column flex-sm-row gap-3">
                
                <input type="number" name="idProducto" id="idProductoReceta" required min="1" 
                        class="form-control form-control-lg input-custom flex-grow-1" 
                        placeholder="Ingrese ID del Producto para ver receta (Ej: 1)">

                <button type="submit" class="btn btn-warning btn-lg btn-warning-custom shadow-sm">
                    🔍 Ver Receta
                </button>
            </form>

            <div id="recetaResult" class="mt-4 p-3 rounded d-none border-custom">
                <h3 class="h5 font-weight-bold mb-2 title-primary">Receta del Producto</h3>
                <pre id="recetaContent" class="p-3 rounded overflow-auto text-sm pre-custom"></pre>
            </div>
        </div>
        
        <h2 class="h3 font-weight-bold mb-4 mt-5 border-bottom pb-2 text-dark">Historial de Producción</h2>

        <div id="historialContainer" class="table-responsive shadow-sm rounded-3">
            <table class="table table-hover table-striped mb-0 table-bordered">
                <thead class="table-header-custom">
                    <tr>
                        <th class="p-3 text-start text-uppercase text-dark">ID Prod.</th>
                        <th class="p-3 text-start text-uppercase text-dark">ID Reg.</th>
                        <th class="p-3 text-start text-uppercase text-dark">Cantidad</th>
                        <th class="p-3 text-start text-uppercase text-dark">Fecha</th>
                        <th class="p-3 text-center text-uppercase text-dark">Acciones</th>
                    </tr>
                </thead>
                <tbody id="historialBody">
                    </tbody>
            </table>
        </div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-custom-border">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold title-primary" id="editModalLabel">Editar Registro de Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="d-flex flex-column gap-3">
                        
                        <form id="formPut" class="p-3 rounded form-put-custom border">
                            <input type="hidden" name="idProduccion" id="modalIdProduccionPut">
                            <h6 class="font-weight-bold mb-2 text-info">Actualización Completa (PUT)</h6>
                            
                            <div class="mb-3">
                                <label for="modalIdProductoPut" class="form-label text-dark">Nuevo ID Producto</label>
                                <input type="number" name="idProducto" id="modalIdProductoPut" required min="1" class="form-control input-custom">
                            </div>
                            <div class="mb-3">
                                <label for="modalCantidadPut" class="form-label text-dark">Nueva Cantidad</label>
                                <input type="number" name="cantidad" id="modalCantidadPut" required min="1" class="form-control input-custom">
                            </div>
                            <button type="submit" class="btn btn-info w-100 shadow-sm">
                                💾 Actualizar Completamente (PUT)
                            </button>
                        </form>

                        <form id="formPatch" class="p-3 rounded form-patch-custom border">
                            <input type="hidden" name="idProduccion" id="modalIdProduccionPatch">
                            <h6 class="font-weight-bold mb-2 text-purple">Actualización Parcial (PATCH)</h6>
                            
                            <p class="text-sm mb-2 text-purple-dark">Deje vacío el campo que no quiera modificar.</p>
                            
                            <div class="mb-3">
                                <label for="modalCantidadPatch" class="form-label text-dark">Nueva Cantidad (opcional)</label>
                                <input type="number" name="cantidad" id="modalCantidadPatch" min="1" class="form-control input-custom" placeholder="Solo cambia la cantidad">
                            </div>
                            
                            <button type="submit" class="btn btn-patch-custom w-100 shadow-sm">
                                ⚡ Actualizar Parcial (PATCH)
                            </button>
                        </form>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-custom w-100" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script src="../../js/produccion.js"></script> 
</body>
</html>