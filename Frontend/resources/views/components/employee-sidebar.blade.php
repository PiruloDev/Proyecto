<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Hamburger Button -->
<button class="btn btn-hamburger d-md-none" type="button" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

{{-- Sidebar Empleado --}}
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="sidebar-content">
        <button class="btn-close-sidebar d-md-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="sidebar-brand">
            <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
            <h5>Portal Empleado</h5>
        </div>
        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.employee') ? 'active' : '' }}" href="{{ route('dashboard.employee') }}">
                    <i class="bi bi-house"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}" href="{{ route('pedidos.index') }}">
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
                <div class="d-flex flex-column">
                    <span id="employee-name" class="fw-bold">Empleado</span>
                    <small id="employee-role" class="text-muted">Empleado</small>
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
    function initEmployeeSidebar() {
        // Actualizar nombre del empleado
        if (typeof AuthManager !== 'undefined' && AuthManager.isAuthenticated()) {
            const userData = AuthManager.getUserData();
            const userRole = AuthManager.getRole();

            const employeeNameElement = document.getElementById('employee-name');
            const employeeRoleElement = document.getElementById('employee-role');

            if (employeeNameElement && userData && userData.nombre) {
                employeeNameElement.textContent = userData.nombre;
            }

            if (employeeRoleElement && userRole) {
                employeeRoleElement.textContent = userRole === 'EMPLEADO' ? 'Empleado' : userRole;
            }
        }

        // Logout
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                    AuthManager.clearAuth();
                    window.location.replace('/');
                }
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
        document.addEventListener('DOMContentLoaded', initEmployeeSidebar);
    } else {
        initEmployeeSidebar();
    }
})();
</script>
