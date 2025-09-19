function obtenerClientes() {
  return JSON.parse(localStorage.getItem("clientes")) || [];
}

function guardarClientes(clientes) {
  localStorage.setItem("clientes", JSON.stringify(clientes));
}

function mostrarClientes() {
  const clientes = obtenerClientes();
  const tbody = document.querySelector("#tablaClientes tbody");
  tbody.innerHTML = "";

  clientes.forEach((cliente, index) => {
    const estado = cliente.activo ? "Activo ✅" : "Inactivo ❌";
    const fila = `
      <tr>
        <td>${cliente.nombre}</td>
        <td>${cliente.email}</td>
        <td>${cliente.telefono}</td>
        <td>${estado}</td>
        <td>
          <button onclick="editarCliente(${index})">✏️</button>
          <button onclick="toggleCliente(${index})">🔄</button>
        </td>
      </tr>
    `;
    tbody.innerHTML += fila;
  });
}

function guardarCliente(event) {
  event.preventDefault();

  const cliente = {
    nombre: document.getElementById("nombre").value,
    email: document.getElementById("email").value,
    telefono: document.getElementById("telefono").value,
    activo: true
  };

  const clientes = obtenerClientes();
  clientes.push(cliente);
  guardarClientes(clientes);

  cerrarAgregarClienteModal();
  mostrarClientes();
}

let indiceEditarCliente = null;
function editarCliente(index) {
  const clientes = obtenerClientes();
  const cliente = clientes[index];
  indiceEditarCliente = index;

  document.getElementById("editNombre").value = cliente.nombre;
  document.getElementById("editEmail").value = cliente.email;
  document.getElementById("editTelefono").value = cliente.telefono;

  document.getElementById("editarClienteModal").style.display = "block";
}

function guardarCambiosCliente() {
  const clientes = obtenerClientes();
  clientes[indiceEditarCliente] = {
    ...clientes[indiceEditarCliente],
    nombre: document.getElementById("editNombre").value,
    email: document.getElementById("editEmail").value,
    telefono: document.getElementById("editTelefono").value,
  };

  guardarClientes(clientes);
  cerrarEditarClienteModal();
  mostrarClientes();
}

function toggleCliente(index) {
  const clientes = obtenerClientes();
  clientes[index].activo = !clientes[index].activo;
  guardarClientes(clientes);
  mostrarClientes();
}

document.addEventListener("DOMContentLoaded", mostrarClientes);
