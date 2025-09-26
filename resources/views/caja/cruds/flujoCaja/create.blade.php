<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Flujo de Caja</title>
</head>
<body>
    <h1>Crear Nuevo Flujo de Caja</h1>
    
    <!-- Formulario para crear un flujo de caja -->
    <form method="POST" action="{{ route('flujoCaja.store') }}">
        @csrf
        <div>
            <label for="Fecha">Fecha</label>
            <input type="date" id="Fecha" name="Fecha" required>
        </div>

        <div>
            <label for="Saldo_Neto">Saldo Neto</label>
            <input type="number" id="Saldo_Neto" name="Saldo_Neto" step="0.01" required>
        </div>

        <div>
            <label for="Saldo_Final">Saldo Final</label>
            <input type="number" id="Saldo_Final" name="Saldo_Final" step="0.01" required>
        </div>

        <div>
            <label for="Activo">Activo</label>
            <input type="checkbox" id="Activo" name="Activo" value="1">
        </div>

        <button type="submit">Crear Flujo</button>
    </form>

    <br>
    <a href="{{ route('flujoCaja.index') }}">Volver a la lista de Flujos</a>
</body>
</html>
