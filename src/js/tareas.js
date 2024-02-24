(function () {
  // Arreglo de tareas de un proyecto para Virtual DOM
  let tareas = [];
  let filtradas = [];

  obtenerTareas();

  // Mostrar el modal de agregar tarea
  const btnNuevaTarea = document.querySelector("#agregar-tarea");
  btnNuevaTarea.addEventListener("click", () => {
    mostrarFormulario();
  });

  /** Filtros de busqeuda de tareas */
  const filtros = document.querySelectorAll("#filtros input[type='radio']");
  filtros.forEach((radio) => {
    radio.addEventListener("input", filtrarTareas);
  });

  /** Realiza el filtrado de las tareas */
  function filtrarTareas(e) {
    const filtro = e.target.value;

    if (filtro !== "") {
      filtradas = tareas.filter((tarea) => tarea.estado === filtro); // Comprobar el valor del estado de la tarea con el del filtro
    } else {
      filtradas = []; // Vaciar el arreglo de tareas filtyradas para mostrar TODAS las tareas
    }

    mostarTareas(); // Obtener las tareas
  }

  /** Realiza un llamado Fecth para obtener las tareas relacionadas a cada proytecto */
  async function obtenerTareas() {
    try {
      const id = obtenerProyecto();
      const url = `${window.origin}/api/tareas?proyecto=${id}`; // URL de la api
      const respuesta = await fetch(url);
      const resultado = await respuesta.json();

      tareas = resultado.tareas; // Asignar las taeas obtenidas de la api al arreglo de las tareas

      mostarTareas();
    } catch (error) {
      console.log(error);
    }
  }

  /** Muestra las tareas en el HTML con Scripting */
  function mostarTareas() {
    limiarTareas();

    // Obtener número de tareas pendientes y completadas
    totalPendientes();
    totalCompletas();

    // Comprobar si se han seleccionado tareas filtradas
    const arrayTareas = filtradas.length ? filtradas : tareas; // Asignar tareas de forma dinámica (Todas o Filtradas)

    // Validación en caso de que no existan tareas para un proyecto
    if (arrayTareas.length === 0) {
      const contenedorTareas = document.querySelector("#listado-tareas");
      const noTareas = document.createElement("LI");
      noTareas.textContent = "No hay tareas";
      noTareas.classList.add("no-tareas");
      contenedorTareas.appendChild(noTareas);

      return;
    }

    // Diccionario para estados relacionados con los registros de la base de datos (Estados visuales para el usuario)
    const estados = {
      0: "Pendiente",
      1: "Completa",
    };

    // Iterar las tareas del JSON obtenido de fetch
    arrayTareas.forEach((tarea) => {
      const { id, nombre, estado, proyectoId } = tarea;
      const contenedorTarea = document.createElement("LI"); // Contenedor para una tarea
      contenedorTarea.dataset.tareaId = id;
      contenedorTarea.classList.add("tarea");

      const nombreTarea = document.createElement("P"); // Nombre de la tarea
      nombreTarea.textContent = tarea.nombre;
      nombreTarea.ondblclick = () => {
        mostrarFormulario((editar = true), { ...tarea });
      };

      const opcionesDiv = document.createElement("DIV"); // Sección con opciones para las tareas
      opcionesDiv.classList.add("opciones");

      // Botones de acciones de estado
      const btnEstado = document.createElement("BUTTON");
      btnEstado.classList.add("estado-tarea");
      btnEstado.classList.add(`${estados[tarea.estado].toLowerCase()}`);
      btnEstado.textContent = estados[tarea.estado];
      btnEstado.dataset.estadoTarea = tarea.estado;

      btnEstado.ondblclick = () => {
        cambiarEstado({ ...tarea }); // Copia del objeto original (Para no modificar objetos originales antes de tiempo)
      };

      // Boton para eliminar tareas
      const btnEliminar = document.createElement("BUTTON");
      btnEliminar.classList.add("eliminar-tarea");
      btnEliminar.dataset.idTarea = tarea.id;
      btnEliminar.textContent = "Eliminar Tarea";
      btnEliminar.ondblclick = () => {
        confirmarEliminarTarea(tarea);
      };

      //  Agregar los elementos al contendor generico de tareas
      opcionesDiv.appendChild(btnEstado);
      opcionesDiv.appendChild(btnEliminar);
      contenedorTarea.appendChild(nombreTarea);
      contenedorTarea.appendChild(opcionesDiv);

      // Agregar al HTML
      const listadoTareas = document.querySelector("#listado-tareas");
      listadoTareas.appendChild(contenedorTarea);
    });
  }

  /** Cambia el estado de una tarea (pendiente/completa) */
  function cambiarEstado(tarea) {
    const nuevoEstado = tarea.estado === "1" ? "0" : "1"; // Ternario para evaluar el estado de tarea nueva
    tarea.estado = nuevoEstado;

    actualizarTarea(tarea);
  }

  /** Petición al servidor para actualizar una tarea */
  async function actualizarTarea(tarea) {
    const { id, estado, nombre } = tarea; // Obtener datos de la tarea

    const datos = new FormData(); // Datos del formulario
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", obtenerProyecto());

    // for (let value of datos.values()) {
    //   console.log(value);
    // }

    try {
      // Construir la petición
      const url = `${window.origin}/api/tareas/actualizar`;
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();

      // Mostrar alerta en caso de ejecución correcta
      if (resultado.respuesta.tipo === "exito") {
        Swal.fire({
          icon: "success",
          title: resultado.respuesta.mensaje,
          text: "Estado de la Tarea Actualizado",
        });

        const modal = document.querySelector(".modal");
        if (modal) {
          modal.remove();
        }

        // Modificando el Virtual DOM de tareas
        tareas.map((tareaMemoria) => {
          if (tareaMemoria.id === id) {
            // Identificar si la tarea en memoria es la misma que se ha a actualizado
            tareaMemoria.estado = estado;
            tareaMemoria.nombre = nombre;
          }
          return tareaMemoria; // Retornar el cambio realizado
        });

        mostarTareas(); // Mostrar los nuevos cambios
      }
    } catch (error) {
      console.log(error);
    }
  }

  function confirmarEliminarTarea(tarea) {
    Swal.fire({
      title: "¿Eliminar Tarea?",
      icon: "warning",
      text: "Una vez eliminada no podrá recuperarse",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar",
      confirmButtonText: "Si, eliminar",
    }).then((result) => {
      if (result.isConfirmed) {
        eliminarTarea(tarea);
      }
    });
  }

  async function eliminarTarea(tarea) {
    const datos = new FormData();
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", obtenerProyecto());

    try {
      const url = `${window.origin}/api/tareas/eliminar`;
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();
      if (resultado.resultado) {
        Swal.fire({
          icon: "success",
          title: resultado.mensaje,
          text: "Tarea Eliminada",
        });

        // Crear un arreglo nuevo fliltrado con las tareas que no se eliminaron
        tareas = tareas.filter((tareaMemoria) => tareaMemoria.id !== tarea.id);
        mostarTareas();
      }
    } catch (error) {
      console.log(error);
    }
  }

  /** Habilitar y deshabilitar el radio te tareas pendientes en caso de que no existan tareas pendientes */
  function totalPendientes() {
    const totalPendientes = tareas.filter((tarea) => tarea.estado === "0");
    const pendienteRadio = document.querySelector("#pendientes");

    if (totalPendientes.length === 0) {
      pendienteRadio.disabled = true;
    } else {
      pendienteRadio.disabled = false;
    }
  }

  /** Habilitar y deshabilitar el radio te tareas completas en caso de que no existan tareas completas */
  function totalCompletas() {
    const totalCompletas = tareas.filter((tarea) => tarea.estado === "1");
    const completadasRadio = document.querySelector("#completadas");

    if (totalCompletas.length === 0) {
      completadasRadio.disabled = true;
    } else {
      completadasRadio.disabled = false;
    }
  }

  /** Crea el modal del formulario de creación de tareas y lo inserta en el HTML */
  function mostrarFormulario(editar = false, tarea = {}) {
    console.log(tarea);
    console.log(editar);
    const modal = document.createElement("DIV"); // Crear el contenedor del modal
    modal.classList.add("modal"); // Agregar clase al contenedor

    // Agregar HTML al contenedor
    modal.innerHTML = `<form class="formulario nueva-tarea">
        <legend>${editar ? "Editar Tarea" : "Añade una Nueva Tarea"}</legend>
        <div class="campo">
          <label for="tarea">Tarea</label>
          <input
            type="text"
            name="tarea"
            placeholder="${
              tarea.nombre
                ? "Editar la Tarea"
                : "Añadir Tarea al Proyecto Actual"
            }"
            id="tarea",
            value="${tarea.nombre ? tarea.nombre : ""}"
          />
        </div>
        <div class="opciones">
          <input
            type="submit"
            class="submit-nueva-tarea"
            value="${tarea.nombre ? "Guardar Cambios" : "Añadir Tarea"}"
          />
          <button type="button" class="cerrar-modal">
            Cancelar
          </button>
        </div>
      </form>`;

    //? Puede ejecutarse por el Modelo de Concurrencia y Loop de Eventos
    setTimeout(() => {
      // Agregar clase para animación
      const formulario = document.querySelector(".formulario");
      formulario.classList.add("animar");
    }, 0);

    document.querySelector(".dashboard").appendChild(modal); // Agregar el modal al HTML

    //? Delegation => (Detectar a que elemento de le damos click)
    modal.addEventListener("click", (e) => {
      e.preventDefault();
      // Click al boton de cancelar
      if (e.target.classList.contains("cerrar-modal")) {
        const formulario = document.querySelector(".formulario");
        formulario.classList.add("cerrar");
        // Remover el modal
        setTimeout(() => {
          modal.remove();
        }, 300);
      }

      // Funcionamiento para boton de guardar tarea
      if (e.target.classList.contains("submit-nueva-tarea")) {
        const tareaNombre = document.querySelector("#tarea").value.trim(); // Valor de la tarea

        if (tareaNombre === "") {
          // Mostrar una alerta de error
          mostarAlerta(
            "El nombre de la tarea es obligatorio",
            "error",
            document.querySelector(".formulario legend")
          );

          return; // Detiene la ejecución de más código
        }

        if (editar) {
          tarea.nombre = tareaNombre;
          actualizarTarea(tarea);
        } else {
          agregarTarea(tareaNombre);
        }
      }
    });
  }

  /** Muestra una alerta en la interfaz */
  function mostarAlerta(mensaje, tipo, referencia) {
    // Prevenir  la creación de multiples alertas
    const alertaPrevia = document.querySelector(".alerta");
    if (alertaPrevia) {
      alertaPrevia.remove();
    }

    const alerta = document.createElement("DIV");
    alerta.classList.add("alerta", tipo);
    alerta.textContent = mensaje;

    // Insertar la alerta antes del legend del formulario
    referencia.parentElement.insertBefore(
      alerta,
      referencia.nextElementSibling
    );

    // Eliminar la tarea después de 5s
    setTimeout(() => {
      alerta.remove();
    }, 5000);
  }

  /** Realiza la petición al servidor para almacenar una tarea en la BD */
  async function agregarTarea(tarea) {
    // Construir la petición
    const datos = new FormData();
    datos.append("nombre", tarea);
    datos.append("proyectoId", obtenerProyecto());

    // Realizar la petición
    try {
      const url = `${window.origin}/api/tareas`;
      const respuesta = await fetch(url, {
        method: "post",
        body: datos,
      });

      const resultado = await respuesta.json();
      console.log(resultado);

      mostarAlerta(
        resultado.mensaje,
        resultado.tipo,
        document.querySelector(".formulario legend")
      );

      // Ejecución correcta
      if (resultado.tipo === "exito") {
        const modal = document.querySelector(".modal");
        setTimeout(() => {
          modal.remove();
        }, 2000);

        // Agregar el objeto de tareas al global de tareas
        const tareaObj = {
          id: String(resultado.id), // Convertir el ID a un String
          nombre: tarea,
          estado: "0",
          proyectoId: resultado.proyectoId,
        };

        tareas = [...tareas, tareaObj]; // Copia del arreglo de tareas mas la tarea nueva que se agrega

        mostarTareas();
      }
    } catch (error) {
      console.log(error);
    }
  }

  /**
   * Obtener la url del proyecto actual
   */
  function obtenerProyecto() {
    const proyectoParam = new URLSearchParams(window.location.search);
    const proyecto = Object.fromEntries(proyectoParam);
    return proyecto.proyecto;
  }

  /** Limpia el arreglo de tareas para evitar registros duplicadios en la interfaz de usuario */
  function limiarTareas() {
    const listadoTareas = document.querySelector("#listado-tareas");
    // listadoTareas.innerHTML = ""; -> forma 1 (más lenta)

    while (listadoTareas.firstChild) {
      // Iterar los elementos y limpiar el HTML para eliminar el contenido
      listadoTareas.removeChild(listadoTareas.firstChild);
    }
  }
})(); //* IIFE -> (Immediately Invoked Function Expression) => (Expresión de Functión Ejecutada Inmediataente.)
