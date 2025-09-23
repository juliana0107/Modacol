@extends('layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('usuarios.report') }}" class="btn btn-success">
        <i class="fas fa-download me-1"></i> Descargar Reporte
    </a>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Usuario
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<!-- Formulario de filtros -->
<form method="GET" action="{{ route('usuarios.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <input type="text" class="form-control" name="nombre" placeholder="Nombre" value="{{ request('nombre') }}">
        </div>
        <div class="col-md-3">
            <input type="email" class="form-control" name="correo" placeholder="Correo" value="{{ request('correo') }}">
        </div>
        <div class="col-md-3">
            <select name="activo" class="form-control">
                <option value="">Estado</option>
                <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="rol" class="form-control">
                <option value="">Rol</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->Id }}" {{ request('rol') == $rol->Id ? 'selected' : '' }}>
                        {{ $rol->Tipo }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->Id }}</td>
                <td>{{ $usuario->Nombre }}</td>
                <td>{{ $usuario->Correo }}</td>
                <td>{{ $usuario->rol->Tipo }}</td>
                <td>
                    <span class="badge bg-{{ $usuario->Activo ? 'success' : 'danger' }} text-white rounded-pill">
                        {{ $usuario->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('usuarios.edit', $usuario->Id) }}" class="btn btn-warning btn-sm text-white" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                     <form action="{{ route('usuarios.toggle', $usuario->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $usuario->Activo ? 'success' : 'danger' }} btn-sm text-white"
                                title="{{ $usuario->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($usuarios->isEmpty())
    <div class="alert alert-info text-center">
        No hay usuarios registrados.
    </div>
@endif
@endsection