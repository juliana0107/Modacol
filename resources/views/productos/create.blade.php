@extends('layouts.app')

@section('title', 'Crear Nuevo Producto')

@section('content')
<h2>Nuevo Producto</h2>

<form method="POST" action="{{ route('productos.store') }}">
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
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Nombre" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="Nombre" name="Nombre" required 
                   value="{{ old('Nombre') }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="PrecioU" class="form-label">Precio Unitario</label>
            <input type="number" step="0.01" class="form-control" id="PrecioU" name="PrecioU" required 
                   value="{{ old('PrecioU') }}">
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Cantidad" class="form-label">Cantidad en Stock</label>
            <input type="number" class="form-control" id="Cantidad" name="Cantidad" required 
                   value="{{ old('Cantidad') }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="Fecha" name="Fecha" 
                   value="{{ old('Fecha', date('Y-m-d')) }}">
        </div>
    </div>
    
    <div class="mb-3">
        <label for="Descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="Descripcion" name="Descripcion" rows="3" required>{{ old('Descripcion') }}</textarea>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection