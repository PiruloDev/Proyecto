<?php
/**
 * Configuración de endpoints para autenticación JWT
 * Define las URLs del backend para login y validación de tokens
 */
class endpointAuth {
    const API_LOGIN = 'http://localhost:8080/auth/login';
    const API_VALIDAR_TOKEN = 'http://localhost:8080/auth/validar';
}
?>