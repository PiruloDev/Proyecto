<?php
session_start();

$route = $_GET['route'] ?? 'dashboard';

$routes = [
    'usuarios' => [
        'controller' => 'ReportesUsuariosController',
        'file' => 'Controlador/ReportesUsuariosController.php'
    ],
    'ventas' => [
        'controller' => 'ReportesVentasController',
        'file' => 'Controlador/ReportesVentasController.php'
    ],
    'productos' => [
        'controller' => 'ReportesProductosMasVendidosController',
        'file' => 'Controlador/ReportesProductosMasVendidosController.php'
    ]
];

if (isset($routes[$route])) {
    $routeConfig = $routes[$route];
    
    if (file_exists($routeConfig['file'])) {
        require_once $routeConfig['file'];
        
        // Instanciar y ejecutar
        $controllerClass = $routeConfig['controller'];
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            $controller->manejarPeticion();
        } else {
            echo "Error: Controlador no encontrado";
        }
    } else {
        echo "Error: Archivo no encontrado";
    }
    
} else {
    // Dashboard
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Sistema de Reportes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
            background-image: url('https://www.revistamag.com/wp-content/uploads/2024/05/Pan-Freepik.jpg');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Lato', sans-serif;
        }

        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 0;
        }
            .card { 
                background: rgba(255, 255, 255, 0.9); 
                border-radius: 15px; 
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }
            .btn-custom {
                background-color: #8C5A37;
                border: none;
                padding: 15px 30px;
                color: white;
                border-radius: 8px;
                text-decoration: none;
                display: inline-block;
                margin: 10px;
            }
            .btn-custom:hover {
                background-color: #6E462A;
                color: white;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        
        <div class="background-overlay"></div>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card p-4">
                        <h1 class="text-center mb-4" style="color: #3D2C21;">Sistema de Reportes</h1>
                        
                        <div class="text-center">
                            <h3>Selecciona una opción:</h3>
                            
                            <a href="?route=usuarios" class="btn-custom">
                                Reporte de Usuarios
                            </a>
                            
                            <a href="?route=ventas" class="btn-custom">
                                Gestión de Ventas

                            <a href="?route=productos" class="btn-custom">
                                Reporte de Productos
                            </a>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>

