<footer class="py-5 bg-gris-oscuro text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold mb-3">
                    El Castillo del Pan
                </h5>
                <p class="text-light">
                    Panadería artesanal con más de 10 años de experiencia,
                    ofreciendo productos frescos y de la más alta calidad.
                </p>
            </div>
            <div class="col-lg-2 mb-4">
                <h6 class="fw-bold mb-3">Enlaces</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/') }}" class="text-light text-decoration-none">Inicio</a></li>
                    <li><a href="{{ route('menu') }}" class="text-light text-decoration-none">Menú</a></li>
                    <li><a href="{{ route('carrito.index') }}" class="text-light text-decoration-none">Carrito</a></li>
                    <li><a href="#" class="text-light text-decoration-none">Contacto</a></li>
                </ul>
            </div>
            <div class="col-lg-3 mb-4">
                <h6 class="fw-bold mb-3">Contacto</h6>
                <p class="text-light mb-2">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    123 Calle Principal, Ciudad
                </p>
                <p class="text-light mb-2">
                    <i class="fas fa-phone me-2"></i>
                    (555) 123-4567
                </p>
                <p class="text-light mb-2">
                    <i class="fas fa-envelope me-2"></i>
                    info@elcastillodelpan.com
                </p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-light mb-0">
                    &copy; 2024 El Castillo del Pan. Todos los derechos reservados.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-light">
                    Hecho con <i class="fas fa-heart text-danger"></i> para nuestros clientes
                </small>
            </div>
        </div>
    </div>
</footer>
