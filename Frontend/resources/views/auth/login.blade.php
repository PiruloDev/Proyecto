@extends('layouts.app')

@section('title', 'Iniciar Sesión - Panadería')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styleslogin.css') }}">
@endpush

@section('body-class', '')

@section('content')
<div class="login-container">
    <div class="logo animated-logo">
        <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="logo-img">
    </div>
    <h1>Iniciar Sesión</h1>

    <div id="errorMessage" class="error-message"></div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('logout'))
        <div class="success-message">
            Sesión cerrada correctamente. Gracias por usar nuestro sistema.
        </div>
    @endif

    @if($errors->any())
        <div class="error-message">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form id="loginForm">
        @csrf
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
        </div>

        <div class="forgot-password-link">
            <a href="{{ route('reset-password') }}">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn">
            <span id="btnText">Iniciar Sesión</span>
        </button>

        <div id="loading" class="loading">
            <div class="loading-spinner"></div>
            <p>Validando credenciales...</p>
        </div>
    </form>

    <!-- Enlace de registro separado y alineado a la izquierda -->
    <div class="signup-link">
        <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate como cliente</a>
    </div>
    <!-- Enlace de regreso -->
    <div class="footer-links">
        <div class="back">
            <a href="{{ url('/') }}">Regresar al Inicio</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btnText = document.getElementById('btnText');
        const loading = document.getElementById('loading');
        const errorMessage = document.getElementById('errorMessage');

        errorMessage.style.display = 'none';
        btnText.textContent = 'Validando...';
        loading.style.display = 'block';

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        if (!email || !password) {
            showError('Por favor complete todos los campos');
            resetButton();
            return;
        }

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            console.log('=== RESPUESTA DEL LOGIN ===');
            console.log('Status:', response.status);
            console.log('Data completa:', data);
            console.log('Success:', data.success);
            console.log('Token:', data.token);
            console.log('Usuario:', data.usuario);

            if (data.success && data.token) {
                console.log('Login exitoso, guardando token...');
                AuthManager.saveToken(data.token);

                if (data.usuario) {
                    console.log('Guardando datos de usuario:', data.usuario);
                    AuthManager.saveUserData(data.usuario);

                    const rol = data.usuario.rol || data.usuario.tipoUsuario;
                    console.log('Rol detectado:', rol);

                    const dashboardRoute = AuthManager.getDashboardRoute(rol);
                    console.log('Redirigiendo a:', dashboardRoute);

                    window.location.href = dashboardRoute;
                } else {
                    console.error('No se recibieron datos del usuario');
                    showError('Error al procesar los datos del usuario');
                    resetButton();
                }
            } else {
                console.error('Login fallido:', data.mensaje);
                showError(data.mensaje || 'Error al iniciar sesión');
                resetButton();
            }
        } catch (error) {
            console.error('Error en fetch:', error);
            showError('Error de conexión con el servidor');
            resetButton();
        }
    });

    function showError(message) {
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }

    function resetButton() {
        const btnText = document.getElementById('btnText');
        const loading = document.getElementById('loading');

        if (btnText) {
            btnText.textContent = 'Iniciar Sesión';
        }
        if (loading) {
            loading.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        resetButton();
    });
</script>
@endpush
