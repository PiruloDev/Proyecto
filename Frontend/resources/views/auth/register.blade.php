@extends('layouts.app')

@section('title', 'Registro de Cliente - Panadería')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styleregister.css') }}">
@endpush

@section('body-class', '')

@section('content')
<div class="registro-container">
    <!-- Formulario de registro -->
    <div class="registro-left">
        <div class="logo-registro">
            <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="logo-img-registro">
        </div>
        <h1 class="titulo-registro">Registro Cliente</h1>
    </div>
    
    <div class="registro-right">
        @if($errors->any())
            <div class="error-message-registro" style="display: block;">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="form-group-registro">
                <label for="nombre" class="label-registro">Nombre Completo <span style="color: #F44336;">*</span></label>
                <input type="text" 
                       id="nombre" 
                       name="nombre" 
                       class="input-registro"
                       value="{{ old('nombre') }}"
                       required
                       maxlength="100"
                       placeholder="Ingresa tu nombre completo">
            </div>
            
            <div class="form-group-registro">
                <label for="email" class="label-registro">Correo Electrónico <span style="color: #F44336;">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="input-registro"
                       value="{{ old('email') }}"
                       required
                       maxlength="150"
                       placeholder="ejemplo@correo.com">
            </div>
            
            <div class="form-group-registro">
                <label for="telefono" class="label-registro">Teléfono (opcional)</label>
                <input type="text" 
                       id="telefono" 
                       name="telefono" 
                       class="input-registro"
                       value="{{ old('telefono') }}"
                       maxlength="20"
                       placeholder="Ej: +52 123 456 7890">
            </div>
            
            <div class="form-group-registro">
                <label for="password" class="label-registro">Contraseña <span style="color: #F44336;">*</span></label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="input-registro"
                       required
                       minlength="6"
                       maxlength="255"
                       placeholder="Mínimo 6 caracteres">
                <small style="color: var(--text-light); font-size: 12px; margin-top: 5px; display: block;">Mínimo 6 caracteres</small>
            </div>
            
            <div class="form-group-registro">
                <label for="confirm_password" class="label-registro">Confirmar Contraseña <span style="color: #F44336;">*</span></label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="input-registro"
                       required
                       minlength="6"
                       maxlength="255"
                       placeholder="Repite tu contraseña">
            </div>
            
            <button type="submit" class="btn-registro">Registrarme</button>
        </form>
        
        <div class="footer-links-registro">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </div>
    </div>
</div>

<!-- Modal de registro exitoso -->
<div id="modalExitoso" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <div class="check-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="12" fill="#28a745"/>
                    <path d="m9 12 2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2>¡Registro Exitoso!</h2>
        </div>
        <div class="modal-body">
            <p>Tu cuenta ha sido creada exitosamente.</p>
            <p>Ahora puedes iniciar sesión con tus credenciales.</p>
        </div>
        <div class="modal-footer">
            <button onclick="irAlLogin()" class="btn-modal">Ir al Login</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Función para mostrar el aviso de registro exitoso
    function mostrarAvisoExitoso() {
        const modal = document.getElementById('modalExitoso');
        modal.style.display = 'flex';
        
        // Auto-cerrar después de 5 segundos si el usuario no hace click
        setTimeout(function() {
            irAlLogin();
        }, 5000);
    }
    
    // Función para redirigir al login
    function irAlLogin() {
        window.location.href = "{{ route('login') }}";
    }
    
    // Validación de contraseñas coincidentes
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica.');
                confirmPassword.focus();
            }
        });
    });
    
    @if(session('success'))
        mostrarAvisoExitoso();
    @endif
</script>
@endpush
