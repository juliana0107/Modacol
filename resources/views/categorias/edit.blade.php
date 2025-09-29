@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')

<h1>Editar Categoría</h1>

<form method="POST" action="{{ route('categorias.update', $categoria->Id) }}">
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

    <div>
        <label for="Tipo_categoria">Nombre de la categoría</label>
        <input type="text" id="Tipo_categoria" name="Tipo_categoria" value="{{ $categoria->Tipo_categoria }}" required>
    </div>

    <button type="submit">Actualizar Categoría</button>
</form>

<a href="{{ route('categorias.index') }}">Volver a la lista de Categorías</a>

@endsection
