<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    error_log("Perfil Cliente - Sesión inválida: " . json_encode($_SESSION));
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
            $stmt = $conexion->prepare("UPDATE Clientes SET NOMBRE_CLI = ?, TELEFONO_CLI = ?, EMAIL_CLI = ? WHERE ID_CLIENTE = ?");
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

// Obtener datos actuales del cliente
$cliente_data = null;
try {
    // Verificar que tenemos el ID de usuario
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("ID de usuario no encontrado en la sesión");
    }
    
    $stmt = $conexion->prepare("SELECT * FROM Clientes WHERE ID_CLIENTE = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente_data = $result->fetch_assoc();
    
    if (!$cliente_data) {
        throw new Exception("No se encontraron datos del cliente");
    }
} catch (Exception $e) {
    error_log("Error en perfil_cliente.php: " . $e->getMessage());
    $mensaje = "Error al cargar datos del perfil: " . $e->getMessage();
    $tipo_mensaje = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #bb9467 0%, #a07c4a 100%);
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
            background: linear-gradient(135deg, #bb9467, #a07c4a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: white;
            box-shadow: 0 8px 25px rgba(187, 148, 103, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #bb9467, #a07c4a);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(187, 148, 103, 0.4);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #bb9467;
            box-shadow: 0 0 0 0.2rem rgba(187, 148, 103, 0.25);
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
                <span class="fw-bold" style="color: #bb9467;">El Castillo del Pan</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="menu.php">
                    <i class="fas fa-bread-slice me-1"></i>Menú
                </a>
                <a class="nav-link" href="pedidos.php">
                    <i class="fas fa-shopping-cart me-1"></i>Mis Pedidos
                </a>
                <a class="nav-link" href="dashboard_cliente.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
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
                <a href="dashboard_cliente.php" class="btn btn-light">
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
                                <i class="fas fa-user"></i>
                            </div>
                            <h3 class="mb-0" style="color: #bb9467;"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h3>
                            <p class="text-muted">Cliente de El Castillo del Pan</p>
                        </div>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label fw-bold">
                                        <i class="fas fa-user me-2" style="color: #bb9467;"></i>Nombre Completo
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($cliente_data['NOMBRE_CLI'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label fw-bold">
                                        <i class="fas fa-phone me-2" style="color: #bb9467;"></i>Teléfono
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($cliente_data['TELEFONO_CLI'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2" style="color: #bb9467;"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($cliente_data['EMAIL_CLI'] ?? ''); ?>">
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
        // Configurar navegación para perfil de cliente
        document.addEventListener('DOMContentLoaded', function() {
            if (window.NavSystem) {
                window.NavSystem.setDebugMode(false);
            }
            
            console.log('Perfil de cliente cargado correctamente');
        });
    </script>
</body>
</html>
