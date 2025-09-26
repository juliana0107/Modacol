@extends('layouts.app')

@section('title', 'Gestión de Flujos de Caja')


@section('content')

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('flujoCaja.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nuevo Flujo de Caja
    </a>
    <div class="btn-group">
        <a href="{{ route('flujoCaja.exportExcel', request()->query()) }}" class="btn btn-warning">
           <i class="fas fa-download me-1"></i>
            Exportar Excel
        </a>
    </div>
</div>

    {{-- Formulario de filtros --}}
    <form action="{{ route('flujoCaja.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ request('fecha') }}">
            </div>
            <div class="col-md-3">
                <label for="estado" class="form-label">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Saldo Neto</th>
            <th>Saldo Final</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($flujoCajas as $flujo)
        <tr>
            <td>{{ $flujo->Id }}</td>
            <td>{{ $flujo->Fecha }}</td>
            <td>{{ $flujo->Saldo_Neto }}</td>
            <td>{{ $flujo->Saldo_Final }}</td>
            <td>
                <span class="badge {{ $flujo->Activo ? 'bg-success' : 'bg-danger' }}">
                    {{ $flujo->Activo ? 'Activo' : 'Inactivo' }}
                </span>
            </td>
            <td>
                <a href="{{ route('flujoCaja.show', $flujo->Id) }}" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i></a>
                <a href="{{ route('flujoCaja.edit', $flujo->Id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                <form action="{{ route('flujoCaja.toggle', $flujo->Id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $flujo->Activo ? 'danger' : 'success' }} btn-sm"
                        {{ $flujo->Activo ? 'Inactivar' : 'Activar' }}>
                        <i class="fas fa-power-off"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
