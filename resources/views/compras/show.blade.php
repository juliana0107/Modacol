@extends('layouts.app')

@section('title', 'Detalles de Compra')

@section('content')
<h2>Detalles de Compra #{{ $compra->Id }}</h2>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Proveedor:</strong> {{ $compra->proveedor->RazonSocial }}</p>
                <p><strong>Fecha de Compra:</strong> {{ $compra->FechaCompra->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Estado:</strong> 
                    <span class="badge bg-{{ 
                        $compra->Estado == 'Completada' ? 'success' : 
                        ($compra->Estado == 'Pendiente' ? 'warning' : 'danger') 
                    }}">
                        {{ $compra->Estado }}
                    </span>
                </p>
                <p><strong>Total:</strong> ${{ number_format($compra->Total, 2) }}</p>
            </div>
        </div>
        @if($compra->Observaciones)
            <p><strong>Observaciones:</strong> {{ $compra->Observaciones }}</p>
        @endif
    </div>
</div>

<h4>Productos</h4>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compra->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->Nombre }}</td>
                <td>{{ $detalle->Cantidad }}</td>
                <td>${{ number_format($detalle->PrecioUnitario, 2) }}</td>
                <td>${{ number_format($detalle->Subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>${{ number_format($compra->Total, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="mt-3">
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>
@endsection