@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Cliente
    </a>
<div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-1"></i> Exportar Excel
        </button>
        <ul class="dropdown-menu">

            <li>
                <a class="dropdown-item" href="{{ route('clientes.exportar-excel', array_merge(request()->except(['tipo']), ['tipo' => 'detallado'])) }}">
                    <i class="far fa-file-excel me-2"></i> Reporte Detallado
                </a>
            </li>
        </ul>
    </div>
</div>


<form method="GET" action="{{ route('clientes.index') }}" class="d-flex mb-4">
    <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre" value="{{ request()->nombre }}">
    <input type="text" name="correo" class="form-control ms-2" placeholder="Buscar por correo" value="{{ request()->correo }}">
    <select name="estado" class="form-select ms-2">
        <option value="">Estado</option>
        <option value="1" {{ request()->estado == '1' ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ request()->estado == '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
    <button type="submit" class="btn btn-primary ms-2">Filtrar</button>
</form>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Empresa</th>
                <th>Nombre</th>
                <th>Identificación</th>
                <th>Contacto</th>
                <th>Correo</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td>{{ $cliente->Id }}</td>
                <td>{{ $cliente->Empresa }}</td>
                <td>{{ $cliente->Nombre }}</td>
                <td>{{ $cliente->Identificacion }}</td>
                <td>{{ $cliente->Contacto }}</td>
                <td>{{ $cliente->Correo }}</td>
                <td>
                    <span class="badge bg-{{ $cliente->Activo ? 'success' : 'danger' }}">
                        {{ $cliente->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('clientes.edit', $cliente->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('clientes.toggle', $cliente->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $cliente->Activo ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $cliente->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($clientes->isEmpty())
    <div class="alert alert-info text-center">
        No hay clientes registrados.
    </div>
@endif
@endsection