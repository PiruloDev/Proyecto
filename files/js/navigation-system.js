/**
 * Sistema de Navegación Inteligente para Panadería
 * Maneja la navegación del navegador de manera inteligente, permitiendo
 * navegación normal dentro del sistema mientras protege contra salidas accidentales
 */

(function() {
    'use strict';
    
    // Configuración del sistema
    const config = {
        maxBackAttempts: 3,
        confirmationDelay: 1000,
        debugMode: false
    };
    
    // Variables de estado
    let isNavigatingWithinSystem = false;
    let backButtonAttempts = 0;
    let lastNavigationTime = 0;
    
    // Función de logging para debug
    function log(message, data = null) {
        if (config.debugMode) {
            console.log('[NavSystem]', message, data);
        }
    }
    
    // Detectar si una URL es interna al sistema
    function isInternalUrl(url) {
        try {
            const urlObj = new URL(url, window.location.origin);
            const currentDomain = window.location.hostname;
            const targetDomain = urlObj.hostname;
            
            return targetDomain === currentDomain;
        } catch (error) {
            // Si hay error parseando la URL, asumir que es interna (URL relativa)
            return true;
        }
    }
    
    // Detectar clics en enlaces para determinar navegación interna
    function setupLinkDetection() {
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                if (isInternalUrl(link.href)) {
                    isNavigatingWithinSystem = true;
                    lastNavigationTime = Date.now();
                    log('Navegación interna detectada', link.href);
                    
                    // Resetear flag después de un tiempo
                    setTimeout(() => {
                        isNavigatingWithinSystem = false;
                    }, config.confirmationDelay);
                }
            }
        });
    }
    
    // Manejar el botón atrás del navegador
    function setupBackButtonHandling() {
        // Solo aplicar si estamos en un dashboard o página protegida
        const isProtectedPage = window.location.pathname.includes('dashboard') || 
                               window.location.pathname.includes('perfil') ||
                               window.location.pathname.includes('admin') ||
                               window.location.pathname.includes('empleado') ||
                               window.location.pathname.includes('cliente');
        
        if (!isProtectedPage) {
            log('Página no protegida, navegación libre');
            return;
        }
        
        // Configurar historial inicial
        if (window.history && window.history.replaceState) {
            history.replaceState(null, null, window.location.pathname);
        }
        
        // Manejar popstate
        window.addEventListener('popstate', function(event) {
            backButtonAttempts++;
            const timeSinceLastNavigation = Date.now() - lastNavigationTime;
            
            log('Popstate detectado', {
                attempts: backButtonAttempts,
                isInternal: isNavigatingWithinSystem,
                timeSince: timeSinceLastNavigation
            });
            
            // Si es navegación reciente dentro del sistema, permitirla
            if (isNavigatingWithinSystem || timeSinceLastNavigation < config.confirmationDelay) {
                log('Navegación interna permitida');
                return;
            }
            
            // Solo mostrar confirmación después de múltiples intentos
            if (backButtonAttempts > config.maxBackAttempts) {
                const userType = getUserType();
                const message = `¿Estás seguro de que quieres cerrar la sesión${userType ? ' de ' + userType : ''}?`;
                
                if (confirm(message)) {
                    log('Usuario confirmó salida');
                    window.location.href = 'logout.php';
                } else {
                    log('Usuario canceló salida');
                    backButtonAttempts = 0; // Resetear contador
                    history.pushState(null, null, window.location.pathname);
                }
            } else {
                // Permitir navegación normal las primeras veces
                log('Navegación permitida, intento:', backButtonAttempts);
                history.pushState(null, null, window.location.pathname);
            }
        });
    }
    
    // Obtener tipo de usuario para personalizar mensajes
    function getUserType() {
        const path = window.location.pathname;
        if (path.includes('admin')) return 'administrador';
        if (path.includes('empleado')) return 'empleado';
        if (path.includes('cliente')) return 'cliente';
        return null;
    }
    
    // Limpiar datos sensibles solo al cerrar completamente
    function setupCleanup() {
        window.addEventListener('beforeunload', function(e) {
            // Solo limpiar si realmente está cerrando la página, no navegando
            if (!isNavigatingWithinSystem) {
                log('Preparando limpieza de datos');
                // No limpiar automáticamente sessionStorage para permitir navegación
                // sessionStorage.clear(); // Comentado para permitir navegación normal
            }
        });
    }
    
    // Inicializar el sistema cuando el DOM esté listo
    function initialize() {
        log('Inicializando sistema de navegación inteligente');
        
        setupLinkDetection();
        setupBackButtonHandling();
        setupCleanup();
        
        log('Sistema inicializado correctamente');
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
    
    // Exponer configuración para uso externo si es necesario
    window.NavSystem = {
        config: config,
        setDebugMode: function(enabled) {
            config.debugMode = enabled;
        },
        resetAttempts: function() {
            backButtonAttempts = 0;
            log('Intentos de navegación reseteados');
        }
    };
    
})();
