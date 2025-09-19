@extends('layouts.app')

@section('title', 'Gestión de Compras')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Compras</h2>
    <a href="{{ route('compras.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Compra
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
                <td>{{ $compra->FechaCompra->format('d/m/Y') }}</td>
                <td>${{ number_format($compra->Total, 2) }}</td>
                <td>
                    <span class="badge bg-{{ 
                        $compra->Estado == 'Completada' ? 'success' : 
                        ($compra->Estado == 'Pendiente' ? 'warning' : 'danger') 
                    }}">
                        {{ $compra->Estado }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('compras.show', $compra->Id) }}" class="btn btn-info btn-sm" title="Ver">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('compras.edit', $compra->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
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