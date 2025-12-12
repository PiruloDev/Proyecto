<div class="col-md-3 col-lg-2 sidebar bg-dark text-white p-3">
    <h5 class="text-warning">Módulo de Pedidos</h5>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            {{-- 1. CORRECCIÓN: Usar request()->routeIs('admin.pedidos.*') para verificar la ruta del administrador --}}
            <a class="nav-link text-white {{ request()->routeIs('admin.pedidos.*') ? 'active fw-bold' : '' }}"
               href="{{ route('admin.pedidos.index') }}">
                <i class="fas fa-list"></i> Pedidos de Clientes
            </a>
        </li>

        <li class="nav-item">
            {{-- 2. CORRECCIÓN: Usar route('estados.index') y request()->routeIs('estados.*') --}}
            <a class="nav-link text-white {{ request()->routeIs('estados.*') ? 'active fw-bold' : '' }}"
               href="{{ route('estados.index') }}">
                <i class="fas fa-check-circle"></i> Estados de Pedido
            </a>
        </li>

        <li class="nav-item">
            {{-- 3. CORRECCIÓN: Usar route('pedidoproveedores.index') y request()->routeIs('pedidoproveedores.*') --}}
            <a class="nav-link text-white {{ request()->routeIs('pedidoproveedores.*') ? 'active fw-bold' : '' }}"
               href="{{ route('pedidoproveedores.index') }}">
                <i class="fas fa-truck"></i> Pedidos a Proveedores
            </a>
        </li>
    </ul>
</div>