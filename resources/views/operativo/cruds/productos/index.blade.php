@extends('operativo.layout')

@section('title', 'Gestión de Productos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('productos.create') }}" class="btn btn-primary me-2" title="Nuevo Producto">
        <i class="fas fa-plus me-1"></i> Nuevo Producto
    </a>
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Exportar">
            <i class="fas fa-download me-1"></i> Exportar
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('productos.exportar.excel', array_merge(request()->all(), ['tipo' => 'completo'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Completo
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('productos.exportar.excel', array_merge(request()->all(), ['tipo' => 'detallado'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Detallado
                </a>
            </li>
        </ul>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

        <form action="{{ route('productos.index') }}" method="GET" class="row mb-3 g-2">
            @csrf
                <!-- Campo Nombre -->
            <div class="row">
                <div class="col-md-3">
                    <label for="Nombre">Nombre:</label>
                    <input type="text" name="Nombre" value="{{ request('Nombre') }}" id="Nombre" class="form-control form-control-sm">
                </div>

                <!-- Campo Activo -->
                <div class="col-md-2">
                    <label for="Activo">Activo:</label>
                    <select name="Activo" id="Activo" class="form-control form-control-sm">
                        <option value="">Seleccione estado</option>
                        <option value="1" {{ request('Activo') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('Activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <!-- Campo Precio Mínimo -->
                <div class="col-md-3">
                    <label for="PrecioMin">Precio Mínimo:</label>
                    <input type="number" step="0.01" name="PrecioMin" value="{{ request('PrecioMin') }}" id="PrecioMin" class="form-control form-control-sm">
                </div>

                <!-- Campo Precio Máximo -->
                <div class="col-md-2">
                    <label for="PrecioMax">Precio Máximo:</label>
                    <input type="number" step="0.01" name="PrecioMax" value="{{ request('PrecioMax') }}" id="PrecioMax" class="form-control form-control-sm">
                </div>

                <!-- Botón de Filtrado -->
                <div class="col-md-2">
                    <label for="PrecioMax"></label>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i>Filtrar</button>
                </div>                
            </div>
        </form>
<!-- Tabla de Productos -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->Id }}</td>
                <td>{{ $producto->Nombre }}</td>
                <td>{{ Str::limit($producto->Descripcion, 50) }}</td>
                <td>{{ number_format($producto->PrecioU, 2) }}</td>
                <td>{{ $producto->Cantidad }}</td>
                <td>
                    <span class="badge bg-{{ $producto->Activo ? 'success' : 'danger' }}">
                        {{ $producto->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('productos.edit', $producto->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('productos.toggle', $producto->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $producto->Activo ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $producto->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mensaje si no hay productos -->
@if($productos->isEmpty())
    <div class="alert alert-info text-center">
        No hay productos registrados.
    </div>
@endif
@endsection
