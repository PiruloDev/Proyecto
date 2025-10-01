<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="container py-4">
  <h1 class="mb-4 text-center">Gestión de Productos (Administrador)</h1>

  <?= $mensaje ?? '' ?>

  <!-- Lista de productos -->
  <div class="mb-5">
    <h2>Lista de productos</h2>
    <div class="list-group">
      <?php foreach ($productos as $p): ?>
        <div class="list-group-item">
          <strong><?= $p["nombreProducto"] ?></strong> |
          Precio: <?= $p["precio"] ?> |
          Stock: <?= $p["stockMinimo"] ?> |
          Marca: <?= $p["marcaProducto"] ?> |
          Vence: <?= $p["fechaVencimiento"] ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Crear producto -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">Agregar producto</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="accion" value="crear">
        <div class="row g-3">
          <div class="col-md-6"><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></div>
          <div class="col-md-6"><input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required></div>
          <div class="col-md-6"><input type="number" name="stockMinimo" class="form-control" placeholder="Stock mínimo" required></div>
          <div class="col-md-6"><input type="text" name="marca" class="form-control" placeholder="Marca" required></div>
          <div class="col-md-6"><input type="date" name="fechaVencimiento" class="form-control" required></div>
          <div class="col-12"><button type="submit" class="btn btn-success w-100">Agregar</button></div>
        </div>
      </form>
    </div>
  </div>

  <!-- Actualizar producto -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Actualizar producto</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="accion" value="actualizar">
        <div class="row g-3">
          <div class="col-md-4"><input type="number" name="id" class="form-control" placeholder="ID" required></div>
          <div class="col-md-4"><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></div>
          <div class="col-md-4"><input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required></div>
          <div class="col-md-4"><input type="number" name="stockMinimo" class="form-control" placeholder="Stock mínimo" required></div>
          <div class="col-md-4"><input type="text" name="marca" class="form-control" placeholder="Marca" required></div>
          <div class="col-md-4"><input type="date" name="fechaVencimiento" class="form-control" required></div>
          <div class="col-12"><button type="submit" class="btn btn-primary w-100">Actualizar</button></div>
        </div>
      </form>
    </div>
  </div>

  <!-- Eliminar producto -->
  <div class="card mb-4">
    <div class="card-header bg-danger text-white">Eliminar producto</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="accion" value="eliminar">
        <div class="row g-3">
          <div class="col-md-6"><input type="number" name="id" class="form-control" placeholder="ID del producto" required></div>
          <div class="col-md-6"><button type="submit" class="btn btn-danger w-100">Eliminar</button></div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
