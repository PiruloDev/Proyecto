<?php
session_start();

// Headers de seguridad para prevenir cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificación de autenticación mejorada
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    // Log de debug para entender el problema
    error_log("Perfil Admin - Sesión inválida: " . json_encode($_SESSION));
    
    // Limpiar sesión si hay datos inconsistentes
    session_destroy();
    header('Location: login.php?error=session_invalid');
    exit();
}

require_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_telefono = trim($_POST['telefono']);
    $nuevo_email = trim($_POST['email']);
    
    if (!empty($nuevo_nombre)) {
        try {
            $stmt = $conexion->prepare("UPDATE Administradores SET NOMBRE_ADMIN = ?, TELEFONO_ADMIN = ?, EMAIL_ADMIN = ? WHERE ID_ADMIN = ?");
            $stmt->bind_param("sssi", $nuevo_nombre, $nuevo_telefono, $nuevo_email, $_SESSION['usuario_id']);
            
            if ($stmt->execute()) {
                $_SESSION['usuario_nombre'] = $nuevo_nombre;
                $mensaje = "Perfil actualizado correctamente";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar el perfil";
                $tipo_mensaje = "error";
            }
        } catch (Exception $e) {
            $mensaje = "Error: " . $e->getMessage();
            $tipo_mensaje = "error";
        }
    }
}

// Obtener datos actuales del administrador
$admin_data = null;
try {
    // Verificar que tenemos el ID de usuario
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("ID de usuario no encontrado en la sesión");
    }
    
    $stmt = $conexion->prepare("SELECT * FROM Administradores WHERE ID_ADMIN = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();
    
    if (!$admin_data) {
        throw new Exception("No se encontraron datos del administrador");
    }
} catch (Exception $e) {
    error_log("Error en perfil_admin.php: " . $e->getMessage());
    $mensaje = "Error al cargar datos del perfil: " . $e->getMessage();
    $tipo_mensaje = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styleadmindst.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: #bb9467;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <a href="Homepage.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="../files/img/logoprincipal.jpg" alt="Logo" class="logo-img">
                    <h4 class="logo-text">El Castillo del Pan</h4>
                </a>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard_admin.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="productostabla.php">
                        <i class="fas fa-box"></i>
                        <span>Productos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pedidos_historial.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Pedidos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="perfil_admin.php">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-circle"></i> Mi Perfil</h2>
            <a href="dashboard_admin.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="mb-0"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h3>
                        <p class="text-muted">Administrador del Sistema</p>
                    </div>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nombre Completo
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($admin_data['NOMBRE_ADMIN'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Teléfono
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?php echo htmlspecialchars($admin_data['TELEFONO_ADMIN'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($admin_data['EMAIL_ADMIN'] ?? ''); ?>">
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/navigation-system.js"></script>
    <script>
        // Configurar navegación para perfil de administrador
        document.addEventListener('DOMContentLoaded', function() {
            // Habilitar debug mode para desarrollo
            if (window.NavSystem) {
                window.NavSystem.setDebugMode(false); // Cambiar a true para debug
            }
            
            console.log('Perfil de administrador cargado correctamente');
        });
    </script>
</body>
</html>
