@extends('layouts.app')

@section('title', 'Gestión de Compras')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('compras.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Compra
    </a>
<div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-1"></i> Exportar Excel
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('compras.exportarTodo', array_merge(request()->except(['tipo']), ['tipo' => 'detallado'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte 
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('compras.exportarDetallado', array_merge(request()->except(['tipo']), ['tipo' => 'detallado'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Detallado
                </a>
            </li>
        </ul>
    </div>
</div>
    <form method="GET" action="{{ route('compras.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control" placeholder="Fecha Inicio">
            </div>
            <div class="col-md-3">
               <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control" placeholder="Fecha Fin">
            </div>
            <div class="col-md-3">
                <select name="proveedor_id" class="form-select">
                    <option value="">Seleccionar Proveedor</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->Id }}" {{ request('proveedor_id') == $proveedor->Id ? 'selected' : '' }}>{{ $proveedor->RazonSocial }}</option>
                    @endforeach
                </select>

            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compras as $compra)
            <tr>
                <td>{{ $compra->Id }}</td>
                <td>{{ $compra->proveedor->RazonSocial }}</td>
                <td>{{ $compra->FechaCompra ? $compra->FechaCompra->format('d/m/Y') : 'Fecha no disponible' }}</td>
                <td>{{ number_format($compra->Total, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $compra->Activo ? 'success' : 'danger' }}">
                        {{ $compra->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('compras.show', $compra->Id) }}" class="btn btn-info btn-sm" title="Ver">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('compras.edit', $compra->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('compras.toggle', $compra->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $compra->Estado ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $compra->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($compras->isEmpty())
    <div class="alert alert-info text-center">
        No hay compras registradas.
    </div>
@endif
@endsection