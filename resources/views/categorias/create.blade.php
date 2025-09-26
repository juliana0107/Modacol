@extends('layouts.app')

@section('title', 'Crear Categoría')

@section('content')

<h1>Crear Nueva Categoría</h1>

<form method="POST" action="{{ route('categorias.store') }}">
    @csrf
    <div>
        <label for="Tipo_categoria">Nombre de la categoría</label>
        <input type="text" id="Tipo_categoria" name="Tipo_categoria" required>
    </div>

    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Crear Categoría</button>
</form>

<!-- <a href="{{ route('categorias.index') }}">Volver a la lista de Categorías</a>
 -->
@endsection
