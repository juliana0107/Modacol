@extends('layouts.app')

@section('title', 'Ver Detalles del Flujo de Caja')

@section('content')

<h1>Detalles del Flujo de Caja</h1>

<div class="card">
    <div class="card-header">
        <h5>Flujo de Caja ID: {{ $flujo->Id }}</h5>
    </div>
    <div class="card-body">
        <p><strong>Fecha:</strong> {{ $flujo->Fecha }}</p>
        <p><strong>Saldo Neto:</strong> ${{ number_format($flujo->Saldo_Neto, 2) }}</p>
        <p><strong>Saldo Final:</strong> ${{ number_format($flujo->Saldo_Final, 2) }}</p>
        <p><strong>Estado:</strong>
            <span class="badge {{ $flujo->Activo ? 'bg-success' : 'bg-danger' }}">
                {{ $flujo->Activo ? 'Activo' : 'Inactivo' }}
            </span>
        </p>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <a href="{{ route('flujoCaja.edit', $flujo->Id) }}" class="btn btn-info btn-sm ms-2">Editar</a>
    </div>
</div>

@endsection
