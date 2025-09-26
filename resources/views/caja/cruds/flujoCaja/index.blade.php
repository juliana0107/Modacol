<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Flujo de Caja</title>
</head>
<body>
    <h1>Flujo de Caja</h1>
    <form method="POST" action="{{ route('flujoCaja.store') }}">
        @csrf
        <label for="Fecha">Fecha</label>
        <input type="date" id="Fecha" name="Fecha" required>
        
        <label for="Saldo_Neto">Saldo Neto</label>
        <input type="number" id="Saldo_Neto" name="Saldo_Neto" step="0.01" required>

        <label for="Saldo_Final">Saldo Final</label>
        <input type="number" id="Saldo_Final" name="Saldo_Final" step="0.01" required>

        <label for="Activo">Activo</label>
        <input type="checkbox" id="Activo" name="Activo" value="1">

        <button type="submit">Crear Flujo</button>
    </form>

    <h2>Lista de Flujos</h2>
    <ul>
        @foreach ($flujos as $flujo)
            <li>{{ $flujo->Fecha }} - Saldo Neto: {{ $flujo->Saldo_Neto }} - Saldo Final: {{ $flujo->Saldo_Final }}</li>
        @endforeach
    </ul>
</body>
</html>
