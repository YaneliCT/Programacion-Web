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
  <title>Dashboard DAE</title>
  <link rel="stylesheet" href="estilos.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    .archivo-item {
      width: 150px;
      text-align: center;
      margin: 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .archivo-nombre {
      max-width: 130px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-top: 5px;
      cursor: default;
    }

    .lista-archivos {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    /* MINIATURA BASE */
    .miniatura-pdf {
      width: 120px;
      height: 150px;
      border: 1px solid #ccc;
      object-fit: cover;
      background: #f2f2f2;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      color: #555;
      transition: transform 0.25s ease-in-out, z-index 0.25s;
      position: relative;
    }

    /* EFECTO AL PASAR EL CURSOR (AGRANDAR MINIATURA) */
    .miniatura-pdf:hover {
      transform: scale(1.7);
      z-index: 10;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .acciones {
      margin-top: 6px;
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .acciones i {
      cursor: pointer;
      font-size: 18px;
    }
  </style>
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
      <h4>Departamento: TID - TECNOLOGÍA INFORMÁTICA Y DIFUSIÓN</h4>
      <ul>
        <li>Inicio</li>
        <li>Archivos</li>
        <li>Configuración</li>
      </ul>
      <button id="cerrar-sesion" style="margin-top:20px;">Cerrar sesión</button>
    </aside>

    <main class="contenido">
      <div class="saludo">
        <p>¡Bienvenido al departamento de Tecnología Informática y Difusión!</p>
      </div>

      <div class="botones">
        <!-- SOLO PDF PERMITIDO -->
        <input type="file" id="inputArchivo" accept="application/pdf" style="display:none;">
        <button id="btnSubir">Subir</button>
        <button id="btnCrear">Crear</button>
      </div>

      <section class="archivos-subidos">
        <h3>Archivos subidos</h3>
        <div class="lista-archivos" id="listaArchivos"></div>
      </section>
    </main>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

  <script>
    const archivos = [];

    document.getElementById("btnSubir").addEventListener("click", () => {
      document.getElementById("inputArchivo").click();
    });

    document.getElementById("inputArchivo").addEventListener("change", async function () {
      const archivo = this.files[0];
      if (!archivo) return;

      /* VALIDACIÓN: SOLO PERMITIR PDF */
      if (archivo.type !== "application/pdf") {
        alert("Solo se permiten archivos PDF.");
        this.value = "";
        return;
      }

      const url = URL.createObjectURL(archivo);
      let miniatura = await generarMiniaturaPDF(url);

      archivos.push({ nombre: archivo.name, url, miniatura });
      mostrarArchivos();
    });

    function mostrarArchivos() {
      const lista = document.getElementById("listaArchivos");
      lista.innerHTML = "";

      archivos.forEach((file, index) => {
        const div = document.createElement("div");
        div.className = "archivo-item";

        div.innerHTML = `
          <img src="${file.miniatura}" class="miniatura-pdf">
          <div class="archivo-nombre" title="${file.nombre}">${file.nombre}</div>
          <div class="acciones">
            <i class="bi bi-download" onclick="descargarArchivo(${index})"></i>
            <i class="bi bi-trash" onclick="eliminarArchivo(${index})"></i>
          </div>
        `;

        lista.appendChild(div);
      });
    }

    function descargarArchivo(i) {
      const a = document.createElement("a");
      a.href = archivos[i].url;
      a.download = archivos[i].nombre;
      a.click();
    }

    /* CONFIRMACIÓN PARA ELIMINAR ARCHIVO */
    function eliminarArchivo(i) {
      const confirmar = confirm("¿Estás seguro de eliminar este documento?");
      if (confirmar) {
        archivos.splice(i, 1);
        mostrarArchivos();
      }
    }

    async function generarMiniaturaPDF(url) {
      const pdf = await pdfjsLib.getDocument(url).promise;
      const page = await pdf.getPage(1);
      const scale = 0.5;
      const viewport = page.getViewport({ scale });

      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");
      canvas.width = viewport.width;
      canvas.height = viewport.height;

      await page.render({ canvasContext: ctx, viewport }).promise;

      return canvas.toDataURL();
    }

    /* SESIÓN */
    const usuarioActivo = JSON.parse(localStorage.getItem("usuarioActivo"));
    if (!usuarioActivo) {
      alert("Debes iniciar sesión primero.");
      window.location.href = "login.html";
    } else {
      document.getElementById("nombre-usuario").textContent =
        usuarioActivo.nombre + " " + usuarioActivo.apellidos;
      document.getElementById("correo-usuario").textContent = usuarioActivo.correo;
    }

    document.getElementById("cerrar-sesion").addEventListener("click", () => {
      localStorage.removeItem("usuarioActivo");
      window.location.href = "login.php";
    });
  </script>

</body>
</html>
