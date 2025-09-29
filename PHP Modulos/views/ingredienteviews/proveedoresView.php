<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Proveedores - El Castillo del Pan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="../../images/logoprincipal.jpg">

  <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css">

  <!-- Bootstrap y estilos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link href="/css/stylemoduloinv.css" rel="stylesheet">
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
          <a class="nav-link" href="menu.php"><i class="fas fa-home"></i> Dashboard</a>
          <a class="nav-link" href="Ingredienteindex.php?modulo=ingredientes&accion=listar"><i class="fas fa-cheese"></i> Ingredientes</a>
          <a class="nav-link" href="Ingredienteindex.php?modulo=categoria&accion=listar"><i class="fas fa-list-alt"></i> Categorías</a>
          <a class="nav-link active" href="Ingredienteindex.php?modulo=proveedores&accion=listar"><i class="fas fa-truck"></i> Proveedores</a>
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

      <h1 class="mt-3">Gestión de Proveedores</h1>

      <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info my-3">
          <?= $mensaje ?>
        </div>
      <?php endif; ?>

      <!-- Agregar Proveedor -->
      <section id="agregar" class="mb-5">
        <h2>Agregar Proveedor</h2>
        <form method="POST" action="?modulo=proveedores&accion=agregar" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombreProv" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefonoProv" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="emailProv" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccionProv" class="form-control">
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="activoProv" value="1" id="activoProv">
              <label class="form-check-label" for="activoProv">Activo</label>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Guardar</button>
          </div>
        </form>
      </section>

      <!-- Lista de Proveedores -->
      <section id="listado" class="mb-5">
        <h2>Lista de Proveedores</h2>
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Activo</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($proveedores) && is_array($proveedores)): ?>
                <?php foreach ($proveedores as $prov): ?>
                  <tr>
                    <td><?= htmlspecialchars($prov["idProveedor"]) ?></td>
                    <td><?= htmlspecialchars($prov["nombreProv"]) ?></td>
                    <td><?= htmlspecialchars($prov["telefonoProv"]) ?></td>
                    <td><?= htmlspecialchars($prov["emailProv"]) ?></td>
                    <td><?= htmlspecialchars($prov["direccionProv"]) ?></td>
                    <td><?= $prov["activoProv"] ? "Sí" : "No" ?></td>
                    <td>
                      <!-- Actualizar -->
                      <form method="POST" action="?modulo=proveedores&accion=actualizar" class="d-inline">
                        <input type="hidden" name="id" value="<?= $prov["idProveedor"] ?>">
                        <input type="text" name="nombreProv" value="<?= htmlspecialchars($prov["nombreProv"]) ?>" class="form-control form-control-sm mb-1">
                        <input type="text" name="telefonoProv" value="<?= htmlspecialchars($prov["telefonoProv"]) ?>" class="form-control form-control-sm mb-1">
                        <input type="email" name="emailProv" value="<?= htmlspecialchars($prov["emailProv"]) ?>" class="form-control form-control-sm mb-1">
                        <input type="text" name="direccionProv" value="<?= htmlspecialchars($prov["direccionProv"]) ?>" class="form-control form-control-sm mb-1">
                        <div class="form-check">
                          <input type="checkbox" name="activoProv" value="1" class="form-check-input" <?= $prov["activoProv"] ? "checked" : "" ?>>
                          <label class="form-check-label">Activo</label>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm mt-1"><i class="fas fa-edit"></i> Actualizar</button>
                      </form>

                      <!-- Eliminar -->
                      <form method="POST" action="?modulo=proveedores&accion=eliminar" class="d-inline">
                        <input type="hidden" name="id" value="<?= $prov["idProveedor"] ?>">
                        <button type="submit" class="btn btn-danger btn-sm mt-1" onclick="return confirm('¿Eliminar proveedor?')">
                          <i class="fas fa-trash"></i> Eliminar
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center">No hay proveedores</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
