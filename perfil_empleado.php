<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar que el usuario esté logueado y sea empleado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'empleado') {
    error_log("Perfil Empleado - Sesión inválida: " . json_encode($_SESSION));
    header('Location: login.php?error=session_invalid');
    exit();
}

require_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_email = trim($_POST['email']);
    
    if (!empty($nuevo_nombre)) {
        try {
            $stmt = $conexion->prepare("UPDATE Empleados SET NOMBRE_EMPLEADO = ?, EMAIL_EMPLEADO = ? WHERE ID_EMPLEADO = ?");
            $stmt->bind_param("ssi", $nuevo_nombre, $nuevo_email, $_SESSION['usuario_id']);
            
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

// Obtener datos actuales del empleado
$empleado_data = null;
try {
    // Verificar que tenemos el ID de usuario
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("ID de usuario no encontrado en la sesión");
    }
    
    $stmt = $conexion->prepare("SELECT * FROM Empleados WHERE ID_EMPLEADO = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $empleado_data = $result->fetch_assoc();
    
    if (!$empleado_data) {
        throw new Exception("No se encontraron datos del empleado");
    }
} catch (Exception $e) {
    error_log("Error en perfil_empleado.php: " . $e->getMessage());
    $mensaje = "Error al cargar datos del perfil: " . $e->getMessage();
    $tipo_mensaje = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            padding: 20px;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: white;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="Homepage.php">
                <img src="../files/img/logoprincipal.jpg" width="40" alt="Logo" class="me-2 rounded-circle">
                <span class="fw-bold" style="color: #28a745;">El Castillo del Pan</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard_empleado.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="productostabla.php">
                    <i class="fas fa-box me-1"></i>Productos
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: white;">
                    <i class="fas fa-user-circle"></i> Mi Perfil
                </h2>
                <a href="dashboard_empleado.php" class="btn btn-light">
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
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="mb-0" style="color: #28a745;"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h3>
                            <p class="text-muted">Empleado de El Castillo del Pan</p>
                        </div>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label fw-bold">
                                        <i class="fas fa-user me-2" style="color: #28a745;"></i>Nombre Completo
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($empleado_data['NOMBRE_EMPLEADO'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2" style="color: #28a745;"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($empleado_data['EMAIL_EMPLEADO'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="estado" class="form-label fw-bold">
                                    <i class="fas fa-info-circle me-2" style="color: #28a745;"></i>Estado
                                </label>
                                <input type="text" class="form-control" id="estado" name="estado" 
                                       value="<?php echo $empleado_data['ACTIVO_EMPLEADO'] ? 'Activo' : 'Inactivo'; ?>" readonly>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/navigation-system.js"></script>
    <script>
        // Configurar navegación para perfil de empleado
        document.addEventListener('DOMContentLoaded', function() {
            if (window.NavSystem) {
                window.NavSystem.setDebugMode(false);
            }
            
            console.log('Perfil de empleado cargado correctamente');
        });
    </script>
</body>
</html>
