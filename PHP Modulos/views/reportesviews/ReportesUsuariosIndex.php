<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8C5A37;
            --primary-hover: #6E462A;
            --text-dark: #3D2C21;
            --text-light: #6B5B51;
            --border-color: #D3C5BC;
            --container-bg: rgba(255, 255, 255, 0.75);
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --success-color: #4CAF50;
            --info-color: #2196F3;
            --warning-color: #FF9800;
            --danger-color: #F44336;
            --crema: #f5f5dc;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --border-radius: 10px;
            --transition: all 0.3s ease;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Lato', sans-serif;
            background-image: url('../files/img/pan-rtzqhi1ok4k1bxlo.jpg');
            background-size: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--text-dark);
            margin: 40px 0 20px;
            font-size: 32px;
            text-align: center;
        }

        .usuarios-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            padding: 20px;
        }

        .card {
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
            z-index: 2;
        }

        .card:hover::before {
            left: 100%;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .card p {
            margin: 5px 0;
            color: var(--text-light);
            font-size: 14px;
        }

        .rol {
            margin-top: 10px;
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            color: white;
        }

    </style>
</head>
<body>
    <h1>Reporte de Usuarios</h1>

    <div class="usuarios-container">
        <?php if (!empty($usuarios)): ?>
            <?php foreach ($usuarios as $usuario): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($usuario['nombre']) ?></h2>
                    <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']) ?></p>
                    <span class="rol <?= strtolower(htmlspecialchars($usuario['rol'])) ?>">
                        <?= htmlspecialchars($usuario['rol']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--danger-color);">No hay usuarios disponibles.</p>
        <?php endif; ?>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
