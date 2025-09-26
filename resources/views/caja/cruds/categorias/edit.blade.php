<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
</head>
<body>
    <h1>Editar Categoría</h1>
    
    <!-- Formulario para editar una categoría -->
    <form method="POST" action="{{ route('categorias.update', $categoria->Id) }}">
        @csrf
        @method('PUT') <!-- Método PUT para actualizar -->
        
        <div>
            <label for="Tipo_categoria">Nombre de la categoría</label>
            <input type="text" id="Tipo_categoria" name="Tipo_categoria" value="{{ $categoria->Tipo_categoria }}" required>
        </div>

        <button type="submit">Actualizar Categoría</button>
    </form>

    <br>
    <a href="{{ route('categorias.index') }}">Volver a la lista de Categorías</a>
</body>
</html>
