<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Categorías de Ingredientes - El Castillo del Pan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css">
  <!-- Bootstrap y estilos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  

</head>
<body>

<div class="container-fluid">
  <div class="row g-0">
    
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar">
      <div class="d-flex flex-column">
        <div class="sidebar-header text-center">
          <h5 class="mb-0">El Castillo del Pan</h5>
        </div>
        <nav class="nav flex-column">
          <a class="nav-link" href="views\ingredienteviews\menu.php"><i class="fas fa-home"></i> Dashboard</a>
          <a class="nav-link" href="Ingredienteindex.php?modulo=ingredientes&accion=listar"><i class="fas fa-cheese"></i> Ingredientes</a>
          <a class="nav-link active" href="Ingredienteindex.php?modulo=categoria&accion=listar"><i class="fas fa-list-alt"></i> Categorías</a>
          <a class="nav-link" href="Ingredienteindex.php?modulo=proveedores&accion=listar"><i class="fas fa-truck"></i> Proveedores</a>
          <a class="nav-link" href="Ingredienteindex.php?modulo=detallePedidos&accion=listar"><i class="fas fa-receipt"></i> Detalle Pedidos</a>
        </nav>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 main-content">
      
      <!-- Top Navbar -->
      <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid">
          <a class="navbar-brand d-md-none" href="menu.php">Menú</a>
          <div class="collapse navbar-collapse justify-content-end">
            <div class="navbar-nav">
              <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                  <span class="me-2 text-dark d-none d-sm-inline">Administrador</span>
                  <div class="profile-icon-wrapper"><i class="fas fa-user"></i></div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <h1 class="mt-3">Gestión de Categorías de Ingredientes</h1>

      <!-- Mensaje -->
      <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-info my-3">
          <i class="fas fa-info-circle"></i> <?= htmlspecialchars($mensaje) ?>
        </div>
      <?php endif; ?>

      <!-- Lista -->
      <section id="lista" class="mb-5">
        <h2>Lista de Categorías</h2>
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($categorias)) : ?>
                <?php foreach ($categorias as $c) : ?>
                  <tr>
                    <td><?= htmlspecialchars($c['idCategoriaIngrediente']) ?></td>
                    <td><?= htmlspecialchars($c['nombreCategoria']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="2" class="text-center">No hay categorías.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Formulario Agregar -->
      <section id="agregar" class="mb-5">
        <h2>Agregar Categoría</h2>
        <form method="POST" action="?modulo=categoria&accion=agregar" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre categoría</label>
            <input type="text" name="nombreCategoria" class="form-control" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
          </div>
        </form>
      </section>

      <!-- Formulario Editar -->
      <section id="editar" class="mb-5">
        <h2>Editar Categoría</h2>
        <form method="POST" action="?modulo=categoria&accion=editar" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">ID Categoría</label>
            <input type="number" name="idCategoria" class="form-control" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nuevo nombre</label>
            <input type="text" name="nombreCategoria" class="form-control" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</button>
          </div>
        </form>
      </section>

      <!-- Formulario Eliminar -->
      <section id="eliminar" class="mb-5">
        <h2>Eliminar Categoría</h2>
        <form method="POST" action="?modulo=categoria&accion=eliminar" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">ID Categoría</label>
            <input type="number" name="idCategoria" class="form-control" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
          </div>
        </form>
      </section>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
