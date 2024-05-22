let paso = 1;
const pasoIncial = 1;
const pasoFinal = 3;

const cita = {
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [],
};

document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion(); //Muestra y oculta las secciones
  tabs(); //Cambia la sección cuando se muestren dos tabs
  botonesPaginador(); // Agrega o quita los botones del paginador
  paginaSiguiente();
  paginaAnterior();

  consultarAPI(); //Consulta el Json para los datos de la DB

  idCliente();
  nombreCliente(); //Trae el nombre del cliente al objeto de cita.
  selccionarFecha(); //Añade la fecha de la cita al objeto de cita.
  seleccionarHora(); //Añande la hora de la cita al objeto de cita.

  mostrarResumen(); //Muestra el resumen de la cita
}

function eliminarClase(nombreClase) {
  const eliminarAlgo = document.querySelector(`.${nombreClase}`);
  if (eliminarAlgo) {
    eliminarAlgo.classList.remove(`${nombreClase}`);
  }
}

function mostrarSeccion() {
  //Ocultar la seccion que tenga la clase de mostrar
  eliminarClase("mostrar");

  //Seleccionar la sección con el paso
  const pasoSelector = `#paso-${paso}`;
  const seccion = document.querySelector(pasoSelector);
  seccion.classList.add("mostrar");

  //Cambiar el color cuando el tab no es donde se está
  eliminarClase("actual");

  //Cambiar el color del tab
  const tab = document.querySelector(`[data-paso="${paso}"]`);
  tab.classList.add("actual");
}

function tabs() {
  const botones = document.querySelectorAll(".tabs button");
  botones.forEach((boton) => {
    boton.addEventListener("click", function (e) {
      paso = parseInt(e.target.dataset.paso);
      mostrarSeccion();
      botonesPaginador();
    });
  });
}

