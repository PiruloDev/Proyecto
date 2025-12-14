<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'El Castillo del Pan')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logoprincipal.jpg') }}">

    {{-- 🔥 CORRECCIÓN CLAVE: AGREGAR TOKEN CSRF 🔥 --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    @stack('styles')

    @yield('extra-css')
</head>
<body class="@yield('body-class', 'bg-blanco-cálido')">

    @yield('content')

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Auth Manager -->
    <script src="{{ asset('js/auth/auth-manager.js') }}"></script>
    <script src="{{ asset('js/auth/auth-guard.js') }}"></script>

    <!-- Scripts personalizados -->
    @stack('scripts')

    @yield('extra-js')
</body>
</html>
