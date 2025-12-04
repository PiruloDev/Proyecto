<div class="col-md-3 col-lg-2 sidebar bg-dark text-white p-3">
    <h5 class="text-warning">Módulo de Pedidos</h5>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link text-white {{ request()->is('pedidos') ? 'active fw-bold' : '' }}"
               href="{{ route('pedidos.index') }}">
                <i class="fas fa-list"></i> Pedidos de Clientes
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white {{ request()->is('pedidos/estados') ? 'active fw-bold' : '' }}"
               href="{{ route('pedidos.estados') }}">
                <i class="fas fa-check-circle"></i> Estados de Pedido
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white {{ request()->is('pedidos/proveedores') ? 'active fw-bold' : '' }}"
               href="{{ route('pedidos.proveedores') }}">
                <i class="fas fa-truck"></i> Pedidos a Proveedores
            </a>
        </li>
    </ul>
</div>
