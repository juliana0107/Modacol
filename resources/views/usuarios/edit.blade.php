@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<h2>Editar Usuario</h2>

<form method="POST" action="{{ route('usuarios.update', $usuario->Id) }}">
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
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="Nombre" name="Nombre" required 
                   value="{{ old('Nombre', $usuario->Nombre) }}">
            @error('Nombre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="Correo" name="Correo" required 
                   value="{{ old('Correo', $usuario->Correo) }}">
            @error('Correo')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="Contraseña" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
            <input type="password" class="form-control" id="Contraseña" name="Contraseña">
            @error('Contraseña')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="Id_Rol" class="form-label">Rol</label>
            <select class="form-select" id="Id_Rol" name="Id_Rol" required>
                <option value="">Seleccionar Rol</option>
                @foreach($roles as $rol)
                <option value="{{ $rol->Id }}" {{ (old('Id_Rol', $usuario->Id_Rol) == $rol->Id) ? 'selected' : '' }}>
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
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Actualizar
        </button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
@endsection