@extends('layout')

@section('title', 'Editar Venta')

@section('content')
<h2>Editar Venta</h2>

<form method="POST" action="{{ route('ventas.update', $venta->Id) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="Fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="Fecha" name="Fecha" required 
                   value="{{ old('Fecha', $venta->Fecha) }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="Cliente_id" class="form-label">Cliente</label>
            <select class="form-select" id="Cliente_id" name="Cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->Id }}" {{ (old('Cliente_id', $venta->Cliente_id) == $cliente->Id) ? 'selected' : '' }}>
                    {{ $cliente->Nombre }} - {{ $cliente->Empresa }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="Usuario_id" class="form-label">Vendedor</label>
            <select class="form-select" id="Usuario_id" name="Usuario_id" required>
                <option value="">Seleccionar Vendedor</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->Id }}" {{ (old('Usuario_id', $venta->Usuario_id) == $usuario->Id) ? 'selected' : '' }}>
                    {{ $usuario->Nombre }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="ValorTotal" class="form-label">Valor Total</label>
            <input type="number" step="0.01" class="form-control" id="ValorTotal" name="ValorTotal" required 
                   value="{{ old('ValorTotal', $venta->ValorTotal) }}">
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Actualizar
        </button>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection