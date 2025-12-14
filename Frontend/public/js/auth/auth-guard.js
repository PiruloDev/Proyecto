document.addEventListener('DOMContentLoaded', function() {
    const isLoginPage = window.location.pathname === '/login';
const isRegisterPage = window.location.pathname === '/register';
const isAuthRequiredPage = window.location.pathname === '/acceso-requerido'; // <--- NUEVA LÍNEA
const isPublicPage = window.location.pathname === '/' ||
                         window.location.pathname === '/menu' ||
                         window.location.pathname.startsWith('/productos') ||
                         isAuthRequiredPage;

    if (!isLoginPage && !isRegisterPage && !isPublicPage) {
        if (!AuthManager.isAuthenticated()) {
            console.warn('Usuario no autenticado, redirigiendo al login');
            AuthManager.redirectToLogin();
            return;
        }

        const currentPath = window.location.pathname;
        const userRole = AuthManager.getRole();
        const expectedDashboard = AuthManager.getDashboardRoute(userRole);

        if (currentPath.includes('/dashboard') && !currentPath.includes(expectedDashboard)) {
            console.warn('Usuario intentando acceder a dashboard no autorizado');
            window.location.href = expectedDashboard;
        }
    }

    if ((isLoginPage || isRegisterPage) && AuthManager.isAuthenticated()) {
        const userRole = AuthManager.getRole();
        const dashboard = AuthManager.getDashboardRoute(userRole);
        console.log('Usuario ya autenticado, redirigiendo al dashboard');
        window.location.href = dashboard;
    }
});

window.addEventListener('popstate', function(event) {
    const isDashboardPage = window.location.pathname.includes('/dashboard');
    const isLoginPage = window.location.pathname === '/login';
    const isHomePage = window.location.pathname === '/';

    if (isDashboardPage && !AuthManager.isAuthenticated()) {
        console.log('Intento de acceso a dashboard sin autenticación, bloqueando...');
        window.location.replace('/login');
        return;
    }

    if (isDashboardPage && AuthManager.isAuthenticated()) {
        console.log('Navegación hacia atrás detectada desde dashboard, cerrando sesión...');
        AuthManager.clearAuth();
        window.location.replace('/');
        return;
    }

    if (isLoginPage && AuthManager.isAuthenticated()) {
        console.log('Navegación hacia atrás al login, cerrando sesión...');
        AuthManager.clearAuth();
    }

    if (isHomePage) {
        AuthManager.clearAuth();
        console.log('Regreso al homepage, sesión limpiada');
    }
});

window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const isDashboardPage = window.location.pathname.includes('/dashboard');

        if (isDashboardPage) {
            if (!AuthManager.isAuthenticated()) {
                console.log('Cache del navegador detectado sin autenticación, redirigiendo...');
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
