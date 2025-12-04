{{-- resources/views/partials/sidebar-inventario.blade.php --}}

<div class="col-md-3 col-lg-2 sidebar">
    <div class="d-flex flex-column">
        <div class="sidebar-header text-center">
            <h5 class="mb-0">El Castillo del Pan</h5>
        </div>
        <nav class="nav flex-column">
            {{-- Usamos la función route() de Laravel para que sean rutas dinámicas --}}
            <a class="nav-link" href="{{ route('dashboard.inventario') }}"><i class="fas fa-home"></i> Dashboard</a>
            
            {{-- Puedes usar la función request()->routeIs() para marcar el enlace activo --}}
            <a class="nav-link @if(request()->routeIs('ingredientes.index')) active @endif" 
               href="{{ route('ingredientes.index') }}">
               <i class="fas fa-cheese"></i> Ingredientes
            </a>
            
            <a class="nav-link @if(request()->routeIs('categorias.index')) active @endif" 
               href="{{ route('categorias.index') }}">
               <i class="fas fa-list-alt"></i> Categorías
            </a>
            
            <a class="nav-link @if(request()->routeIs('proveedores.index')) active @endif" 
               href="{{ route('proveedores.index') }}">
               <i class="fas fa-truck"></i> Proveedores
            </a>
            
            <a class="nav-link @if(request()->routeIs('pedidoproveedores.index')) active @endif" 
               href="{{ route('pedidoproveedores.index') }}">
               <i class="fas fa-receipt"></i> Pedido a Proveedores
            </a>
        </nav>
    </div>
</div>