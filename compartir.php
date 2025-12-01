<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

$docId = $_POST["docId"];
$correo = $_POST["correoDestino"];

$stmt = $conexion->prepare("INSERT INTO compartidos (documento_id, compartido_con) VALUES (?, ?)");
$stmt->bind_param("is", $docId, $correo);

if ($stmt->execute()) {
    header("Location: ../dashboard/tid.php?shared=1");
} else {
    echo "Error: " . $conexion->error;
}
?>
