<header>
    <nav class="navbar navbar-expand-md navbar-light bg-crema shadow-sm animate__animated animate__fadeInDown">
        <div class="container-fluid px-4 px-xl-5">
            <a class="navbar-brand d-flex align-items-center logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logoprincipal.jpg') }}" width="50" alt="Logo El Castillo del Pan" class="me-2 rounded-circle border border-3 border-marron p-1 bg-white">
                <span class="fw-bold text-marron fs-4">El Castillo del Pan</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-marron fw-semibold" href="{{ route('menu') }}" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Menú
                        </a>
                        <ul class="dropdown-menu bg-crema shadow rounded-3 border-0 mt-2" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item text-marron fw-semibold py-2" href="{{ route('menu') }}">Ver menú</a></li>
                        </ul>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-marron fw-semibold" href="{{ route('carrito.index') }}">
                            Carrito
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3 ms-lg-4">
                    <!-- Usuario Autenticado (mostrado con JS) -->
                    <div id="user-dropdown" class="dropdown" style="display: none;">
                        <a class="btn btn-user-glass dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fs-5 me-2"></i><span id="user-name" class="fw-semibold"></span>
                        </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-glass">
                        <li><a class="dropdown-item" href="{{ route('dashboard.cliente') }}">
                            <i class="fas fa-user-circle me-2"></i>Ver perfil
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="#" class="dropdown-item" id="logout-btn">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
                    <!-- Botón Acceder (mostrado con JS) -->
                    <a href="{{ route('login') }}" id="login-btn" class="btn btn-primary btn-rounded fw-bold" style="display: none;">Acceder</a>
                </div>
            </div>
        </div>
    </nav>
</header>
