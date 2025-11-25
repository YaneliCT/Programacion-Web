<?php
include("conexion.php");

$id = $_GET["id"];

$sql = "DELETE FROM usuario WHERE Id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Usuario eliminado correctamente.";
} else {
    echo "Error al eliminar usuario: " . $stmt->error;
}
?>


