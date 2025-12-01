<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

$id = $_GET["id"];
$sql = "SELECT * FROM documentos WHERE id = $id LIMIT 1";
$res = $conexion->query($sql);
$d = $res->fetch_assoc();

header("Content-Type: " . $d["tipo"]);
echo $d["archivo"];
?>
