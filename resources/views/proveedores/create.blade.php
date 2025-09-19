@extends('layouts.app')

@section('title', 'Crear Nuevo Proveedor')

@section('content')
<h2>Nuevo Proveedor</h2>

<form method="POST" action="{{ route('proveedores.store') }}">
    @csrf
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="RazonSocial" class="form-label">Razón Social</label>
            <input type="text" class="form-control" id="RazonSocial" name="RazonSocial" required 
                   value="{{ old('RazonSocial') }}">
            @error('RazonSocial')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Identificacion" class="form-label">Identificación</label>
            <input type="number" class="form-control" id="Identificacion" name="Identificacion" required 
                   value="{{ old('Identificacion') }}">
            @error('Identificacion')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="Direccion" name="Direccion" required 
                   value="{{ old('Direccion') }}">
            @error('Direccion')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="Correo" name="Correo" required 
                   value="{{ old('Correo') }}">
            @error('Correo')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Contacto" class="form-label">Contacto</label>
            <input type="text" class="form-control" id="Contacto" name="Contacto" required 
                   value="{{ old('Contacto') }}">
            @error('Contacto')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection