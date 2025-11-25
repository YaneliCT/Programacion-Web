<?php
include("conexion.php");

$id = $_GET["id"];

// Cargar datos del usuario
$sql = "SELECT * FROM usuario WHERE Id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $apellido_p = $_POST["apellido_p"];
    $apellido_m = $_POST["apellido_m"];
    $sexo = $_POST["sexo"];
    $id_direccion = $_POST["id_direccion"];

    $sql_update = "UPDATE usuario 
                   SET Nombre=?, Apellido_P=?, Apellido_M=?, Sexo=?, Id_direccion=? 
                   WHERE Id_usuario=?";

    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("ssssii", 
        $nombre, $apellido_p, $apellido_m, $sexo, $id_direccion, $id);

    if ($stmt_update->execute()) {
        echo "Usuario actualizado correctamente.";
    } else {
        echo "Error al actualizar: " . $stmt_update->error;
    }
}
?>

<form method="POST">
    Nombre: <input type="text" name="nombre" value="<?= $usuario['Nombre'] ?>"><br>
    Apellido Paterno: <input type="text" name="apellido_p" value="<?= $usuario['Apellido_P'] ?>"><br>
    Apellido Materno: <input type="text" name="apellido_m" value="<?= $usuario['Apellido_M'] ?>"><br>
    Sexo: 
    <select name="sexo">
        <option <?= $usuario['Sexo']=="Hombre"?"selected":"" ?>>Hombre</option>
        <option <?= $usuario['Sexo']=="Mujer"?"selected":"" ?>>Mujer</option>
    </select><br>
    Dirección: <input type="number" name="id_direccion" value="<?= $usuario['Id_direccion'] ?>"><br>

    <button type="submit">Guardar cambios</button>
</form>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Modificar Usuario</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<form class="form" method="POST">
<h2>Modificar Usuario</h2>

<input type="text" name="nombre" class="box" value="<?= $usuario['Nombre'] ?>" required>
<input type="text" name="paterno" class="box" value="<?= $usuario['Ap_Paterno'] ?>" required>
<input type="text" name="materno" class="box" value="<?= $usuario['Am_Materno'] ?>">

<input type="email" name="correo" class="box" value="<?= $usuario['Correo'] ?>" required>

<select name="sexo" class="box" required>
    <option value="Femenino" <?= ($usuario["Sexo"] == "Femenino") ? "selected" : "" ?>>Femenino</option>
    <option value="Masculino" <?= ($usuario["Sexo"] == "Masculino") ? "selected" : "" ?>>Masculino</option>
</select>

<select name="direccion" class="box" required>
    <option value="1" <?= ($usuario["Id_direccion"] == 1) ? "selected" : "" ?>>DAE - Desarrollo Archivístico Estatal</option>
    <option value="2" <?= ($usuario["Id_direccion"] == 2) ? "selected" : "" ?>>TID - Tecnología Informática y Difusión</option>
    <option value="3" <?= ($usuario["Id_direccion"] == 3) ? "selected" : "" ?>>DA - Departamento Administrativo</option>
    <option value="4" <?= ($usuario["Id_direccion"] == 4) ? "selected" : "" ?>>DJ - Departamento Jurídico</option>
    <option value="5" <?= ($usuario["Id_direccion"] == 5) ? "selected" : "" ?>>DIR - Dirección</option>
</select>

<input type="submit" value="Guardar Cambios" class="submit">
<a href="usuarios.php" class="btn">Cancelar</a>

</form>
</div>

</body>
</html>
