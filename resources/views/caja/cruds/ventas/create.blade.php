@extends('layouts.app')

@section('title', 'Crear Nueva Venta')

@section('content')
<h2>Nueva Venta</h2>

<form method="POST" action="{{ route('ventas.store') }}">
    @csrf
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="Fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="Fecha" name="Fecha" required 
                   value="{{ old('Fecha', date('Y-m-d')) }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="Cliente_id" class="form-label">Cliente</label>
            <select class="form-select" id="Cliente_id" name="Cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->Id }}" {{ old('Cliente_id') == $cliente->Id ? 'selected' : '' }}>
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
                <option value="{{ $usuario->Id }}" {{ old('Usuario_id') == $usuario->Id ? 'selected' : '' }}>
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
                   value="{{ old('ValorTotal') }}">
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection