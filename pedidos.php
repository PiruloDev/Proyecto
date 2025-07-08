<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || !$_SESSION['usuario_logueado']) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['usuario_nombre'] ?? '';
$user_role = $_SESSION['usuario_tipo'] ?? '';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Configuración de la base de datos
$host = 'localhost';
$db_name = 'proyectopanaderia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $error_msg = "Error al conectar con la base de datos: " . $e->getMessage();
}

// Calcular totales
$subtotal = 0;
$carrito_items = [];

if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
        $carrito_items[] = $item;
    }
}

$iva = $subtotal * 0.19;
$total = $subtotal + $iva;
?>

<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - El Castillo del Pan</title>
    <link rel="icon" type="image/x-icon" href="../files/img/logoprincipal.jpg">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="stylehomepage.css">
    <link rel="stylesheet" href="stylemenu.css">
    
    <style>
        body {
            background-color: #bb9467 !important;
            font-family: 'Quicksand', sans-serif;
        }
        
        .cart-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .cart-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            background-color: #bb9467;
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .quantity-btn:hover {
            background-color: #a0814e;
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.25rem;
        }
        
        .remove-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .remove-btn:hover {
            background-color: #c82333;
        }
        
        .summary-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            position: sticky;
            top: 2rem;
        }
        
        .btn-finalizar {
            background-color: #bb9467;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-finalizar:hover {
            background-color: #a0814e;
            transform: translateY(-2px);
        }
        
        .btn-continuar {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        
        .btn-continuar:hover {
            background-color: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #bb9467;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 1rem;
        }
        
        .spinner-border {
            color: #bb9467;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center logo" href="Homepage.php">
                    <img src="../files/img/logoprincipal.jpg" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
                    <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-marron fw-semibold" href="Homepage.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-marron fw-semibold" href="menu.php">Menú</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-marron fw-semibold active" href="pedidos.php">Mis Pedidos</a>
                        </li>
                    </ul>
                    <div class="dropdown">
                        <a class="btn btn-user-glass dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-glass">
                            <li><a class="dropdown-item" href="dashboard_cliente.php">
                                <i class="fas fa-user-circle me-2"></i>Mi Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="text-marron fw-bold text-center mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>Mi Carrito de Compras
                    </h1>
                </div>
            </div>

            <?php if (!empty($carrito_items)): ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-container">
                            <h3 class="text-marron fw-bold mb-4">Productos Seleccionados</h3>
                            <div id="cart-items">
                                <?php foreach ($carrito_items as $item): ?>
                                    <div class="cart-item" data-producto-id="<?php echo $item['id']; ?>">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <img src="img/pan-rtzqhi1ok4k1bxlo.jpg" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="cart-item-image">
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="text-marron fw-bold mb-1"><?php echo htmlspecialchars($item['nombre']); ?></h5>
                                                <p class="text-muted small mb-0">Precio unitario: $<?php echo number_format($item['precio'], 0); ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="quantity-input" value="<?php echo $item['cantidad']; ?>" 
                                                           min="1" max="<?php echo $item['stock_disponible']; ?>" 
                                                           onchange="updateQuantity(<?php echo $item['id']; ?>, 0, this.value)">
                                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">Stock: <?php echo $item['stock_disponible']; ?></small>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <p class="fw-bold text-marron mb-1">$<?php echo number_format($item['precio'] * $item['cantidad'], 0); ?></p>
                                                <button class="remove-btn" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="loading" id="loading">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="summary-card">
                            <h4 class="text-marron fw-bold mb-4">Resumen del Pedido</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">$<?php echo number_format($subtotal, 0); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>IVA (19%):</span>
                                <span id="iva">$<?php echo number_format($iva, 0); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold text-marron" id="total">$<?php echo number_format($total, 0); ?></span>
                            </div>
                            
                            <button class="btn-finalizar mb-3" onclick="finalizarPedido()">
                                <i class="fas fa-check-circle me-2"></i>Finalizar Pedido
                            </button>
                            
                            <a href="menu.php" class="btn-continuar">
                                <i class="fas fa-arrow-left me-2"></i>Continuar Comprando
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <div class="cart-container">
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h3 class="text-marron fw-bold mb-3">Tu carrito está vacío</h3>
                                <p class="text-muted mb-4">¡Agrega algunos productos deliciosos a tu carrito!</p>
                                <a href="menu.php" class="btn-continuar">
                                    <i class="fas fa-utensils me-2"></i>Ver Menú
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-5 bg-gris-oscuro text-white mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-bread-slice me-2 text-dorado"></i>
                        El Castillo del Pan
                    </h5>
                    <p class="text-light">
                        Panadería artesanal con más de 10 años de experiencia.
                    </p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <li><a href="Homepage.php" class="text-light text-decoration-none">Inicio</a></li>
                        <li><a href="menu.php" class="text-light text-decoration-none">Menú</a></li>
                        <li><a href="pedidos.php" class="text-light text-decoration-none">Mis Pedidos</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <p class="text-light mb-2">
                        <i class="fas fa-phone me-2"></i>(555) 123-4567
                    </p>
                    <p class="text-light mb-2">
                        <i class="fas fa-envelope me-2"></i>info@elcastillodelpan.com
                    </p>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="text-center">
                <p class="text-light mb-0">
                    &copy; 2024 El Castillo del Pan. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personalizado -->
    <script>
        function updateQuantity(productId, change, newValue = null) {
            let input = document.querySelector(`[data-producto-id="${productId}"] .quantity-input`);
            let currentValue = parseInt(input.value);
            let newQuantity;
            
            if (newValue !== null) {
                newQuantity = parseInt(newValue);
            } else {
                newQuantity = currentValue + change;
            }
            
            if (newQuantity < 1) {
                newQuantity = 1;
            }
            
            if (newQuantity > parseInt(input.max)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: 'No hay suficiente stock disponible',
                    confirmButtonColor: '#bb9467'
                });
                return;
            }
            
            input.value = newQuantity;
            
            // Enviar actualización al servidor
            fetch('procesar_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accion: 'actualizar',
                    producto_id: productId,
                    cantidad: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recargar para actualizar totales
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonColor: '#bb9467'
                    });
                }
            });
        }
        
        function removeFromCart(productId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Quieres eliminar este producto del carrito?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#bb9467',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('procesar_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            accion: 'eliminar',
                            producto_id: productId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                confirmButtonColor: '#bb9467'
                            });
                        }
                    });
                }
            });
        }
        
        function finalizarPedido() {
            Swal.fire({
                title: '¿Confirmar pedido?',
                text: "¿Estás seguro de que quieres finalizar tu pedido?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#bb9467',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('loading').style.display = 'block';
                    
                    fetch('procesar_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            accion: 'finalizar'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loading').style.display = 'none';
                        
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Pedido realizado!',
                                text: `Tu pedido #${data.pedido_id} ha sido procesado exitosamente.`,
                                confirmButtonColor: '#bb9467'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                confirmButtonColor: '#bb9467'
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
