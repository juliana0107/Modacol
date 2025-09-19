function obtenerProductos() {
  return JSON.parse(localStorage.getItem("productos")) || [];
}

function guardarProductos(productos) {
  localStorage.setItem("productos", JSON.stringify(productos));
}

function mostrarProductos() {
  const productos = obtenerProductos();
  const tbody = document.querySelector("#tablaProductos tbody");
  tbody.innerHTML = "";

  productos.forEach((producto, index) => {
    const estado = producto.activo ? "Activo ✅" : "Inactivo ❌";
    const fila = `
      <tr>
        <td>${producto.nombre}</td>
        <td>${producto.categoria}</td>
        <td>${producto.precio}</td>
        <td>${producto.stock}</td>
        <td>${estado}</td>
        <td>
          <button onclick="editarProducto(${index})">✏️</button>
          <button onclick="toggleProducto(${index})">🔄</button>
        </td>
      </tr>
    `;
    tbody.innerHTML += fila;
  });
}

function guardarProducto(event) {
  event.preventDefault();

  const producto = {
    nombre: document.getElementById("nombre").value,
    categoria: document.getElementById("categoria").value,
    precio: document.getElementById("precio").value,
    stock: document.getElementById("stock").value,
    activo: true
  };

  const productos = obtenerProductos();
  productos.push(producto);
  guardarProductos(productos);

  cerrarAgregarProductoModal();
  mostrarProductos();
}

let indiceEditarProd = null;
function editarProducto(index) {
  const productos = obtenerProductos();
  const producto = productos[index];
  indiceEditarProd = index;

  document.getElementById("editNombre").value = producto.nombre;
  document.getElementById("editCategoria").value = producto.categoria;
  document.getElementById("editPrecio").value = producto.precio;
  document.getElementById("editStock").value = producto.stock;

  document.getElementById("editarProductoModal").style.display = "block";
}

function guardarCambiosProducto() {
  const productos = obtenerProductos();
  productos[indiceEditarProd] = {
    ...productos[indiceEditarProd],
    nombre: document.getElementById("editNombre").value,
    categoria: document.getElementById("editCategoria").value,
    precio: document.getElementById("editPrecio").value,
    stock: document.getElementById("editStock").value,
  };

  guardarProductos(productos);
  cerrarEditarProductoModal();
  mostrarProductos();
}

function toggleProducto(index) {
  const productos = obtenerProductos();
  productos[index].activo = !productos[index].activo;
  guardarProductos(productos);
  mostrarProductos();
}

document.addEventListener("DOMContentLoaded", mostrarProductos);
