<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Categoría</title>
</head>
<body>
    <h1>Crear Nueva Categoría</h1>
    
    <!-- Formulario para crear una categoría -->
    <form method="POST" action="{{ route('caja.cruds.categorias.store') }}">
        @csrf
        <div>
            <label for="Tipo_categoria">Nombre de la categoría</label>
            <input type="text" id="Tipo_categoria" name="Tipo_categoria" required>
        </div>
        
        <button type="submit">Crear Categoría</button>
    </form>

    <br>
    <a href="{{ route('categorias.index') }}">Volver a la lista de Categorías</a>
</body>
</html>
