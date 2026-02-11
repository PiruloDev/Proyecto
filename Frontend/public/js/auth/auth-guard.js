document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const isLoginPage = currentPath === '/login';
    const isRegisterPage = currentPath === '/register';
    const isAuthRequiredPage = currentPath === '/acceso-requerido';
    const isResetPasswordPage = currentPath === '/recuperar-contrasena';
    const isPublicPage = currentPath === '/' ||
                         currentPath === '/menu' ||
                         currentPath.startsWith('/productos') ||
                         isAuthRequiredPage ||
                         isResetPasswordPage;

    // Si el usuario cerró sesión, limpiar todo y quedar en login
    if (AuthManager.wasLoggedOut()) {
        AuthManager.clearAuthComplete();
        if (!isLoginPage && !isPublicPage && !isRegisterPage) {
            window.location.replace('/login');
            return;
        }
    }

    // Páginas protegidas: verificar autenticación
    if (!isPublicPage && !isLoginPage && !isRegisterPage) {
        if (!AuthManager.isAuthenticated()) {
            console.warn('Usuario no autenticado, redirigiendo al login');
            AuthManager.redirectToLogin();
            return;
        }

        const userRole = AuthManager.getRole();
        const expectedDashboard = AuthManager.getDashboardRoute(userRole);

        if (currentPath.includes('/dashboard') && !currentPath.includes(expectedDashboard)) {
            console.warn('Usuario intentando acceder a dashboard no autorizado');
            window.location.href = expectedDashboard;
        }
    }

    // Si ya está autenticado e intenta ir al login/register, redirigir al dashboard
    if ((isLoginPage || isRegisterPage) && AuthManager.isAuthenticated() && !AuthManager.wasLoggedOut()) {
        const userRole = AuthManager.getRole();
        const dashboard = AuthManager.getDashboardRoute(userRole);
        console.log('Usuario ya autenticado, redirigiendo al dashboard');
        window.location.href = dashboard;
    }
});

// Manejar navegación hacia atrás (popstate)
window.addEventListener('popstate', function(event) {
    const currentPath = window.location.pathname;
    const isDashboardPage = currentPath.includes('/dashboard');

    // Si se hizo logout, siempre redirigir al login
    if (AuthManager.wasLoggedOut()) {
        AuthManager.clearAuthComplete();
        window.location.replace('/login');
        return;
    }

    // Si intenta acceder a dashboard sin autenticación
    if (isDashboardPage && !AuthManager.isAuthenticated()) {
        console.log('Intento de acceso a dashboard sin autenticación, bloqueando...');
        window.location.replace('/login');
        return;
    }
});

// Manejar bfcache del navegador (pageshow)
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const currentPath = window.location.pathname;
        const isDashboardPage = currentPath.includes('/dashboard');
        const isProtectedPage = isDashboardPage ||
                                currentPath.startsWith('/inventario') ||
                                currentPath.startsWith('/pedidos') ||
                                currentPath.startsWith('/empleados') ||
                                currentPath.startsWith('/admin');

        if (isProtectedPage) {
            if (AuthManager.wasLoggedOut() || !AuthManager.isAuthenticated()) {
                console.log('Cache del navegador detectado sin autenticación, redirigiendo...');
                AuthManager.clearAuthComplete();
                window.location.replace('/login');
            }
        }
    }
});

function setupLogoutButton() {
    const logoutButtons = document.querySelectorAll('[data-logout], .btn-logout, #logoutBtn');
    const logoutForms = document.querySelectorAll('.logout-form, form[action*="logout"]');

    logoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                AuthManager.logout();
            }
        });
    });

    logoutForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            console.log('Formulario de logout interceptado');
            AuthManager.clearAuth();

            history.pushState(null, null, location.href);
            window.location.replace('/');
        });
    });
}

document.addEventListener('DOMContentLoaded', setupLogoutButton);

function attachAuthHeaders(options = {}) {
    const token = AuthManager.getToken();

    if (token) {
        options.headers = options.headers || {};
        options.headers['Authorization'] = `Bearer ${token}`;
    }

    return options;
}

async function authenticatedFetch(url, options = {}) {
    const authOptions = attachAuthHeaders(options);

    try {
        const response = await fetch(url, authOptions);

        if (response.status === 401) {
            console.warn('Token expirado o inválido');
            AuthManager.clearAuth();
            AuthManager.redirectToLogin();
            return null;
        }

        return response;
    } catch (error) {
        console.error('Error en petición autenticada:', error);
        throw error;
    }
}

if (typeof window !== 'undefined') {
    window.setupLogoutButton = setupLogoutButton;
    window.attachAuthHeaders = attachAuthHeaders;
    window.authenticatedFetch = authenticatedFetch;
}
