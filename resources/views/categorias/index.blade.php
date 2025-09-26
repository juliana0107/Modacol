@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Categoría
    </a>
</div>

<form method="GET" action="{{ route('categorias.index') }}" class="d-flex mb-4">
    <input type="text" name="tipo_categoria" class="form-control" placeholder="Buscar por nombre" value="{{ request()->tipo_categoria }}">
    <select name="estado" class="form-select ms-2">
        <option value="">Estado</option>
        <option value="1" {{ request()->estado == '1' ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ request()->estado == '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
    <button type="submit" class="btn btn-primary ms-2">Filtrar</button>
</form>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tipo de Categoría</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categorias as $categoria)
            <tr>
                <td>{{ $categoria->Id }}</td>
                <td>{{ $categoria->Tipo_categoria }}</td>
                <td>
                    <span class="badge bg-{{ $categoria->Activo ? 'success' : 'danger' }}">
                        {{ $categoria->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('categorias.show', $categoria->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('categorias.toggle', $categoria->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $categoria->Activo ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $categoria->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($categorias->isEmpty())
    <div class="alert alert-info text-center">
        No hay categorías registradas.
    </div>
@endif

@endsection
