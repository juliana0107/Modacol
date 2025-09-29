@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('content')
<h2>Nuevo Usuario</h2>

<form method="POST" action="{{ route('usuarios.store') }}">
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
            <label for="Nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="Nombre" name="Nombre" required 
                   value="{{ old('Nombre') }}">
            @error('Nombre')
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
            <label for="Contraseña" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="Contraseña" name="Contraseña" required>
            @error('Contraseña')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Id_Rol" class="form-label">Rol</label>
            <select class="form-select" id="Id_Rol" name="Id_Rol" required>
                <option value="">Seleccionar Rol</option>
                @foreach($roles as $rol)
                <option value="{{ $rol->Id }}" {{ old('Id_Rol') == $rol->Id ? 'selected' : '' }}>
                    {{ $rol->Tipo }}
                </option>
                @endforeach
            </select>
            @error('Id_Rol')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection