<div class="action-card mb-4">
    <div class="card-header"><h3>Agregar / Actualizar Categoría</h3></div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="row g-3">
                <div class="col-md-8"><input type="text" name="nombre" class="form-control" placeholder="Nombre de la categoría" required></div>
                <div class="col-md-4"><button type="submit" class="btn btn-success w-100">Agregar</button></div>
            </div>
        </form>
    </div>
</div>
