{{-- resources/views/inventarioviews/proveedores/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Gestión de Proveedores - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .main-content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 3rem;
            background: linear-gradient(135deg, #fdfbf9 0%, #ffffff 100%);
        }

        .prov-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(141, 110, 99, 0.1) !important;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px !important;
            border-top: 6px solid #5d4037 !important;
        }

        .prov-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(93, 64, 55, 0.1) !important;
        }

        .search-container { position: relative; }
        .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8d6e63;
        }
        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
            height: 50px;
            border: 1px solid #e0e0e0 !important;
        }

        .btn-panaderia {
            background-color: #5d4037 !important;
            color: white !important;
            border-radius: 12px !important;
            padding: 0.6rem 1.5rem !important;
            border: none !important;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: #f4f1ee;
            color: #5d4037;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Estilos para las alertas de retroalimentación */
        .custom-alert {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Personalización de Scroll */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #d7ccc8; border-radius: 10px; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- Retroalimentación: Mensajes de Sesión --}}
            <div class="mt-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show custom-alert d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-3 fa-lg"></i>
                        <div><strong>¡Éxito!</strong> {{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show custom-alert d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                        <div>
                            <strong>Hubo un problema:</strong>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-4">
                <div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary border-0 me-3" style="color: #5d4037; font-size: 1.5rem; transition: transform 0.2s;">
            <i class="fas fa-arrow-left"></i>
        </a>    
                
                <h1 class="h2 fw-bold" style="color: #3e2723;">Socio Proveedores</h1>
                    <p class="text-muted small">Catálogo de contacto y suministros para la panadería.</p>
                </div>
                <button class="btn btn-panaderia shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus-circle me-2"></i> Nuevo Proveedor
                </button>
            </div>

            {{-- Buscador --}}
            <div class="row mb-5">
                <div class="col-md-6 col-lg-5">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="provSearch" class="form-control search-input shadow-sm" placeholder="Buscar por nombre, correo o ID...">
                    </div>
                </div>
            </div>

            {{-- Grid de Cards --}}
            <div class="row g-4" id="proveedoresGrid">
                @forelse($proveedores as $prov)
                    <div class="col-md-6 col-lg-4 prov-item">
                        <div class="card h-100 prov-card border-0 shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="icon-box"><i class="fas fa-truck"></i></div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius: 15px;">
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal-{{ $prov['idProveedor'] }}"><i class="fas fa-edit me-2 text-warning"></i> Editar</button></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('proveedores.destroy', $prov['idProveedor']) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proveedor? Esta acción no se puede deshacer.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-1 text-dark">{{ $prov['nombreProv'] }}</h5>
                                <span class="badge rounded-pill mb-3 {{ $prov['activoProv'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}" style="width: fit-content;">
                                    {{ $prov['activoProv'] ? 'ACTIVO' : 'INACTIVO' }}
                                </span>

                                <div class="contact-details small text-secondary">
                                    <div class="mb-2"><i class="fas fa-phone-alt me-2 text-muted"></i>{{ $prov['telefonoProv'] }}</div>
                                    <div class="mb-2"><i class="fas fa-envelope me-2 text-muted"></i>{{ $prov['emailProv'] }}</div>
                                    <div class="mb-3"><i class="fas fa-map-marker-alt me-2 text-muted"></i>{{ $prov['direccionProv'] ?? 'No registrada' }}</div>
                                </div>

                                <div class="mt-auto pt-3 border-top text-center small fw-bold text-muted">
                                    ID: #{{ $prov['idProveedor'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL EDITAR --}}
                    <div class="modal fade" id="editModal-{{ $prov['idProveedor'] }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header bg-dark text-white border-0" style="border-radius: 20px 20px 0 0;">
                                    <h5 class="modal-title">Editar Proveedor</h5>
                                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('proveedores.update', $prov['idProveedor']) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-12"><label class="form-label fw-bold small">Nombre</label><input type="text" name="nombreProv" class="form-control" value="{{ $prov['nombreProv'] }}" required></div>
                                            <div class="col-md-6"><label class="form-label fw-bold small">Teléfono</label><input type="text" name="telefonoProv" class="form-control" value="{{ $prov['telefonoProv'] }}" required></div>
                                            <div class="col-md-6"><label class="form-label fw-bold small">Estado</label><select name="activoProv" class="form-select"><option value="1" {{ $prov['activoProv'] ? 'selected' : '' }}>Activo</option><option value="0" {{ !$prov['activoProv'] ? 'selected' : '' }}>Inactivo</option></select></div>
                                            <div class="col-12"><label class="form-label fw-bold small">Email</label><input type="email" name="emailProv" class="form-control" value="{{ $prov['emailProv'] }}" required></div>
                                            <div class="col-12"><label class="form-label fw-bold small">Dirección</label><textarea name="direccionProv" class="form-control" rows="2">{{ $prov['direccionProv'] }}</textarea></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-panaderia">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="mb-3"><i class="fas fa-box-open fa-3x text-muted opacity-50"></i></div>
                        <h5 class="text-muted">Aún no hay proveedores registrados</h5>
                        <button class="btn btn-sm btn-outline-secondary mt-2" data-bs-toggle="modal" data-bs-target="#crearModal">Registrar el primero</button>
                    </div>
                @endforelse
            </div>

            {{-- Retroalimentación: Mensaje de búsqueda no encontrada --}}
            <div id="noResults" class="col-12 text-center py-5 d-none">
                <div class="mb-3"><i class="fas fa-search fa-3x text-muted opacity-50"></i></div>
                <h5 class="text-muted">No encontramos proveedores que coincidan con tu búsqueda</h5>
                <p class="small text-muted">Intenta con otros términos o el ID del proveedor.</p>
            </div>

        </main>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="crearModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header text-white border-0" style="background-color: #5d4037; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title">Registrar Proveedor</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('proveedores.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label fw-bold small">Razón Social</label><input type="text" name="nombreProv" class="form-control" placeholder="Ej: Harinas del Norte S.A." required></div>
                        <div class="col-md-6"><label class="form-label fw-bold small">Teléfono</label><input type="text" name="telefonoProv" class="form-control" placeholder="Ej: 3001234567" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold small">Estado</label><select name="activoProv" class="form-select"><option value="1" selected>Activo</option><option value="0">Inactivo</option></select></div>
                        <div class="col-12"><label class="form-label fw-bold small">Email</label><input type="email" name="emailProv" class="form-control" placeholder="correo@ejemplo.com" required></div>
                        <div class="col-12"><label class="form-label fw-bold small">Dirección</label><textarea name="direccionProv" class="form-control" rows="2" placeholder="Calle 1 #2-3, Ciudad"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-panaderia px-4">Crear Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Buscador con retroalimentación de "No resultados"
    document.getElementById('provSearch').addEventListener('input', function() {
        let query = this.value.toLowerCase();
        let items = document.querySelectorAll('.prov-item');
        let visibleCount = 0;

        items.forEach(item => {
            if (item.innerText.toLowerCase().includes(query)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Mostrar u ocultar mensaje de "No resultados"
        const noResults = document.getElementById('noResults');
        if (visibleCount === 0 && query !== "") {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    });

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush