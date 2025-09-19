@extends('layout')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Clientes</h2>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Cliente
    </a>
</div>

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