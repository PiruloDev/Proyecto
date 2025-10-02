<div class="action-card mb-4">
    <div class="card-header"><h3>Listado de Categorías</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($categorias)): ?>
                    <?php foreach($categorias as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['id']) ?></td>
                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">No hay categorías disponibles</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