function botonesPaginador() {
  const paginaSiguiente = document.querySelector("#siguiente");
  const paginaAnterior = document.querySelector("#anterior");

  if (paso === 1) {
    paginaAnterior.classList.add("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  } else if (paso === 3) {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.add("ocultar");
    mostrarResumen();
  } else {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  }

  mostrarSeccion();
}

function paginaSiguiente() {
  const paginaSiguiente = document.querySelector("#siguiente");
  paginaSiguiente.addEventListener("click", function () {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}

function paginaAnterior() {
  const paginaAnterior = document.querySelector("#anterior");
  paginaAnterior.addEventListener("click", function () {
    if (paso <= pasoIncial) return;
    paso--;
    botonesPaginador();
  });
}

async function consultarAPI() {
  try {
    const url = "http://127.0.0.1:3000/api/servicios";
    const resultado = await fetch(url);
    const servicios = await resultado.json();
    mostrarServicios(servicios);
  } catch (error) {}
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio;

    //Crear el nombre del servicio
    const nombreServicio = document.createElement("P");
    nombreServicio.classList.add("nombre-servicio");
    nombreServicio.textContent = nombre;

    //Crear el precio del servicio
    const precioServicio = document.createElement("P");
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`;

    //Crear el contenedor del servicio
    const divServicio = document.createElement("DIV");
    divServicio.classList.add("servicio");
    divServicio.dataset.idServicio = id;
    divServicio.onclick = function () {
      seleccionarServicio(servicio);
    };

    divServicio.appendChild(nombreServicio);
    divServicio.appendChild(precioServicio);

    document.querySelector("#servicios").appendChild(divServicio);
  });
}

function seleccionarServicio(servicio) {
  const { id } = servicio;
  const { servicios } = cita;

  //Indentifica al elemento que se le da click
  const servicioDiv = document.querySelector(`[data-id-servicio="${id}"]`);

  //Comprobar si un servicio ya fue agregado
  if (servicios.some((agregado) => agregado.id === id)) {
    //Eliminar el objeto
    cita.servicios = servicios.filter((agregado) => agregado.id !== id);
    eliminarClase("seleccionado");
  } else {
    //Agregar el objeto
    cita.servicios = [...servicios, servicio];
    servicioDiv.classList.add("seleccionado");
  }
}

function idCliente() {
  cita.id = document.querySelector("#id").value;
}

function nombreCliente() {
  cita.nombre = document.querySelector("#nombre").value;
}

function selccionarFecha() {
  const inputfecha = document.querySelector("#fecha");
  inputfecha.addEventListener("input", function (e) {
    const dia = new Date(e.target.value).getUTCDay();
    if ([6, 0].includes(dia)) {
      e.target.value = "";
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      cita.fecha = e.target.value;
    }
  });
}

function seleccionarHora() {
  const inputhora = document.querySelector("#hora");
  inputhora.addEventListener("input", function (e) {
    const horaCita = e.target.value;
    const hora = horaCita.split(":")[0];
    if (hora < 10 || hora > 18) {
      e.target.value = "";
      mostrarAlerta("Hora no valida", "error", ".formulario");
    } else {
      cita.hora = e.target.value;
    }
  });
}

function mostrarAlerta(mensaje, tipo, lugar, desaparece = true) {
  //Evitar que salga muchas veces la alerta
  const alertaPrevia = document.querySelector(".alerta");
  if (alertaPrevia) {
    alertaPrevia.remove();
  }

  //Crea el div para la alerta y elige el tipo de alerta
  const alerta = document.createElement("DIV");
  alerta.classList.add("alerta");
  alerta.classList.add(tipo);

  //Crea el parrafo e inserta el mensaje en el div de alerta
  const mensajeAlerta = document.createElement("P");
  mensajeAlerta.textContent = mensaje;
  alerta.appendChild(mensajeAlerta);

  //Agrega la alerta en el formulario
  const formulario = document.querySelector(lugar);
  formulario.appendChild(alerta);

  //Evita que la alerta se mantenga más de 3 segundos
  if (desaparece) {
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}

function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen");

  //Limpiar el contenido del resumuen
  while (resumen.firstChild) {
    resumen.removeChild(resumen.firstChild);
  }
  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    mostrarAlerta(
      "Faltan  datos por completar",
      "error",
      ".contenido-resumen",
      false
    );
    return;
  }

  //Formatear el div de resumen
  const { nombre, fecha, hora, servicios } = cita;

  const nombreCliente = document.createElement("P");
  nombreCliente.innerHTML = `<span>Nombre: </span>${nombre}`;

  //Formatear la fecha en español
  const fechaObj = new Date(fecha);
  const mes = fechaObj.getMonth();
  const dia = fechaObj.getDate() + 2;
  const year = fechaObj.getFullYear();

  const fechaUTC = new Date(Date.UTC(year, mes, dia));
  const opcionesFecha = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const fechaFormateada = fechaUTC.toLocaleDateString("es-MX", opcionesFecha);

  const fechaCita = document.createElement("P");
  fechaCita.innerHTML = `<span>Fecha: </span>${fechaFormateada}`;

  const horaCita = document.createElement("P");
  horaCita.innerHTML = `<span>Hora: </span>${hora} horas`;

  // Boton para Crear una cita
  const botonReservar = document.createElement("BUTTON");
  botonReservar.classList.add("btn-azul");
  botonReservar.textContent = "Reservar Cita";
  botonReservar.onclick = reservarCita;

  //Heading para servicios en resumen
  const headingServicios = document.createElement("H3");
  headingServicios.textContent = "Resumen de servicios";

  resumen.appendChild(headingServicios);

  //Heading para servicios en resumen
  const headingUsuario = document.createElement("H3");
  headingUsuario.textContent = "Resumen de los datos de la cita";

  resumen.appendChild(headingUsuario);

  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);

  //Iterando en los servicios
  servicios.forEach((servicio) => {
    const { id, precio, nombre } = servicio;
    const contenedorServicio = document.createElement("DIV");
    contenedorServicio.classList.add("contenedor-servicio");

    const textoServicio = document.createElement("P");
    textoServicio.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.innerHTML = `<span>Precio: </span> $${precio}`;

    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioServicio);

    resumen.appendChild(contenedorServicio);
  });

  resumen.appendChild(botonReservar);
}

async function reservarCita() {
  const { nombre, fecha, hora, servicios, id } = cita;

  const idServicios = servicios.map((servicio) => servicio.id); //A diferencia de For Each, Map lo hace solo por coincidencias, servicio.id selecciona solo los id

  const datos = new FormData();
  datos.append("fecha", fecha);
  datos.append("hora", hora);
  datos.append("usuarioid", id);
  datos.append("servicios", idServicios);

  try {
    //Peticion hacia la API
    const url = "http://127.0.0.1:3000/api/citas";
    const respuesta = await fetch(url, {
      method: "POST",
      body: datos,
    });

    const resultado = await respuesta.json();
    if (resultado.resultado.resultado) {
      Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
        button: "OK",
      }).then(() => {
        setTimeout(() => {
          window.location.reload();
        }, 3000);
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Algo no salio bien!",
    });
  }
}
