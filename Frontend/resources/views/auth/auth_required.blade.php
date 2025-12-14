@extends('layouts.app')

@section('title', 'Acceso Requerido - Panadería')

@push('styles')
{{-- Reutilizamos los estilos de login para mantener la consistencia --}}
<link rel="stylesheet" href="{{ asset('css/styleslogin.css') }}">
{{-- Se podría añadir estilos adicionales si el mensaje necesita ser más grande o centrado --}}
@endpush

@section('body-class', '')

@section('content')
<div class="login-container">
    <div class="logo animated-logo">
        {{-- Mantenemos el logo principal --}}
        <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="logo-img">
    </div>
    
    <h1>Acceso Restringido</h1>

    <div class="auth-message-box">
        <h2 style="color: #c0392b; margin-bottom: 10px;">¡Detente un momento!</h2>
        <p>Para poder explorar y adquirir nuestros deliciosos productos, debes estar autenticado en nuestro sistema.</p>
        
        <p>Por favor, "inicia sesión" con tu cuenta existente" o "regístrate" como nuevo cliente.</p>
    </div>

    {{-- Botones de Acción --}}
    <div class="form-group" style="margin-top: 30px;">
        <a href="{{ route('login') }}" class="btn" style="display: block; text-align: center; margin-bottom: 15px; background-color: #27ae60;">
            Iniciar Sesión
        </a>
        
        <a href="{{ route('register') }}" class="btn" style="display: block; text-align: center; background-color: #f39c12;">
            Regístrate Ahora
        </a>
    </div>

    <div class="footer-links" style="margin-top: 20px;">
        <div class="back">
            <a href="{{ url('/') }}">Regresar al Inicio</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- No se necesitan scripts de autenticación para esta vista informativa --}}
@endpush