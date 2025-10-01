<?php include __DIR__ . '/../../templates/header.php'; ?>

<div class="container py-4">
  <h1 class="mb-4 text-center">Productos Disponibles</h1>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($productos as $p): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="../images/categoria/<?= strtolower(str_replace(' ', '-', $p["nombreProducto"])) ?>.jpg" 
               class="card-img-top" 
               alt="<?= htmlspecialchars($p["nombreProducto"]) ?>"
               onerror="this.src='../images/categoria/default.jpg'">
          <div class="card-body">
            <h5 class="card-title"><?= $p["nombreProducto"] ?></h5>
            <p class="card-text">
              Precio: $<?= $p["precio"] ?><br>
              Stock: <?= $p["stockMinimo"] ?><br>
              Marca: <?= $p["marcaProducto"] ?><br>
              Vence: <?= $p["fechaVencimiento"] ?>
            </p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
