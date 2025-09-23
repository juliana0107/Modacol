@extends('layout')

@section('title', 'Gestión de Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('productos.index', array_merge(request()->all(), ['download' => 1])) }}" class="btn btn-success btn-sm ml-2">Descargar Reporte CSV</a>
    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Producto
    </a>
</div>

<!-- Filtro Multi Criterio -->
<form action="{{ route('productos.index') }}" method="GET" class="mb-4">
    @csrf
    <div class="row align-items-center">
        <!-- Campo Nombre -->
        <div class="col-md-3 mb-2">
            <label for="Nombre">Nombre:</label>
            <input type="text" name="Nombre" value="{{ request('Nombre') }}" id="Nombre" class="form-control form-control-sm">
        </div>

        <!-- Campo Activo -->
        <div class="col-md-3 mb-2">
            <label for="Activo">Activo:</label>
            <select name="Activo" id="Activo" class="form-control form-control-sm">
                <option value="">Seleccione estado</option>
                <option value="1" {{ request('Activo') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ request('Activo') == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <!-- Campo Precio Mínimo -->
        <div class="col-md-3 mb-2">
            <label for="PrecioMin">Precio Mínimo:</label>
            <input type="number" step="0.01" name="PrecioMin" value="{{ request('PrecioMin') }}" id="PrecioMin" class="form-control form-control-sm">
        </div>

        <!-- Campo Precio Máximo -->
        <div class="col-md-3 mb-2">
            <label for="PrecioMax">Precio Máximo:</label>
            <input type="number" step="0.01" name="PrecioMax" value="{{ request('PrecioMax') }}" id="PrecioMax" class="form-control form-control-sm">
        </div>

        <!-- Botón de Filtrado -->
        <div class="col-md-12 text-end mt-3">
            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        </div>
    </div>
</form>

<!-- Mostrar Mensaje de Éxito -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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
                <td>${{ number_format($producto->PrecioU, 2) }}</td>
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
