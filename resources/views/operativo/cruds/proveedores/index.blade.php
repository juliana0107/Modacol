@extends('operativo.layout')

@section('title', 'Gestión de Proveedores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Proveedor
        </a>

        <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-1"></i> Exportar Excel
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('proveedores.export.all', array_merge(request()->except(['tipo']), ['tipo' => 'completo'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Completo
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="{{ route('proveedores.export.filtered', array_merge(request()->except(['tipo']), ['tipo' => 'detallado'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Detallado
                </a>
            </li>
        </ul>
    </div>
</div>
        <form method="GET" action="{{ route('proveedores.index') }}" class="row mb-3 g-2">
            <div class="col-md-3">
                <input type="text" name="RazonSocial" value="{{ $filters['RazonSocial'] ?? '' }}"
                    class="form-control" placeholder="Buscar por Razón Social">
            </div>
            <div class="col-md-2">
                <select name="Activo" class="form-select">
                    <option value="">-- Estado --</option>
                    <option value="1" {{ ($filters['Activo'] ?? '')==='1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ ($filters['Activo'] ?? '')==='0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="Correo" value="{{ $filters['Correo'] ?? '' }}"
                    class="form-control" placeholder="Buscar por Correo">
            </div>
            <div class="col-md-2">
                <input type="text" name="Direccion" value="{{ $filters['Direccion'] ?? '' }}"
                    class="form-control" placeholder="Buscar por Dirección">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Razón Social</th>
                <th>Identificación</th>
                <th>Dirección</th>
                <th>Correo</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td>{{ $proveedor->Id }}</td>
                <td>{{ $proveedor->RazonSocial }}</td>
                <td>{{ $proveedor->Identificacion }}</td>
                <td>{{ $proveedor->Direccion }}</td>
                <td>{{ $proveedor->Correo }}</td>
                <td>{{ $proveedor->Contacto }}</td>
                <td>
                    <span class="badge bg-{{ $proveedor->Activo ? 'success' : 'danger' }}">
                        {{ $proveedor->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('proveedores.edit', $proveedor->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('proveedores.toggle', $proveedor->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $proveedor->Activo ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $proveedor->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($proveedores->isEmpty())
    <div class="alert alert-info text-center">
        No hay proveedores registrados.
    </div>
@endif
@endsection