// resources/views/clientes/edit.blade.php
@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<h2>Editar Cliente</h2>

<form method="POST" action="{{ route('clientes.update', $cliente->Id) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Empresa" class="form-label">Empresa</label>
            <input type="text" class="form-control" id="Empresa" name="Empresa" required 
                   value="{{ old('Empresa', $cliente->Empresa) }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="Nombre" name="Nombre" required 
                   value="{{ old('Nombre', $cliente->Nombre) }}">
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Identificacion" class="form-label">Identificación</label>
            <input type="number" class="form-control" id="Identificacion" name="Identificacion" required 
                   value="{{ old('Identificacion', $cliente->Identificacion) }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Contacto" class="form-label">Contacto</label>
            <input type="text" class="form-control" id="Contacto" name="Contacto" required 
                   value="{{ old('Contacto', $cliente->Contacto) }}">
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="Correo" name="Correo" required 
                   value="{{ old('Correo', $cliente->Correo) }}">
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Actualizar
        </button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection