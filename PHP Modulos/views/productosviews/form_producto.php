<div class="action-card mb-4">
    <div class="card-header"><h3>Agregar / Actualizar Producto</h3></div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="row g-3">
                <div class="col-md-4"><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></div>
                <div class="col-md-4"><input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required></div>
                <div class="col-md-4"><input type="number" name="stockMinimo" class="form-control" placeholder="Stock mínimo" required></div>
                <div class="col-md-4"><input type="text" name="marca" class="form-control" placeholder="Marca" required></div>
                <div class="col-md-4"><input type="date" name="fechaVencimiento" class="form-control" required></div>
                <div class="col-md-4"><button type="submit" class="btn btn-success w-100">Agregar</button></div>
            </div>
        </form>
    </div>
</div>
