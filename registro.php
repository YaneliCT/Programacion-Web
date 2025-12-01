<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

// Procesar registro desde PHP
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $paterno = $_POST["paterno"];
    $materno = $_POST["materno"];
    $correo = $_POST["correo"];
    $sexo = $_POST["sexo"];
    $direccion = $_POST["direccion"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
    $rol = 2;
    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

   // Obtener el correo del departamento
    $sql_correo = "SELECT Correo_Direccion FROM direccion WHERE Id_direccion = ?";
    $stmt_correo = $conexion->prepare($sql_correo);
    $stmt_correo->bind_param("i", $id_direccion);
    $stmt_correo->execute();
    $stmt_correo->bind_result($correo_departamento);
    $stmt_correo->fetch();
    $stmt_correo->close();

    if (!$correo_departamento){
       die("Error: El departamento no tiene un correo registrado.");
    } 
    //INSERTAR AL USUARIO
     $sql = "INSERT INTO usuario 
            (Nombre, Apellido_P, Apellido_M, Correo, Password, Sexo, Id_direccion, Id_rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "ssssssii",
        $nombre,
        $apellido_p,
        $apellido_m,
        $correo_departamento,  // correo del departamento
        $passwordHash,
        $sexo,
        $id_direccion,
        $id_rol
    );

    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
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
  <title>Registrar usuario</title>
  <link rel="stylesheet" href="style.css" />
  </head>
<body>
  <div class="container">
    <div class="info">
      <p class="txt-1">Crea tu cuenta</p>
      <h2>Registro de usuario</h2>
      <hr />
      <p class="txt-2">Ingresa tus datos para registrarte en la plataforma.</p>
    </div>
    <!-- AHORA el formulario envía datos a PHP -->
    <form class="form" autocomplete="off" method="POST">

      <h2>Formulario de Registro</h2>
      <div class="input">
        <input type="text" name="nombre" class="box" placeholder="Nombre" required />
        <input type="text" name="paterno" class="box" placeholder="Apellido paterno" required />
        <input type="text" name="materno" class="box" placeholder="Apellido materno" required />
        <input type="email" name="correo" class="box" placeholder="Correo electrónico" required />
        <input type="password" name="contrasena" class="box" placeholder="Contraseña" required />
         <!-- Sexo -->
        <select name="sexo" class="box" required>
          <option value="">Seleccione sexo</option>
          <option value="Hombre">Hombre</option>
          <option value="Mujer">Mujer</option>
        </select>
        <!-- Selección de DIRECCIÓN desde la BD -->
        <select id="departamento" name="direccion" class="box" required>
          <option value="">Selecciona un departamento</option>
          <option value="1">DAE - DESARROLLO ARCHIVISTICO ESTATAL</option>
          <option value="2">TID - TECNOLOGIA INFORMATICA Y DIFUSION</option>
          <option value="3">DA - DEPARTAMENTO ADMINISTRATIVO</option>
          <option value="4">DJ - DEPARTAMENTO JURIDICO</option>
          <option value="5">DIR - DIRECCION</option>
        </select>

        <input type="submit" value="Registrar" class="submit" />
        <a href="login.php" class="btn" style="display:block; margin-top:10px;">Volver al login</a>
      </div>
    </form>
  </div>
</body>
</html>
