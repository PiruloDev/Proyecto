const AuthManager = {
    TOKEN_KEY: 'access_token',
    USER_KEY: 'user_data',
    LOGOUT_FLAG: 'logout_flag',
    REFRESH_INTERVAL: 30 * 60 * 1000,
    refreshTimer: null,

    saveToken(token) {
        sessionStorage.setItem(this.TOKEN_KEY, token);
        sessionStorage.removeItem(this.LOGOUT_FLAG); // Limpiar flag de logout al iniciar sesión
        this.startRefreshTimer();
    },

    getToken() {
        return sessionStorage.getItem(this.TOKEN_KEY);
    },

    saveUserData(userData) {
        sessionStorage.setItem(this.USER_KEY, JSON.stringify(userData));
    },

    getUserData() {
        const data = sessionStorage.getItem(this.USER_KEY);
        return data ? JSON.parse(data) : null;
    },

    clearAuth() {
        sessionStorage.removeItem(this.TOKEN_KEY);
        sessionStorage.removeItem(this.USER_KEY);
        this.stopRefreshTimer();
    },

    clearAuthComplete() {
        sessionStorage.removeItem(this.TOKEN_KEY);
        sessionStorage.removeItem(this.USER_KEY);
        sessionStorage.removeItem(this.LOGOUT_FLAG);
        this.stopRefreshTimer();
    },

    markLogout() {
        sessionStorage.setItem(this.LOGOUT_FLAG, 'true');
    },

    wasLoggedOut() {
        return sessionStorage.getItem(this.LOGOUT_FLAG) === 'true';
    },

    isAuthenticated() {
        return !!this.getToken();
    },

    decodeToken(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            return JSON.parse(jsonPayload);
        } catch (error) {
            console.error('Error decoding token:', error);
            return null;
        }
    },

    getRole() {
        const userData = this.getUserData();
        if (userData) {
            return userData.rol || userData.tipoUsuario;
        }

        const token = this.getToken();
        if (token) {
            const decoded = this.decodeToken(token);
            return decoded?.rol || decoded?.tipoUsuario;
        }

        return null;
    },

    getDashboardRoute(rol) {
        const routes = {
            'ADMIN': '/dashboardadmin',
            'ADMINISTRADOR': '/dashboardadmin',
            'EMPLEADO': '/dashboardempleado',
            'CLIENTE': '/dashboardcliente',
            'CLIENT': '/dashboardcliente'
        };

        return routes[rol?.toUpperCase()] || '/';
    },

    async refreshToken() {
        const token = this.getToken();
        if (!token) {
            this.redirectToLogin();
            return false;
        }

        try {
            const response = await fetch('/api/auth/validar', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.valido) {
                console.log('Token validado correctamente');
                return true;
            } else {
                console.warn('Token inválido, redirigiendo al login');
                this.clearAuth();
                this.redirectToLogin();
                return false;
            }
        } catch (error) {
            console.error('Error al refrescar token:', error);
            this.clearAuth();
            this.redirectToLogin();
            return false;
        }
    },

    startRefreshTimer() {
        this.stopRefreshTimer();

        this.refreshTimer = setInterval(() => {
            console.log('Ejecutando refresh automático de token...');
            this.refreshToken();
        }, this.REFRESH_INTERVAL);

        console.log(`Refresh timer iniciado - se ejecutará cada ${this.REFRESH_INTERVAL / 1000 / 60} minutos`);
    },

    stopRefreshTimer() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
            console.log('Refresh timer detenido');
        }
    },

    redirectToLogin() {
        window.location.href = '/login';
    },

    async logout() {
        console.log('Cerrando sesión y limpiando tokens...');

        const token = this.getToken();

        // 1. Invalidar token en el backend (blacklist) y cerrar sesión Laravel
        if (token) {
            try {
                await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                console.log('Token invalidado en el backend (blacklist)');
            } catch (error) {
                console.error('Error al invalidar token en backend:', error);
            }
        }

        // 2. Marcar logout y limpiar todo el almacenamiento local
        this.markLogout();
        this.clearAuth();

        // 3. Limpiar todo sessionStorage y localStorage por seguridad
        sessionStorage.clear();
        sessionStorage.setItem(this.LOGOUT_FLAG, 'true');

        // 4. Redirigir al login directamente
        window.location.replace('/login');
    },

    init() {
        // Si se detecta que se hizo logout, limpiar completamente
        if (this.wasLoggedOut()) {
            this.clearAuthComplete();
            // Siempre redirigir al login después de logout, sin excepciones
            if (window.location.pathname !== '/login') {
                window.location.replace('/login');
                return;
            }
        }

        if (this.isAuthenticated()) {
            this.startRefreshTimer();
            console.log('AuthManager inicializado con sesión activa');
        } else {
            // Si no hay sesión y se intenta acceder a una página protegida
            const protectedPaths = ['/dashboardadmin', '/dashboardempleado', '/dashboardcliente',
                                    '/inventario', '/pedidos', '/empleados', '/clientes',
                                    '/estadisticas', '/reportes', '/admin', '/carrito'];
            if (protectedPaths.some(path => window.location.pathname.startsWith(path))) {
                window.location.replace('/login');
                return;
            }
        }

        window.addEventListener('beforeunload', () => {
            this.stopRefreshTimer();
        });

        // Manejar bfcache: cuando el navegador restaura la página desde caché (botón Atrás)
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                // La página fue restaurada desde bfcache
                if (this.wasLoggedOut() || !this.isAuthenticated()) {
                    console.log('[AuthManager] Página restaurada de caché sin sesión, redirigiendo...');
                    window.location.replace('/login');
                    return;
                }
            }
            // Incluso sin bfcache, verificar estado
            if (this.wasLoggedOut()) {
                this.clearAuthComplete();
                window.location.replace('/login');
            }
        });

        // Prevenir navegación hacia atrás en páginas protegidas
        const protectedPaths = ['/dashboardadmin', '/dashboardempleado', '/dashboardcliente',
                                '/inventario', '/pedidos', '/empleados', '/clientes',
                                '/estadisticas', '/reportes', '/admin', '/carrito'];
        const isProtectedPage = protectedPaths.some(path => window.location.pathname.startsWith(path));

        if (isProtectedPage) {
            // Reemplazar el estado actual para prevenir navegación hacia atrás
            history.pushState(null, null, location.href);

            window.addEventListener('popstate', (event) => {
                if (this.wasLoggedOut() || !this.isAuthenticated()) {
                    history.pushState(null, null, '/login');
                    window.location.replace('/login');
                } else {
                    // Con sesión activa, mantener en la página
                    history.pushState(null, null, location.href);
                }
            });
        } else {
            // En páginas no protegidas, solo manejar si hubo logout
            window.addEventListener('popstate', (event) => {
                if (this.wasLoggedOut()) {
                    window.location.replace('/login');
                }
            });
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AuthManager.init();
});

if (typeof window !== 'undefined') {
    window.AuthManager = AuthManager;
}
