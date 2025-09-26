@extends('layouts.app')

@section('title', 'Editar Flujo de Caja')

@section('content')

<h1>Editar Flujo de Caja</h1>

<form action="{{ route('flujoCaja.update', $flujo->Id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="Fecha" class="form-label">Fecha</label>
        <input type="date" class="form-control @error('Fecha') is-invalid @enderror" id="Fecha" name="Fecha" value="{{ old('Fecha', $flujo->Fecha) }}">
        @error('Fecha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="Saldo_Neto" class="form-label">Saldo Neto</label>
        <input type="number" step="0.01" class="form-control @error('Saldo_Neto') is-invalid @enderror" id="Saldo_Neto" name="Saldo_Neto" value="{{ old('Saldo_Neto', $flujo->Saldo_Neto) }}">
        @error('Saldo_Neto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="Saldo_Final" class="form-label">Saldo Final</label>
        <input type="number" step="0.01" class="form-control @error('Saldo_Final') is-invalid @enderror" id="Saldo_Final" name="Saldo_Final" value="{{ old('Saldo_Final', $flujo->Saldo_Final) }}">
        @error('Saldo_Final')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="Activo" class="form-label">Estado</label>
        <select class="form-select @error('Activo') is-invalid @enderror" id="Activo" name="Activo">
            <option value="1" {{ old('Activo', $flujo->Activo) == 1 ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('Activo', $flujo->Activo) == 0 ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('Activo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('flujoCaja.index') }}" class="btn btn-secondary">Cancelar</a>
</form>

@endsection
