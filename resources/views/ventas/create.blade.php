@extends('layouts.app')

@section('title', 'Crear Nueva Venta')

@section('content')
<h2>Nueva Venta</h2>

<form method="POST" action="{{ route('ventas.store') }}">
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <select name="Producto_id" id="producto" class="form-control">
        @foreach($productos as $producto)
            <option value="{{ $producto->id }}">
                {{ $producto->Nombre }} (Cantidad: {{ $producto->Cantidad }})
            </option>
        @endforeach
    </select>

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
        
        <div class="col-md-4 mb-4">
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
        <div class="col-md-4 mb-3">
            <label for="Producto_id" class="form-label">Producto</label>
            <select class="form-select" id="Producto_id" name="detalles[0][Producto_id]" required>
                <option value="">Seleccionar Producto</option>
                @foreach($productos as $producto)   
                    <option value="{{ $producto->Id }}">{{ $producto->Nombre }}</option>
                @endforeach              
            </select>
        </div>
        <div class="col-md-3">
            <label for="Cantidad" class="form-label">Cantidad</label>
            <input type="number" name="detalles[0][Cantidad]" id="cantidad" class="form-control" required>
        </div>

        <!-- <div class="col-md-4 mb-3">
                <label for="ValorTotal" class="form-label">Valor Total</label>
                <input type="number" step="0.01" class="form-control" id="ValorTotal" name="ValorTotal" required 
                    value="{{ old('ValorTotal') }}">
            </div>
        </div> -->
    
    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Realizar Venta
        </button>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection