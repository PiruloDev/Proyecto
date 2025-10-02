<?php 
  $isAdmin = true;
  require_once __DIR__ . '/config/configproductos.php';
  include __DIR__ . '/views/productosviews/admin_list.php';
  include "templates/header.php"; 
?>

<section class="w-100 text-center text-white py-5" style="background-color:#8b4513;">
  <h1 class="display-5 fw-bold">Gestión de Productos</h1>
  <p class="lead">Administra tu inventario de manera rápida y sencilla</p>
</section>

<section class="container text-center my-4">
  <h2 class="fw-bold">Listado de Productos</h2>
</section>

<section class="container my-3">
  <div class="table-responsive">
    <table class="table table-striped table-bordered text-center align-middle w-100">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
          <th>Fecha de creación</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Pan francés</td>
          <td>Panes</td>
          <td>$2.500</td>
          <td>50</td>
          <td><span class="badge bg-success">Activo</span></td>
          <td>2025-02-01</td>
          <td>
            <button class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></button>
            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<?php include "templates/footer.php"; ?>
