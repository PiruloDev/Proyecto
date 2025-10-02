<div class="action-card mb-4">
    <div class="card-header"><h3>Listado de Productos</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Marca</th>
                    <th>Vence</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($productos)): ?>
                    <?php foreach($productos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['idProducto']) ?></td>
                            <td><?= htmlspecialchars($p['nombreProducto']) ?></td>
                            <td><?= htmlspecialchars($p['precio']) ?></td>
                            <td><?= htmlspecialchars($p['stockMinimo']) ?></td>
                            <td><?= htmlspecialchars($p['marcaProducto']) ?></td>
                            <td><?= htmlspecialchars($p['fechaVencimiento']) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= $p['idProducto'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No hay productos disponibles</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
