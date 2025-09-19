@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Proveedores</h2>
    <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Proveedor
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
                <th>Razón Social</th>
                <th>Identificación</th>
                <th>Dirección</th>
                <th>Correo</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td>{{ $proveedor->Id }}</td>
                <td>{{ $proveedor->RazonSocial }}</td>
                <td>{{ $proveedor->Identificacion }}</td>
                <td>{{ $proveedor->Direccion }}</td>
                <td>{{ $proveedor->Correo }}</td>
                <td>{{ $proveedor->Contacto }}</td>
                <td>
                    <span class="badge bg-{{ $proveedor->Activo ? 'success' : 'danger' }}">
                        {{ $proveedor->Activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="table-actions text-center">
                    <a href="{{ route('proveedores.edit', $proveedor->Id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form action="{{ route('proveedores.toggle', $proveedor->Id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $proveedor->Activo ? 'secondary' : 'success' }} btn-sm" 
                                title="{{ $proveedor->Activo ? 'Inactivar' : 'Activar' }}">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($proveedores->isEmpty())
    <div class="alert alert-info text-center">
        No hay proveedores registrados.
    </div>
@endif
@endsection