{{-- Código tomado de tu dashboard-employee.blade.php --}}
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
            <h5>Portal Empleado</h5>
        </div>
        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <div class="nav-item">
                {{-- Enlace a Dashboard --}}
                <a class="nav-link active" href="#dashboard" data-section="dashboard">
                    <i class="bi bi-house"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                {{-- ENLACE CORRECTO: Gestionar Pedidos --}}
                <a class="nav-link" href="{{ route('pedidos.index') }}" data-section="pedidos-index">
                    <i class="bi bi-cart-check"></i>
                    Gestionar Pedidos
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#productos" data-section="productos">
                    <i class="bi bi-box-seam"></i>
                    Ver Productos
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#perfil" data-section="perfil">
                    <i class="bi bi-person-circle"></i>
                    Mi Perfil
                </a>
            </div>
        </ul>

        <div class="sidebar-divider"></div>

        <div class="sidebar-user">
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span>Empleado</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>