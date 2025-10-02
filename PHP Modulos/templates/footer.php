<?php
$isAdmin = $isAdmin ?? false;
?>

<?php if (!$isAdmin): ?>
  </main>
  <footer class="py-5 bg-gris-oscuro text-white mt-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4">
          <h5 class="fw-bold mb-3">El Castillo del Pan</h5>
          <p class="text-light">Panadería artesanal con más de 10 años de experiencia, ofreciendo productos frescos y de la más alta calidad.</p>
        </div>
        <div class="col-lg-2 mb-4">
          <h6 class="fw-bold mb-3">Enlaces</h6>
          <ul class="list-unstyled">
            <li><a href="/GITHUB%20REPO/PHP%20Modulos/views/homepage.php" class="text-light text-decoration-none">Inicio</a></li>
            <li><a href="/GITHUB%20REPO/PHP%20Modulos/views/productosviews/categoriaProductosIndex.php" class="text-light text-decoration-none">Categorías</a></li>
            <li><a href="/GITHUB%20REPO/PHP%20Modulos/productosindex.php" class="text-light text-decoration-none">Productos</a></li>
          </ul>
        </div>
        <div class="col-lg-3 mb-4">
          <h6 class="fw-bold mb-3">Contacto</h6>
          <p class="text-light mb-2"><i class="bi bi-geo-alt-fill me-2"></i>Bogotá - Bosa El Recreo</p>
          <p class="text-light mb-2"><i class="bi bi-phone me-2"></i>(601) 123-4567</p>
          <p class="text-light mb-2"><i class="bi bi-envelope me-2"></i>info@elcastillodelpan.com</p>
        </div>
        <div class="col-lg-3 mb-4">
          <h6 class="fw-bold mb-3">Síguenos</h6>
          <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
        </div>
      </div>
      <hr class="my-4 border-secondary">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="text-light mb-0">&copy; 2025 El Castillo del Pan. Todos los derechos reservados.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <small class="text-light">Hecho con <i class="bi bi-heart-fill text-danger"></i> para nuestros clientes</small>
        </div>
      </div>
    </div>
  </footer>
<?php else: ?>
  </main>
  <footer class="py-3 bg-light text-center border-top">
    <small class="text-muted">&copy; 2025 El Castillo del Pan - Panel de administración</small>
  </footer>
<?php endif; ?>

<!-- Bootstrap JavaScript (al final para que cargue después del DOM) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
