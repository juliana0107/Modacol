@extends('operativo.layout')

@section('title', 'Crear Nuevo Cliente')

@section('content')
<h2>Nuevo Cliente</h2>

<form method="POST" action="{{ route('operativo.cruds.clientes.store') }}">
    @csrf
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Empresa" class="form-label">Empresa</label>
            <input type="text" class="form-control" id="Empresa" name="Empresa" required 
                   value="{{ old('Empresa') }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="Nombre" name="Nombre" required 
                   value="{{ old('Nombre') }}">
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Identificacion" class="form-label">Identificación</label>
            <input type="number" class="form-control" id="Identificacion" name="Identificacion" required 
                   value="{{ old('Identificacion') }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Contacto" class="form-label">Contacto</label>
            <input type="text" class="form-control" id="Contacto" name="Contacto" required 
                   value="{{ old('Contacto') }}">
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="Correo" name="Correo" required 
                   value="{{ old('Correo') }}">
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('operativo.cruds.clientes.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection