<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Sistema de Panadería</title>

    <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    
</head>
<body>

<div class="container-fluid">
    <div class="row g-0">
        
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="d-flex flex-column">
                <div class="sidebar-header text-center">
                    <h5 class="mb-0">El Castillo del Pan</h5>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="menu.php"><i class="fas fa-home"></i> Dashboard</a> 
                    
                    <a class="nav-link" href="Ingredienteindex.php?modulo=ingredientes&accion=listar"><i class="fas fa-cheese"></i> Ingredientes</a>
                    <a class="nav-link" href="Ingredienteindex.php?modulo=categoria&accion=listar"><i class="fas fa-list-alt"></i> Categorías</a>
                    <a class="nav-link" href="Ingredienteindex.php?modulo=proveedores&accion=listar"><i class="fas fa-truck"></i> Proveedores</a>
                    <a class="nav-link" href="Ingredienteindex.php?modulo=detallePedidos&accion=listar"><i class="fas fa-receipt"></i> Detalle Pedidos</a>
                    
                </nav>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 main-content">
            
            <nav class="navbar navbar-expand-lg top-navbar">
                <div class="container-fluid">
                    <a class="navbar-brand d-md-none" href="menu.php">Menú</a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="me-2 text-dark d-none d-sm-inline">Administrador</span>
                                    <div class="profile-icon-wrapper">
                                        <i class="fas fa-user"></i>
                                    </div>
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

            <div class="welcome-section">
                <h1>Bienvenido al Panel de Control</h1>
                <p class="lead" style="color: var(--text-light);">Usa el menú lateral para navegar entre los módulos de gestión.</p>
                </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>