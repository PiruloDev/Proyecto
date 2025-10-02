<?php
require_once __DIR__ . '/../../config/configproductos.php';
require_once __DIR__ . '/../../config/configCategoriaProductos.php';

$productos = consumirGET_Productos(EndpointsProductos::LISTAR);

$categorias = consumirGET_Categorias(EndpointsCategorias::LISTAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $stockMinimo = $_POST['stockMinimo'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $fechaVencimiento = $_POST['fechaVencimiento'] ?? '';
    $categoriaId = $_POST['categoriaId'] ?? '';
    $activo = isset($_POST['activo']) ? true : false;

    if ($accion === 'crear' || $accion === 'actualizar') {
        $data = [
            "nombreProducto" => $nombre,
            "precio" => (float)$precio,
            "stockMinimo" => (int)$stockMinimo,
            "marcaProducto" => $marca,
            "fechaVencimiento" => $fechaVencimiento,
            "idCategoriaProducto" => (int)$categoriaId,
            "activo" => $activo
        ];

        if ($accion === 'actualizar') {
            $response = consumirAPI_Productos(EndpointsProductos::actualizar($id), 'PATCH', $data);
        } else {
            $response = consumirAPI_Productos(EndpointsProductos::CREAR, 'POST', $data);
        }

        header("Location: admin_list.php?mensaje=" . urlencode($response['respuesta']['mensaje'] ?? 'Acción realizada'));
        exit;
    }

    if ($accion === 'eliminar') {
        $response = consumirAPI_Productos(EndpointsProductos::eliminar($id), 'DELETE');
        header("Location: admin_list.php?mensaje=" . urlencode($response['respuesta']['mensaje'] ?? 'Producto eliminado'));
        exit;
    }
}

$mensaje = $_GET['mensaje'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin Productos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Productos</h1>

    <?php if($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- Botón para abrir modal de crear producto -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#productoModal" onclick="limpiarFormulario()">Crear Producto</button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Marca</th>
                <th>Fecha Vencimiento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['Id Producto:'] ?? $p['idProducto'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['Nombre Producto:'] ?? $p['nombreProducto'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['Precio:'] ?? $p['precio'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['Stock Minímo:'] ?? $p['stockMinimo'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['Marca Producto:'] ?? $p['marcaProducto'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['Fecha Vencimiento:'] ?? $p['fechaVencimiento'] ?? '') ?></td>
                <td>
                    <!-- Editar -->
                    <button class="btn btn-sm btn-primary btn-editar"
                        data-id="<?= htmlspecialchars($p['Id Producto:'] ?? $p['idProducto'] ?? '') ?>"
                        data-nombre="<?= htmlspecialchars($p['Nombre Producto:'] ?? $p['nombreProducto'] ?? '') ?>"
                        data-precio="<?= htmlspecialchars($p['Precio:'] ?? $p['precio'] ?? '') ?>"
                        data-stock="<?= htmlspecialchars($p['Stock Minímo:'] ?? $p['stockMinimo'] ?? '') ?>"
                        data-marca="<?= htmlspecialchars($p['Marca Producto:'] ?? $p['marcaProducto'] ?? '') ?>"
                        data-fecha="<?= htmlspecialchars($p['Fecha Vencimiento:'] ?? $p['fechaVencimiento'] ?? '') ?>"
                        data-categoria="<?= htmlspecialchars($p['Id Categoria Producto:'] ?? $p['idCategoriaProducto'] ?? '') ?>"
                        data-activo="<?= htmlspecialchars($p['activo'] ?? 1) ?>"
                        data-bs-toggle="modal" data-bs-target="#productoModal">✏</button>

                    <!-- Eliminar -->
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar este producto?');">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($p['Id Producto:'] ?? $p['idProducto'] ?? '') ?>">
                        <button type="submit" class="btn btn-sm btn-danger">🗑</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal formulario -->
<div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="productoForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="accion" value="crear">
          <input type="hidden" name="id" value="">

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
            <label>Fecha Vencimiento</label>
            <input type="date" name="fechaVencimiento" class="form-control">
          </div>

          <div class="mb-3">
            <label>Categoría</label>
            <select name="categoriaId" class="form-control" required>
              <?php foreach($categorias as $cat): ?>
                <option value="<?= $cat['ID_CATEGORIA_PRODUCTO'] ?? $cat['id'] ?>"><?= $cat['NOMBRE_CATEGORIAPRODUCTO'] ?? $cat['nombre'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" name="activo" class="form-check-input" checked>
            <label class="form-check-label">Activo</label>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function limpiarFormulario() {
  const form = document.getElementById('productoForm');
  form.reset();
  form.querySelector('input[name="accion"]').value = 'crear';
  form.querySelector('input[name="id"]').value = '';
}

document.addEventListener('DOMContentLoaded', function () {
  const editarBtns = document.querySelectorAll('.btn-editar');
  const form = document.getElementById('productoForm');

  editarBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      form.querySelector('input[name="accion"]').value = 'actualizar';
      form.querySelector('input[name="id"]').value = this.dataset.id || '';
      form.querySelector('input[name="nombre"]').value = this.dataset.nombre || '';
      form.querySelector('input[name="precio"]').value = this.dataset.precio || '';
      form.querySelector('input[name="stockMinimo"]').value = this.dataset.stock || '';
      form.querySelector('input[name="marca"]').value = this.dataset.marca || '';
      form.querySelector('input[name="fechaVencimiento"]').value = this.dataset.fecha || '';
      form.querySelector('select[name="categoriaId"]').value = this.dataset.categoria || '';
      form.querySelector('input[name="activo"]').checked = (this.dataset.activo == 1 || this.dataset.activo === '1');
    });
  });
});
</script>
</body>
</html>
