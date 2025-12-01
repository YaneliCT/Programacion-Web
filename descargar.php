<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

$id = $_GET["id"];
$sql = "SELECT * FROM documentos WHERE id = $id LIMIT 1";
$res = $conexion->query($sql);
$d = $res->fetch_assoc();

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $d["nombre"] . "\"");
echo $d["archivo"];
?>
