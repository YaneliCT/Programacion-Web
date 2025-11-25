<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "gobierno";

$conexion = new mysqli($server, $user, $pass, $db);

if ($conexion->connect_errno){
    die("Conexion Fallida" . $conexion->connect_errno);
}else{
    echo("conectado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - DIRECCIÓN</title>
  <link rel="stylesheet" href="estilos.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

  <nav class="barra-superior">
    <ul>
      <li><a href="#">Capacitaciones</a></li>
      <li><a href="#">Visitas Guiadas</a></li>
      <li><a href="#">Transferencias</a></li>
      <li><a href="#">Asesorías</a></li>
    </ul>
  </nav>

  <div class="container">
    <aside class="barra-tareas">
      <div class="usuario-icono">
        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="50" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
        </svg>
      </div>

      <div class="datos-usuario">
        <p id="nombre-usuario"></p>
        <p id="correo-usuario"></p>
      </div>

      <h4>Departamento: DIR - DIRECCIÓN</h4>

      <ul>
        <li>Inicio</li>
        <li>Archivos</li>
        <li>Configuración</li>
      </ul>

      <button id="cerrar-sesion" style="margin-top:20px;">Cerrar sesión</button>
    </aside>

    <main class="contenido">
      <div class="saludo">
        <p>¡Bienvenido al departamento de Dirección!</p>
      </div>

      <div class="botones">
        <input type="file" id="inputArchivo" accept="application/pdf" style="display: none;">
        <button id="btnSubir">Subir</button>
        <button id="btnCrear">Crear</button>
        <button id="btnDescargar">Descargar</button>
      </div>

      <section class="archivos-subidos">
        <h3>Archivos subidos</h3>
        <div class="lista-archivos" id="listaArchivos"></div>
      </section>
    </main>
  </div>

  <!-- PDF.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

  <script>
    /* ==============================
           VALIDACIÓN DE USUARIO
    =============================== */
    const usuarioActivo = JSON.parse(localStorage.getItem("usuarioActivo"));

    if (!usuarioActivo) {
      alert("Debes iniciar sesión primero.");
      window.location.href = "login.php";
    } else {
      document.getElementById("nombre-usuario").textContent =
        usuarioActivo.nombre + " " + usuarioActivo.apellidos;
      document.getElementById("correo-usuario").textContent =
        usuarioActivo.correo;
    }

    document.getElementById("cerrar-sesion").addEventListener("click", () => {
      localStorage.removeItem("usuarioActivo");
      window.location.href = "login.php";
    });

    /* ==============================
            SISTEMA DE ARCHIVOS PDF
    =============================== */

    const inputArchivo = document.getElementById("inputArchivo");
    const listaArchivos = document.getElementById("listaArchivos");
    let archivos = [];

    document.getElementById("btnSubir").addEventListener("click", () => inputArchivo.click());

    inputArchivo.addEventListener("change", async (event) => {
      const archivo = event.target.files[0];

      if (!archivo) return;

      // Validar solo PDF
      if (archivo.type !== "application/pdf") {
        alert("Solo se permiten archivos PDF.");
        event.target.value = "";
        return;
      }

      const url = URL.createObjectURL(archivo);
      const miniatura = await generarMiniaturaPDF(url);

      archivos.push({ archivo, nombre: archivo.name, url, miniatura });

      mostrarArchivos();
      event.target.value = "";
    });

    async function generarMiniaturaPDF(url) {
      const pdf = await pdfjsLib.getDocument(url).promise;
      const pagina = await pdf.getPage(1);

      const viewport = pagina.getViewport({ scale: 0.35 });
      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");

      canvas.width = viewport.width;
      canvas.height = viewport.height;

      await pagina.render({ canvasContext: ctx, viewport }).promise;

      return canvas.toDataURL("image/png");
    }

    function mostrarArchivos() {
      listaArchivos.innerHTML = "";

      archivos.forEach((a, i) => {
        const div = document.createElement("div");
        div.classList.add("archivo-item");

        div.innerHTML = `
          <img src="${a.miniatura}" class="miniatura-pdf">

          <p class="nombre-archivo" title="${a.nombre}">
            ${a.nombre.length > 17 ? a.nombre.substring(0, 17) + "..." : a.nombre}
          </p>

          <button class="btn-eliminar" data-index="${i}">
            <i class="bi bi-trash"></i>
          </button>
        `;

        listaArchivos.appendChild(div);
      });

      // BOTONES DE ELIMINAR
      document.querySelectorAll(".btn-eliminar").forEach(btn => {
        btn.addEventListener("click", function () {
          const index = this.getAttribute("data-index");

          if (confirm("¿Seguro que deseas eliminar este documento?")) {
            archivos.splice(index, 1);
            mostrarArchivos();
          }
        });
      });
    }
  </script>

</body>
</html>
