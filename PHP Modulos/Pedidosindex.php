<?php
// Mostrar versión admin del header/footer
$isAdmin = true;
include __DIR__ . '/templates/header.php';
?>

<!-- overrides CSS rápidos para FORZAR verticalidad -->
<style>
  /* Fuerza bloques verticales y quita posibles flex laterales */
  .block-section { display: block !important; width: 100% !important; clear: both !important; box-sizing: border-box; }
  .full-banner { min-height: 220px; display: flex !important; align-items: center; justify-content: center; flex-direction: column; background: linear-gradient(135deg,#8b4513 0%, #d2b48c 100%); color: #fff; text-align: center; }
  .section-centered { margin: 2rem auto; max-width: 1200px; width: 100%; }
  .table-block { width: 100%; overflow-x: auto; background: transparent; }
  /* Asegurar que contenedores no estén en row/inline por CSS heredado */
  header, nav, main, section, footer, div { box-sizing: border-box; }
</style>

<!-- BANNER ALTO (hero) -->
<section class="block-section full-banner">
  <div class="container">
    <h1 class="display-4 fw-bold mb-2">Gestión de Productos</h1>
    <p class="lead mb-0">Administra tu inventario de manera rápida y sencilla</p>
  </div>
</section>

<!-- Título secundario (bloque separado) -->
<section class="block-section">
  <div class="container section-centered text-center">
    <h2 class="fw-bold">Listado de Productos</h2>
  </div>
</section>

<!-- TABLA (bloque separado) -->
<section class="block-section">
  <div class="container section-centered">
    <div class="table-block shadow-sm rounded">
      <table class="table table-striped table-bordered text-center align-middle mb-0" style="width:100%;">
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
          <!-- filas de ejemplo (vacía si quieres) -->
          <tr>
            <td>1</td>
            <td>Pan Tradicional</td>
            <td>Panes</td>
            <td>$2.500</td>
            <td>40</td>
            <td><span class="badge bg-success">Disponible</span></td>
            <td>2025-01-12</td>
            <td>
              <button class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></button>
              <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          <!-- si quieres filas vacías, borra estas filas de ejemplo -->
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php
include __DIR__ . '/templates/footer.php';
?>
