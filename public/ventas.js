// === FUNCIONES BASE ===
function obtenerVentas() {
  return JSON.parse(localStorage.getItem("ventas")) || [];
}

function guardarVentas(ventas) {
  localStorage.setItem("ventas", JSON.stringify(ventas));
}

// === MOSTRAR TABLA ===
function mostrarVentas() {
  const ventas = obtenerVentas();
  const tbody = document.querySelector("#tablaVentas tbody");
  tbody.innerHTML = "";

  ventas.forEach((venta, index) => {
    const estado = venta.activo ? "Activo ✅" : "Inactivo ❌";
    const fila = `
      <tr>
        <td>${venta.fecha}</td>
        <td>${venta.numeroFactura}</td>
        <td>${venta.nombreCliente}</td>
        <td>${venta.descripcion}</td>
        <td>${venta.valorUnitario}</td>
        <td>${venta.iva}</td>
        <td>${venta.valorTotal}</td>
        <td>${estado}</td>
        <td>
          <button onclick="editarVenta(${index})">✏️</button>
          <button onclick="toggleVenta(${index})">🔄</button>
        </td>
      </tr>
    `;
    tbody.innerHTML += fila;
  });
}

// === CREAR VENTA ===
function guardarVenta(event) {
  event.preventDefault();

  const venta = {
    fecha: document.getElementById("fecha").value,
    numeroFactura: document.getElementById("numeroFactura").value,
    nombreCliente: document.getElementById("nombreCliente").value,
    descripcion: document.getElementById("descripcion").value,
    valorUnitario: document.getElementById("valorUnitario").value,
    iva: document.getElementById("iva").value,
    valorTotal: document.getElementById("valorTotal").value,
    activo: true
  };

  const ventas = obtenerVentas();
  ventas.push(venta);
  guardarVentas(ventas);

  cerrarAgregarVentaModal();
  mostrarVentas();
}

// === EDITAR VENTA ===
let indiceEditar = null;
function editarVenta(index) {
  const ventas = obtenerVentas();
  const venta = ventas[index];
  indiceEditar = index;

  document.getElementById("editFecha").value = venta.fecha;
  document.getElementById("editNumeroFactura").value = venta.numeroFactura;
  document.getElementById("editNombreCliente").value = venta.nombreCliente;
  document.getElementById("editDescripcion").value = venta.descripcion;
  document.getElementById("editValorUnitario").value = venta.valorUnitario;
  document.getElementById("editIva").value = venta.iva;
  document.getElementById("editValorTotal").value = venta.valorTotal;

  document.getElementById("editarVentaModal").style.display = "block";
}

function guardarCambiosVenta() {
  const ventas = obtenerVentas();
  ventas[indiceEditar] = {
    ...ventas[indiceEditar],
    fecha: document.getElementById("editFecha").value,
    numeroFactura: document.getElementById("editNumeroFactura").value,
    nombreCliente: document.getElementById("editNombreCliente").value,
    descripcion: document.getElementById("editDescripcion").value,
    valorUnitario: document.getElementById("editValorUnitario").value,
    iva: document.getElementById("editIva").value,
    valorTotal: document.getElementById("editValorTotal").value,
  };

  guardarVentas(ventas);
  cerrarEditarVentaModal();
  mostrarVentas();
}

// === ACTIVAR/INACTIVAR ===
function toggleVenta(index) {
  const ventas = obtenerVentas();
  ventas[index].activo = !ventas[index].activo;
  guardarVentas(ventas);
  mostrarVentas();
}

// === INICIALIZAR ===
document.addEventListener("DOMContentLoaded", mostrarVentas);
