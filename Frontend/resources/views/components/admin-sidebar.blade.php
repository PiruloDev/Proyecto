<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Hamburger Button -->
<button class="btn btn-hamburger d-md-none" type="button" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar Administrador -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="sidebar-content">
        <button class="btn-close-sidebar d-md-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
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
                <a class="nav-link {{ request()->routeIs('reportes.*') || request()->routeIs('ordenes.salida.*') ? 'active' : '' }}"
                   href="{{ route('ordenes.salida.index') }}">
                    <i class="bi bi-receipt"></i>
                    Órdenes de Salida
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
                <a class="nav-link {{ request()->routeIs('estadisticas.*') ? 'active' : '' }}"
                   href="{{ route('estadisticas.index') }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    Estadísticas
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
            <button type="button" class="logout-btn" id="logoutBtn" data-logout>
                <i class="bi bi-box-arrow-right"></i>
                Cerrar Sesión
            </button>
        </div>
    </div>
</nav>
<script>
(function() {
    function initAdminSidebar() {
        // Actualizar nombre del administrador
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

        // Logout
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                AuthManager.logout();
            });
        }

        // Sidebar toggle mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function openSidebar() {
            if (sidebar) sidebar.classList.add('show');
            if (sidebarOverlay) sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('show');
            if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);

        // Cerrar sidebar al hacer click en nav-link (mobile)
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) closeSidebar();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminSidebar);
    } else {
        initAdminSidebar();
    }
})();
</script>
