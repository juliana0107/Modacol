@extends('layouts.app')

@section('title', 'Editar Venta')

@section('content')
<h2>Editar Venta</h2>

<form method="POST" action="{{ route('ventas.update', $venta->Id) }}">
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
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="Fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="Fecha" name="Fecha" required 
                   value="{{ old('Fecha', $venta->Fecha) }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="Cliente_id" class="form-label">Cliente</label>
            <select class="form-select" id="Cliente_id" name="Cliente_id" required>
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->Id }}" {{ (old('Cliente_id', $venta->Cliente_id) == $cliente->Id) ? 'selected' : '' }}>
                    {{ $cliente->Nombre }} - {{ $cliente->Empresa }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="Usuario_id" class="form-label">Vendedor</label>
            <select class="form-select" id="Usuario_id" name="Usuario_id" required>
                <option value="">Seleccionar Vendedor</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->Id }}" {{ (old('Usuario_id', $venta->Usuario_id) == $usuario->Id) ? 'selected' : '' }}>
                    {{ $usuario->Nombre }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    
    
    <!-- Detalles de la venta -->
    <div class="row" id="detalles">
        <div class="col-md-6">
            <label for="Producto_id" class="form-label">Producto</label>
            <select class="form-select" name="detalles[0][Producto_id]" required>
                <option value="">Seleccionar Producto</option>
                @foreach($productos as $producto)
                <option value="{{ $producto->Id }}" {{ isset($venta->detalles[0]) && $venta->detalles[0]->Producto_id == $producto->Id ? 'selected' : '' }}>
                    {{ $producto->Nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="Cantidad" class="form-label">Cantidad</label>
            <input type="number" name="detalles[0][Cantidad]" value="{{ isset($venta->detalles[0]) ? $venta->detalles[0]->Cantidad : 1 }}" required class="form-control">
        </div>

        <!-- <div class="row">
        <div class="col-md-6 mb-3">
            <label for="ValorTotal" class="form-label">Valor Total</label>
            <input type="number" step="0.01" class="form-control" id="ValorTotal" name="ValorTotal" required 
                   value="{{ old('ValorTotal', $venta->ValorTotal) }}">
        </div>
    </div> -->
        <div class="col-md-3">
            <button type="button" class="btn btn-secondary mt-4" id="add-producto">
                <i class="fas fa-plus"></i> Agregar Producto
            </button>
        </div>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Actualizar
        </button>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>
<script>
    let productoIndex = 1;

    document.getElementById('add-producto').addEventListener('click', function() {
        const detallesRow = document.createElement('div');
        detallesRow.classList.add('row');
        detallesRow.innerHTML = `
            <div class="col-md-6">
                <label for="Producto_id" class="form-label">Producto</label>
                <select class="form-select" name="detalles[${productoIndex}][Producto_id]" required>
                    <option value="">Seleccionar Producto</option>
                    @foreach($productos as $producto)
                    <option value="{{ $producto->Id }}">
                        {{ $producto->Nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="Cantidad" class="form-label">Cantidad</label>
                <input type="number" name="detalles[${productoIndex}][Cantidad]" value="1" required class="form-control">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger mt-4 remove-producto">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </div>
        `;
        document.getElementById('detalles').appendChild(detallesRow);
        productoIndex++;
    });

    document.getElementById('detalles').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-producto')) {
            event.target.closest('.row').remove();
        }
    });
</script>
@endsection

