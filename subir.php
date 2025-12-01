<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

if ($_FILES["archivo"]["error"] === 0) {
    $pubId = $_POST["pubId"];
    $nombre = $_FILES["archivo"]["name"];
    $tipo = $_FILES["archivo"]["type"];
    $archivo = file_get_contents($_FILES["archivo"]["tmp_name"]);

    $stmt = $conexion->prepare("INSERT INTO documentos (nombre, archivo, tipo, usuario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nombre, $archivo, $tipo, $pubId);
    $stmt->send_long_data(1, $archivo);

    if ($stmt->execute()) {
        header("Location: ../dashboard/tid.php?ok=1");
    } else {
        echo "Error al guardar: " . $conexion->error;
    }
}
?>
