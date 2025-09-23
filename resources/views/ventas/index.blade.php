@extends('layout')

@section('title', 'Gestión de Ventas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ventas</h2>
    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Venta
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

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