<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = $_POST["correo"];
    $password = $_POST["contrasena"]; // ojo: tu formulario usa "contrasena", no "password"

    // Consulta preparada
    $sql = "SELECT u.Id_usuario, u.Password, u.Id_rol, d.Nombre_direccion, u.Nombre
            FROM usuario u
            LEFT JOIN direccion d ON u.Id_direccion = d.Id_direccion
            WHERE u.Correo = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_usuario, $hash, $id_rol, $nombre_direccion, $nombre);
        $stmt->fetch();

        // Verificar contraseña con hash
        if (password_verify($password, $hash)) {

            // Guardar datos en sesión
            $_SESSION["usuario"]   = $id_usuario;
            $_SESSION["rol"]       = $id_rol;
            $_SESSION["nombre"]    = $nombre;
            $_SESSION["direccion"] = $nombre_direccion;

            // Redirección según rol o dirección
            if ($id_rol == 1) {
                header("Location: admin/dashboard.php");
            } else {
                switch ($nombre_direccion) {
                    case "DAE-DESARROLLO ARCHIVISTICO ESTATAL":
                        header("Location: dashboard_dae.php");
                        break;
                    case "TID-TECNOLOGIA INFORMATICA Y DIFUSION":
                        header("Location: dashboard_tid.php");
                        break;
                    case "DA-DEPARTAMENTO ADMINISTRATIVO":
                        header("Location: dashboard_da.php");
                        break;
                    case "DJ-DEPARTAMENTO JURIDICO":
                        header("Location: dashboard_dj.php");
                        break;
                    case "DIR-DIRECCION":
                        header("Location: dashboard_dir.php");
                        break;
                    default:
                        echo "<script>alert('Dirección no reconocida.');</script>";
                }
            }
            exit();

        } else {
            echo "<script>alert('Contraseña incorrecta.');</script>";
        }

    } else {
        echo "<script>alert('Usuario no encontrado.');</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="style.css" />
  </head>
<body>
 <div class="container">
    <div class="info">
      <p class="txt-1">¡Bienvenido!</p>
      <h2>Gracias por visitarnos</h2>
      <hr />
      <p class="txt-2">"Cada calle, un recuerdo. Cada esquina, un susurro del pasado que nos guía hacia el futuro."</p>
    </div>
 <form class="form" autocomplete="off" method="POST">
      <h2>Iniciar sesión</h2>
      <p>"Plataforma Integral de Gestión y Difusión Archivística de Alto Nivel"</p>

      <div class="input">
        <input type="email" id="correo" name="correo" class="box" placeholder="Ingresa tu correo" required />
        <input type="password" id="contrasena" name="contrasena" class="box" placeholder="Ingresa tu contraseña" required />

        <a href="registro.php" class="btn" style="margin-top: 10px;">Registrar usuario</a>
        <input type="submit" value="Iniciar sesión" class="submit" />
      </div>
    </form>
  </div>
</body>
</html>
