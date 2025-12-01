<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

$docId = $_POST["docId"];
$usuario = $_POST["usuario"];
$texto = $_POST["texto"];

$stmt = $conexion->prepare("INSERT INTO comentarios (documento_id, usuario, comentario) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $docId, $usuario, $texto);

if ($stmt->execute()) {
    header("Location: comentarios.php?id=" . $docId);
}
?>
