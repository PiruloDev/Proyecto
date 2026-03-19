@extends('layouts.app')

@section('title', 'Recuperar Contraseña - Panadería')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styleresetpassword.css') }}">
@endpush

@section('body-class', '')

@section('content')
<div class="reset-container">
    <div class="logo animated-logo">
        <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="logo-img">
    </div>
    <h1>Recuperar Contraseña</h1>

    <div id="errorMessage" class="error-message"></div>
    <div id="successMessage" class="success-message"></div>

    {{-- PASO 1: Verificar email --}}
    <div id="step1">
        <p class="step-description">Ingresa tu correo electrónico para verificar tu cuenta.</p>

        <form id="emailForm">
            @csrf
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com">
            </div>

            <button type="submit" class="btn btn-panaderia-action">
                <span id="btnVerifyText">Verificar Correo</span>
            </button>

            <div id="loadingVerify" class="loading">
                <div class="loading-spinner"></div>
                <p>Verificando correo...</p>
            </div>
        </form>
    </div>

    {{-- PASO 2: Nueva contraseña --}}
    <div id="step2" style="display: none;">
        <p class="step-description">
            <i class="fas fa-check-circle" style="color: #4caf50;"></i>
            Correo verificado: <strong id="verifiedEmail"></strong>
        </p>

        <form id="passwordForm">
            @csrf
            <div class="form-group">
                <label for="newPassword">Nueva Contraseña:</label>
                <div class="password-input-wrapper">
                    <input type="password" id="newPassword" name="newPassword" required placeholder="Mínimo 6 caracteres" minlength="6">
                    <button type="button" class="toggle-password" data-target="newPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirmar Contraseña:</label>
                <div class="password-input-wrapper">
                    <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Repita la contraseña" minlength="6">
                    <button type="button" class="toggle-password" data-target="confirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div id="passwordStrength" class="password-strength"></div>

            <button type="submit" class="btn">
                <span id="btnResetText">Cambiar Contraseña</span>
            </button>

            <div id="loadingReset" class="loading">
                <div class="loading-spinner"></div>
                <p>Actualizando contraseña...</p>
            </div>
        </form>
    </div>

    <div class="footer-links">
        <div class="back">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let verifiedEmail = '';

    // PASO 1: Verificar email
    document.getElementById('emailForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        hideMessages();

        const email = document.getElementById('email').value.trim();

        if (!email) {
            showError('Por favor ingresa tu correo electrónico');
            return;
        }

        if (!isValidEmail(email)) {
            showError('Ingresa un correo electrónico válido');
            return;
        }

        showLoading('loadingVerify', true);
        setButtonText('btnVerifyText', 'Verificando...');

        try {
            const response = await fetch('/api/recuperar-contrasena/validate-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (data.success) {
                verifiedEmail = email;
                document.getElementById('verifiedEmail').textContent = email;
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
                showSuccess('Correo verificado. Ingresa tu nueva contraseña.');
            } else {
                showError(data.mensaje || 'El correo no está registrado en el sistema');
            }
        } catch (error) {
            showError('Error de conexión con el servidor');
        } finally {
            showLoading('loadingVerify', false);
            setButtonText('btnVerifyText', 'Verificar Correo');
        }
    });

    // PASO 2: Cambiar contraseña
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        hideMessages();

        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!newPassword || !confirmPassword) {
            showError('Por favor completa ambos campos');
            return;
        }

        if (newPassword.length < 6) {
            showError('La contraseña debe tener al menos 6 caracteres');
            return;
        }

        if (newPassword !== confirmPassword) {
            showError('Las contraseñas no coinciden');
            return;
        }

        showLoading('loadingReset', true);
        setButtonText('btnResetText', 'Actualizando...');

        try {
            const response = await fetch('/api/recuperar-contrasena/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    email: verifiedEmail,
                    password: newPassword
                })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('step2').style.display = 'none';
                showSuccess('¡Contraseña actualizada correctamente! Redirigiendo al login...');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 2500);
            } else {
                showError(data.mensaje || 'Error al actualizar la contraseña');
            }
        } catch (error) {
            showError('Error de conexión con el servidor');
        } finally {
            showLoading('loadingReset', false);
            setButtonText('btnResetText', 'Cambiar Contraseña');
        }
    });

    // Toggle visibilidad de contraseña
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Indicador de fortaleza de contraseña
    document.getElementById('newPassword').addEventListener('input', function() {
        const strength = document.getElementById('passwordStrength');
        const val = this.value;

        if (val.length === 0) {
            strength.innerHTML = '';
            return;
        }

        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { label: 'Muy débil', color: '#f44336', width: '20%' },
            { label: 'Débil', color: '#ff9800', width: '40%' },
            { label: 'Regular', color: '#ffc107', width: '60%' },
            { label: 'Buena', color: '#8bc34a', width: '80%' },
            { label: 'Fuerte', color: '#4caf50', width: '100%' }
        ];

        const level = levels[Math.min(score, levels.length) - 1] || levels[0];

        strength.innerHTML = `
            <div class="strength-bar">
                <div class="strength-fill" style="width: ${level.width}; background-color: ${level.color};"></div>
            </div>
            <span class="strength-label" style="color: ${level.color};">${level.label}</span>
        `;
    });

    // Utilidades
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(message) {
        const el = document.getElementById('errorMessage');
        el.textContent = message;
        el.style.display = 'block';
        document.getElementById('successMessage').style.display = 'none';
    }

    function showSuccess(message) {
        const el = document.getElementById('successMessage');
        el.textContent = message;
        el.style.display = 'block';
        document.getElementById('errorMessage').style.display = 'none';
    }

    function hideMessages() {
        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('successMessage').style.display = 'none';
    }

    function showLoading(id, show) {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    }

    function setButtonText(id, text) {
        document.getElementById(id).textContent = text;
    }
</script>
@endpush
