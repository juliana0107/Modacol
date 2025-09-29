
@extends('layouts.app')

@section('title', 'Editar Compra')

@section('content')
<h2>Editar Compra #{{ $compra->Id }}</h2>

<form method="POST" action="{{ route('compras.update', $compra->Id) }}" id="compraForm">
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
        <div class="col-md-6 mb-3">
            <label for="Proveedor_Id" class="form-label">Proveedor</label>
            <select class="form-select" id="Proveedor_Id" name="Proveedor_Id" required>
                <option value="">Seleccionar proveedor</option>
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->Id }}" 
                        {{ old('Proveedor_Id', $compra->Proveedor_Id) == $proveedor->Id ? 'selected' : '' }}>
                        {{ $proveedor->RazonSocial }}
                    </option>
                @endforeach
            </select>
            @error('Proveedor_Id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    <div class="col-md-3 mb-3">
        <label for="productos">Productos</label>
        <div id="productos">
            @foreach ($compra->detalles as $index => $detalle)
                <div class="producto-row">
                    <select name="productos[{{ $index }}][id]" class="form-control" required>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->Id }}" {{ $detalle->Producto_Id == $producto->Id ? 'selected' : '' }}>{{ $producto->Nombre }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="productos[{{ $index }}][cantidad]" class="form-control" value="{{ $detalle->Cantidad }}" required min="1">
                    <input type="number" name="productos[{{ $index }}][precio]" class="form-control" value="{{ $detalle->PrecioUnitario }}" required min="0">
                </div>
            @endforeach
        </div>
    </div>
        
        <div class="col-md-3 mb-3">
            <label for="FechaCompra" class="form-label">Fecha de Compra</label>
            <input type="date" class="form-control" id="FechaCompra" name="FechaCompra" required 
                   value="{{ old('FechaCompra', $compra->FechaCompra->format('Y-m-d')) }}">
            @error('FechaCompra')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-3 mb-3">
            <label for="Estado" class="form-label">Estado</label>
            <select class="form-select" id="Estado" name="Estado" required>
                <option value="Pendiente" {{ old('Estado', $compra->Estado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="Completada" {{ old('Estado', $compra->Estado) == 'Completada' ? 'selected' : '' }}>Completada</option>
                <option value="Cancelada" {{ old('Estado', $compra->Estado) == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
            @error('Estado')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mb-3">
        <label for="Observaciones" class="form-label">Observaciones</label>
        <textarea class="form-control" id="Observaciones" name="Observaciones" rows="2">{{ old('Observaciones', $compra->Observaciones) }}</textarea>
        @error('Observaciones')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    
    <hr>
    
    <h4>Productos</h4>
    
    <div class="row mb-3">
        <div class="col-md-5">
            <label class="form-label">Seleccionar Producto</label>
            <select class="form-select" id="productoSelect">
                <option value="">Seleccionar producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->Id }}" data-precio="{{ $producto->Precio }}">
                        {{ $producto->Nombre }} - ${{ number_format($producto->Precio, 2) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" min="1" value="1">
        </div>
        <div class="col-md-2">
            <label for="precio" class="form-label">Precio Unitario</label>
            <input type="number" class="form-control" id="precio" step="0.01" min="0">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" class="btn btn-success" id="agregarProducto">
                <i class="fas fa-plus me-1"></i> Agregar Producto
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped" id="productosTable">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los productos se agregarán dinámicamente aquí -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total:</th>
                    <th id="totalCompra">$0.00</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="mt-3">
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-1"></i> Actualizar Compra
        </button>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i> Cancelar
        </a>
    </div>
</form>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productoSelect = document.getElementById('productoSelect');
    const cantidadInput = document.getElementById('cantidad');
    const precioInput = document.getElementById('precio');
    const agregarBtn = document.getElementById('agregarProducto');
    const productosTable = document.getElementById('productosTable').getElementsByTagName('tbody')[0];
    const totalCompra = document.getElementById('totalCompra');
    const compraForm = document.getElementById('compraForm');
    const btnGuardar = document.getElementById('btnGuardar');
    
    let productos = @json($compra->detalles->map(function($detalle) {
        return [
            'id' => $detalle->Producto_Id,
            'nombre' => $detalle->producto->Nombre,
            'cantidad' => $detalle->Cantidad,
            'precio' => floatval($detalle->PrecioUnitario),
            'subtotal' => floatval($detalle->Subtotal)
        ];
    }));
    let total = 0;
    
    // Inicializar tabla con los productos existentes
    actualizarTabla();
    
    // Actualizar precio cuando se selecciona un producto
    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.precio) {
            precioInput.value = selectedOption.dataset.precio;
        }
    });
    
    // Agregar producto a la tabla
    agregarBtn.addEventListener('click', function() {
        const productoId = productoSelect.value;
        const productoNombre = productoSelect.options[productoSelect.selectedIndex].text;
        const cantidad = parseInt(cantidadInput.value);
        const precio = parseFloat(precioInput.value);
        
        if (!productoId || !cantidad || !precio) {
            alert('Por favor, complete todos los campos del producto.');
            return;
        }
        
        // Verificar si el producto ya fue agregado
        if (productos.some(p => p.id === productoId)) {
            alert('Este producto ya ha sido agregado a la compra.');
            return;
        }
        
        const subtotal = cantidad * precio;
        
        // Agregar a la lista
        productos.push({
            id: productoId,
            nombre: productoNombre,
            cantidad: cantidad,
            precio: precio,
            subtotal: subtotal
        });
        
        // Actualizar tabla
        actualizarTabla();
        
        // Limpiar campos
        productoSelect.value = '';
        cantidadInput.value = 1;
        precioInput.value = '';
    });
    
    // Eliminar producto de la tabla
    function eliminarProducto(index) {
        productos.splice(index, 1);
        actualizarTabla();
    }
    
    // Actualizar tabla y total
    function actualizarTabla() {
        productosTable.innerHTML = '';
        total = 0;
        
        productos.forEach((producto, index) => {
            const row = productosTable.insertRow();
            
            const cellProducto = row.insertCell(0);
            const cellCantidad = row.insertCell(1);
            const cellPrecio = row.insertCell(2);
            const cellSubtotal = row.insertCell(3);
            const cellAcciones = row.insertCell(4);
            
            cellProducto.innerHTML = producto.nombre;
            cellCantidad.innerHTML = producto.cantidad;
            cellPrecio.innerHTML = '$' + producto.precio.toFixed(2);
            cellSubtotal.innerHTML = '$' + producto.subtotal.toFixed(2);
            cellAcciones.innerHTML = `
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            total += producto.subtotal;
        });
        
        totalCompra.textContent = '$' + total.toFixed(2);
        
        // Crear campos ocultos para el formulario
        actualizarCamposOcultos();
    }
    
    // Crear campos ocultos para enviar los productos en el formulario
    function actualizarCamposOcultos() {
        // Eliminar campos ocultos anteriores
        const camposOcultos = document.querySelectorAll('input[name^="productos"]');
        camposOcultos.forEach(campo => campo.remove());
        
        // Crear nuevos campos ocultos
        productos.forEach((producto, index) => {
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = `productos[${index}][id]`;
            inputId.value = producto.id;
            compraForm.appendChild(inputId);
            
            const inputCantidad = document.createElement('input');
            inputCantidad.type = 'hidden';
            inputCantidad.name = `productos[${index}][cantidad]`;
            inputCantidad.value = producto.cantidad;
            compraForm.appendChild(inputCantidad);
            
            const inputPrecio = document.createElement('input');
            inputPrecio.type = 'hidden';
            inputPrecio.name = `productos[${index}][precio]`;
            inputPrecio.value = producto.precio;
            compraForm.appendChild(inputPrecio);
        });
    }
    
    // Validar formulario antes de enviar
    compraForm.addEventListener('submit', function(e) {
        if (productos.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto a la compra.');
        }
    });
    
    // Hacer la función eliminarProducto global para que funcione en los botones
    window.eliminarProducto = eliminarProducto;
});
</script>
@endsection