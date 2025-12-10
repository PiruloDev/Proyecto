<!-- Sidebar Administrador -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
            <h5>Portal Administrador</h5>
        </div>

        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}"
                   href="{{ route('dashboard.admin') }}">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.inventario') ? 'active' : '' }}"
                   href="{{ route('dashboard.inventario') }}">
                    <i class="bi bi-boxes"></i>
                    Producción
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}"
                   href="{{ route('pedidos.index') }}">
                    <i class="bi bi-cart-check"></i>
                    Pedidos
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}"
                   href="{{ route('empleados.index') }}">
                    <i class="bi bi-people"></i>
                    Empleados
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
                   href="{{ route('clientes.index') }}">
                    <i class="bi bi-person-badge"></i>
                    Clientes
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('reportes.*') || request()->routeIs('estadisticas.*') ? 'active' : '' }}"
                   href="{{ route('ordenes.salida.index') }}">
                    <i class="bi bi-graph-up"></i>
                    Estadísticas
                </a>
            </div>
            <div class="nav-item">
    <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"
       href="{{ route('productos.index') }}">
        <i class="bi bi-box-seam"></i>
        Productos
    </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-gear"></i>
                    Ajustes
                </a>
            </div>
        </ul>

        <div class="sidebar-divider"></div>

        <div class="sidebar-user">
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span>Administrador</span>
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
