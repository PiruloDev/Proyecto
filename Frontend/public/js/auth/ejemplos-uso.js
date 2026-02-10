// ============================================
// EJEMPLOS DE USO DEL SISTEMA DE AUTENTICACIÓN
// ============================================

// --------------------------------------------
// 1. VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO
// --------------------------------------------
if (AuthManager.isAuthenticated()) {
    console.log('Usuario autenticado');
    const userData = AuthManager.getUserData();
    console.log('Datos del usuario:', userData);
} else {
    console.log('Usuario no autenticado');
    AuthManager.redirectToLogin();
}

// --------------------------------------------
// 2. OBTENER INFORMACIÓN DEL USUARIO
// --------------------------------------------
const token = AuthManager.getToken();
const userData = AuthManager.getUserData();
const userRole = AuthManager.getRole();

console.log('Token:', token);
console.log('Usuario:', userData);
console.log('Rol:', userRole);

// --------------------------------------------
// 3. HACER PETICIONES AUTENTICADAS
// --------------------------------------------

// Opción A: Usando authenticatedFetch (recomendado)
async function obtenerDatosProtegidos() {
    try {
        const response = await authenticatedFetch('/api/datos-protegidos');

        if (!response) {
            // Token inválido, ya redirigió al login
            return;
        }

        const data = await response.json();
        console.log('Datos obtenidos:', data);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Opción B: Manualmente con attachAuthHeaders
async function crearPedido(pedidoData) {
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(pedidoData)
    };

    const authOptions = attachAuthHeaders(options);

    const response = await fetch('/api/pedidos', authOptions);
    const result = await response.json();

    return result;
}

// --------------------------------------------
// 4. CERRAR SESIÓN
// --------------------------------------------

// Opción A: Desde JavaScript
document.getElementById('btnCerrarSesion').addEventListener('click', function(e) {
    e.preventDefault();

    if (confirm('¿Deseas cerrar sesión?')) {
        AuthManager.logout();
    }
});

// Opción B: Automático con data-logout
// Solo agregar el atributo al botón:
// <button data-logout>Cerrar Sesión</button>

// --------------------------------------------
// 5. VERIFICAR ROL DEL USUARIO
// --------------------------------------------
const userRole = AuthManager.getRole();

switch(userRole) {
    case 'ADMIN':
    case 'ADMINISTRADOR':
        console.log('Usuario es administrador');
        // Mostrar opciones de admin
        break;
    case 'EMPLEADO':
        console.log('Usuario es empleado');
        // Mostrar opciones de empleado
        break;
    case 'CLIENTE':
    case 'CLIENT':
        console.log('Usuario es cliente');
        // Mostrar opciones de cliente
        break;
}

// --------------------------------------------
// 6. PROTEGER UNA PÁGINA MANUALMENTE
// --------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Verificar autenticación
    if (!AuthManager.isAuthenticated()) {
        alert('Debes iniciar sesión para acceder a esta página');
        AuthManager.redirectToLogin();
        return;
    }

    // Verificar rol específico
    const userRole = AuthManager.getRole();
    if (userRole !== 'ADMIN' && userRole !== 'ADMINISTRADOR') {
        alert('No tienes permisos para acceder a esta página');
        const correctDashboard = AuthManager.getDashboardRoute(userRole);
        globalThis.location.href = correctDashboard;
        return;
    }

    // Código de la página protegida
    console.log('Acceso autorizado');
});

// --------------------------------------------
// 7. DECODIFICAR TOKEN MANUALMENTE
// --------------------------------------------
const token = AuthManager.getToken();
const decodedToken = AuthManager.decodeToken(token);

console.log('Token decodificado:', decodedToken);
console.log('Email:', decodedToken.sub);
console.log('User ID:', decodedToken.userId);
console.log('Rol:', decodedToken.rol);

// --------------------------------------------
// 8. ACTUALIZAR INFORMACIÓN DEL USUARIO
// --------------------------------------------
async function actualizarPerfil(nuevosDatos) {
    try {
        const response = await authenticatedFetch('/api/perfil/actualizar', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(nuevosDatos)
        });

        if (response && response.ok) {
            const data = await response.json();

            // Actualizar datos guardados localmente
            AuthManager.saveUserData(data.usuario);

            console.log('Perfil actualizado');
            return true;
        }

        return false;
    } catch (error) {
        console.error('Error al actualizar perfil:', error);
        return false;
    }
}

// --------------------------------------------
// 9. VALIDAR TOKEN MANUALMENTE
// --------------------------------------------
async function validarTokenActual() {
    const esValido = await AuthManager.refreshToken();

    if (esValido) {
        console.log('Token válido');
    } else {
        console.log('Token inválido, redirigiendo al login');
    }
}

// --------------------------------------------
// 10. MOSTRAR INFORMACIÓN DEL USUARIO EN UI
// --------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    if (AuthManager.isAuthenticated()) {
        const userData = AuthManager.getUserData();

        // Mostrar nombre del usuario
        const nombreElement = document.getElementById('userName');
        if (nombreElement) {
            nombreElement.textContent = userData.nombre || 'Usuario';
        }

        // Mostrar email
        const emailElement = document.getElementById('userEmail');
        if (emailElement) {
            emailElement.textContent = userData.email || '';
        }

        // Mostrar rol
        const rolElement = document.getElementById('userRole');
        if (rolElement) {
            const rol = userData.rol || userData.tipoUsuario;
            rolElement.textContent = rol || 'Usuario';
        }
    }
});

// --------------------------------------------
// 11. EJEMPLO COMPLETO: FORMULARIO PROTEGIDO
// --------------------------------------------
document.getElementById('formCrearProducto')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    // Verificar autenticación
    if (!AuthManager.isAuthenticated()) {
        alert('Sesión expirada. Por favor inicia sesión nuevamente.');
        AuthManager.redirectToLogin();
        return;
    }

    // Verificar permisos
    const userRole = AuthManager.getRole();
    if (userRole !== 'ADMIN' && userRole !== 'EMPLEADO') {
        alert('No tienes permisos para crear productos');
        return;
    }

    // Obtener datos del formulario
    const formData = {
        nombre: document.getElementById('nombre').value,
        precio: document.getElementById('precio').value,
        categoria: document.getElementById('categoria').value
    };

    try {
        const response = await authenticatedFetch('/api/productos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });

        if (response && response.ok) {
            const result = await response.json();
            alert('Producto creado exitosamente');
            this.reset();
        } else {
            alert('Error al crear el producto');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión');
    }
});

// --------------------------------------------
// 12. MANEJO DE ERRORES 401 (No autorizado)
// --------------------------------------------
async function manejarPeticionProtegida(url, options = {}) {
    try {
        const response = await authenticatedFetch(url, options);

        if (!response) {
            // Ya fue manejado por authenticatedFetch
            return null;
        }

        if (response.status === 401) {
            console.warn('Token expirado');
            AuthManager.clearAuth();
            AuthManager.redirectToLogin();
            return null;
        }

        if (response.status === 403) {
            alert('No tienes permisos para realizar esta acción');
            return null;
        }

        return response;
    } catch (error) {
        console.error('Error en petición:', error);
        throw error;
    }
}
