@extends('layouts.app')

@section('title', 'Gestión de Ventas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Venta
    </a>
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Exportar">
            <i class="fas fa-download me-1"></i> Exportar
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('ventas.exportarTodo', array_merge(request()->all(), ['tipo' => 'completo'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Completo
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('ventas.exportarDetallado', array_merge(request()->all(), ['tipo' => 'detallado'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Detallado
                </a>
            </li>
        </ul>
    </div>
</div>
    <form method="GET" action="{{ route('ventas.index') }}" class="row g-2">
    <div class="row">
        <div class="col-md-3">
            <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control" placeholder="Fecha Inicio">
        </div>
        <div class="col-md-3">
            <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control" placeholder="Fecha Fin">
        </div>       
        <div class="col-md-2">
            <select name="cliente_id" class="form-select">
                <option value="">Seleccionar Cliente</option>
                    @foreach ($clientes as $cliente)
                <option value="{{ $cliente->Id }}" {{ request('cliente_id') == $cliente->Id ? 'selected' : '' }}>{{ $cliente->Nombre }}</option>
                @endforeach
            </select>
        </div>                
        <div class="col-md-2">
            <select name="Activo" id="Activo" class="form-select">
                <option value="">Seleccione estado</option>
                <option value="1" {{ request('Activo') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ request('Activo') == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        <div class="col-md-2">   
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </div>
    </div>
    </form>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Valor Total</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $venta->Id }}</td>
                <td>{{ $venta->Fecha }}</td>
                <td>{{ $venta->cliente->Nombre ?? 'N/A' }}</td>
                <td>{{ $venta->usuario->Nombre ?? 'N/A' }}</td>
                <td>${{ number_format($venta->ValorTotal, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $venta->Activo ? 'success' : 'danger' }}">
                        {{ $venta->Activo ? 'Activa' : 'Inactiva' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('ventas.edit', $venta->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('ventas.toggle', $venta->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $venta->Activo ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $venta->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($ventas->isEmpty())
    <div class="alert alert-info text-center">
        No hay ventas registradas.
    </div>
@endif
@endsection