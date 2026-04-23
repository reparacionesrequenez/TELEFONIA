import { auth, db } from "./firebase-config.js";
import {
  signInWithEmailAndPassword,
  onAuthStateChanged,
  signOut
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

import {
  collection,
  addDoc,
  getDocs,
  doc,
  deleteDoc,
  getDoc,
  updateDoc
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

// LOGIN
const formLogin = document.getElementById("formLogin");

if (formLogin) {
  formLogin.addEventListener("submit", async (e) => {
    e.preventDefault();

    const correo = document.getElementById("correo")?.value;
    const password = document.getElementById("password")?.value;

    try {
      await signInWithEmailAndPassword(auth, correo, password);
      alert("Inicio de sesión correcto");
      window.location.href = "equipos.html";
    } catch (error) {
      alert("Error al iniciar sesión: " + error.message);
      console.error(error);
    }
  });
}

// CRUD EQUIPOS
const formEquipo = document.getElementById("formEquipo");
const tablaEquipos = document.getElementById("tablaEquipos");
const btnCancelar = document.getElementById("btnCancelar");

async function cargarEquipos() {
  if (!tablaEquipos) return;

  tablaEquipos.innerHTML = "";
  const querySnapshot = await getDocs(collection(db, "equipos"));

  querySnapshot.forEach((documento) => {
    const data = documento.data();

    tablaEquipos.innerHTML += `
      <tr>
        <td>${data.cliente || ""}</td>
        <td>${data.telefono || ""}</td>
        <td>${data.equipo || ""}</td>
        <td>${data.marca || ""}</td>
        <td>${data.modelo || ""}</td>
        <td>${data.falla || ""}</td>
        <td>${data.estado || ""}</td>
        <td>${data.precio || ""}</td>
        <td>${data.fecha || ""}</td>
        <td>
          <button class="btn btn-warning btn-sm" onclick="editarEquipo('${documento.id}')">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="eliminarEquipo('${documento.id}')">Eliminar</button>
        </td>
      </tr>
    `;
  });
}

if (formEquipo) {
  formEquipo.addEventListener("submit", async (e) => {
    e.preventDefault();

    const equipoId = document.getElementById("equipoId").value;
    const datos = {
      cliente: document.getElementById("cliente").value,
      telefono: document.getElementById("telefono").value,
      equipo: document.getElementById("equipo").value,
      marca: document.getElementById("marca").value,
      modelo: document.getElementById("modelo").value,
      falla: document.getElementById("falla").value,
      estado: document.getElementById("estado").value,
      precio: Number(document.getElementById("precio").value),
      fecha: document.getElementById("fecha").value
    };

    try {
      if (equipoId) {
        await updateDoc(doc(db, "equipos", equipoId), datos);
        alert("Registro actualizado");
      } else {
        await addDoc(collection(db, "equipos"), datos);
        alert("Registro guardado");
      }

      formEquipo.reset();
      document.getElementById("equipoId").value = "";
      cargarEquipos();
    } catch (error) {
      alert("Error: " + error.message);
      console.error(error);
    }
  });
}

if (btnCancelar) {
  btnCancelar.addEventListener("click", () => {
    formEquipo.reset();
    document.getElementById("equipoId").value = "";
  });
}

window.eliminarEquipo = async (id) => {
  if (!confirm("¿Seguro que deseas eliminar este registro?")) return;

  try {
    await deleteDoc(doc(db, "equipos", id));
    cargarEquipos();
  } catch (error) {
    alert("Error al eliminar: " + error.message);
    console.error(error);
  }
};

window.editarEquipo = async (id) => {
  try {
    const ref = doc(db, "equipos", id);
    const snap = await getDoc(ref);

    if (snap.exists()) {
      const data = snap.data();

      document.getElementById("equipoId").value = id;
      document.getElementById("cliente").value = data.cliente || "";
      document.getElementById("telefono").value = data.telefono || "";
      document.getElementById("equipo").value = data.equipo || "";
      document.getElementById("marca").value = data.marca || "";
      document.getElementById("modelo").value = data.modelo || "";
      document.getElementById("falla").value = data.falla || "";
      document.getElementById("estado").value = data.estado || "";
      document.getElementById("precio").value = data.precio || "";
      document.getElementById("fecha").value = data.fecha || "";
    }
  } catch (error) {
    alert("Error al editar: " + error.message);
    console.error(error);
  }
};

if (tablaEquipos) {
  onAuthStateChanged(auth, (user) => {
    if (user) {
      cargarEquipos();
    } else {
      alert("Debes iniciar sesión");
      window.location.href = "login.html";
    }
  });
}