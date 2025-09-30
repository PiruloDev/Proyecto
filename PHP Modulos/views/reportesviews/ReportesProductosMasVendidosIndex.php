<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Más Vendidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8C5A37;
            --primary-hover: #6E462A;
            --text-dark: #3D2C21;
            --text-light: #6B5B51;
            --container-bg: rgba(255, 255, 255, 0.85);
            --shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

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
            z-index: 0;
        }

        .main-content {
            position: relative;
            z-index: 1;
            padding: 20px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--text-dark);
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.6);
            font-size: 3rem;
            text-align: center;
            margin-bottom: 30px;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .producto-card {
            background: var(--container-bg);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .producto-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
            z-index: 1;
        }

        .producto-card:hover::before {
            left: 100%;
        }

        .producto-card > * {
            position: relative;
            z-index: 2;
        }

        .ranking-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            z-index: 3;
        }

        .producto-nombre {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 10px;
            padding-right: 50px;
        }

        .producto-descripcion {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .producto-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 2px;
        }

        .precio {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }

        .filtros {
            background: var(--container-bg);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .btn-filtro {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-filtro:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .nav-link {
            color: var(--text-dark) !important;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>

    <nav class="navbar navbar-expand-lg" style="background: var(--container-bg);">
        <div class="container">
            <a class="navbar-brand" href="?" style="color: var(--text-dark); font-family: 'Playfair Display', serif;">
                Sistema de Reportes
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="?">Dashboard</a>
                <a class="nav-link" href="?route=usuarios">Usuarios</a>
                <a class="nav-link" href="?route=ventas">Ventas</a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <h1>🏆Productos Más Vendidos</h1>

            <!-- Filtros -->
            <div class="filtros">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 style="color: var(--text-dark); margin: 0;">Filtrar resultados:</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <form method="GET" class="d-flex gap-2 justify-content-end">
                            <input type="hidden" name="route" value="productos-mas-vendidos">
                            <select name="limite" class="form-select" style="width: auto;">
                                <option value="5" <?= (($_GET['limite'] ?? 10) == 5) ? 'selected' : '' ?>>Top 5</option>
                                <option value="10" <?= (($_GET['limite'] ?? 10) == 10) ? 'selected' : '' ?>>Top 10</option>
                                <option value="20" <?= (($_GET['limite'] ?? 10) == 20) ? 'selected' : '' ?>>Top 20</option>
                            </select>
                            <button type="submit" class="btn btn-filtro">Aplicar</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Grid de productos -->
            <?php if (!empty($productos) && is_array($productos)): ?>
            <div class="productos-grid">
                <?php foreach ($productos as $index => $producto): ?>
                <div class="producto-card">
                    <div class="ranking-badge"><?= $index + 1 ?></div>
                    
                    <div class="producto-nombre">
                        <?= htmlspecialchars($producto['nombreProducto'] ?? 'Sin nombre') ?>
                    </div>
                    
                    <div class="producto-descripcion">
                        <?= htmlspecialchars($producto['descripcionProducto'] ?? 'Sin descripción') ?>
                    </div>
                    
                    <div class="producto-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= number_format($producto['cantidad_vendida'] ?? 0) ?></span>
                            <div class="stat-label">Vendidos</div>
                        </div>
                        <div class="stat-item">
                            $<?= number_format($producto['precioProducto'] ?? 0, 2) ?>
                            <div class="stat-label">Precio</div>
                        </div>
                        <div class="stat-item">
                            <?= $producto['stockMin'] ?? 0 ?>
                            <div class="stat-label">Stock</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center" style="background: var(--container-bg); border-radius: 15px; padding: 40px;">
                <h4 style="color: var(--text-light);">No hay datos de productos más vendidos</h4>
                <p style="color: var(--text-light);">Verifica que la API esté funcionando correctamente.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>