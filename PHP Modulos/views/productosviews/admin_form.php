<?php
// admin_form.php
// Este archivo contiene los modales para productos y categorías
?>

<!-- Modal Producto -->
<div class="modal fade" id="productoModal" tabindex="-1" aria-labelledby="productoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="productoForm" method="POST">
        <input type="hidden" name="accion" value="crear">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="tipo" value="productos">

        <div class="modal-header">
          <h5 class="modal-title" id="productoModalLabel">Agregar producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Precio</label>
            <input type="number" name="precio" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stockMinimo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Marca</label>
            <input type="text" name="marca" class="form-control">
          </div>
          <div class="mb-3">
            <label>Fecha de vencimiento</label>
            <input type="date" name="fechaVencimiento" class="form-control">
          </div>
          <div class="mb-3">
            <label>Categoría</label>
            <select name="categoriaId" class="form-select" required>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['ID_CATEGORIA_PRODUCTO'] ?>"><?= $cat['NOMBRE_CATEGORIAPRODUCTO'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-check mb-3">
            <input type="checkbox" name="activo" class="form-check-input" checked>
            <label class="form-check-label">Activo</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Categoría -->
<div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="categoriaForm" method="POST">
        <input type="hidden" name="accion" value="crear">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="tipo" value="categorias">

        <div class="modal-header">
          <h5 class="modal-title" id="categoriaModalLabel">Agregar categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
