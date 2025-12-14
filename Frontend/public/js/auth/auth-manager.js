const AuthManager = {
    TOKEN_KEY: 'access_token',
    USER_KEY: 'user_data',
    REFRESH_INTERVAL: 30 * 60 * 1000,
    refreshTimer: null,

    saveToken(token) {
        sessionStorage.setItem(this.TOKEN_KEY, token);
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
        this.clearAuth();

        history.pushState(null, null, location.href);
        window.location.replace('/');
    },

    init() {
        if (this.isAuthenticated()) {
            this.startRefreshTimer();
            console.log('AuthManager inicializado con sesión activa');
        }

        window.addEventListener('beforeunload', () => {
            this.stopRefreshTimer();
        });

        history.pushState(null, null, location.href);
        window.addEventListener('popstate', () => {
            history.pushState(null, null, location.href);
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AuthManager.init();
});

if (typeof window !== 'undefined') {
    window.AuthManager = AuthManager;
}
