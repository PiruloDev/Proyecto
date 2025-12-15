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
                    href="{{ route('admin.pedidos.index') }}">
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
                <div class="d-flex flex-column">
                    <span id="admin-name" class="fw-bold">Administrador</span>
                    <small id="admin-role" class="text-muted">Administrador</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>
<script>
// Script para actualizar el nombre del administrador en la sidebar
(function() {
    function updateAdminSidebar() {
        if (typeof AuthManager !== 'undefined' && AuthManager.isAuthenticated()) {
            const userData = AuthManager.getUserData();
            const userRole = AuthManager.getRole();

            const adminNameElement = document.getElementById('admin-name');
            const adminRoleElement = document.getElementById('admin-role');

            if (adminNameElement && userData && userData.nombre) {
                adminNameElement.textContent = userData.nombre;
            }

            if (adminRoleElement && userRole) {
                adminRoleElement.textContent = userRole.charAt(0) + userRole.slice(1).toLowerCase();
            }
        }
    }

    // Ejecutar inmediatamente si el DOM ya está cargado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateAdminSidebar);
    } else {
        updateAdminSidebar();
    }
})();
</script>
