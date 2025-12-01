<?php
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli


$docId = $_GET["id"];

$sql = "SELECT * FROM comentarios WHERE documento_id = $docId ORDER BY fecha DESC";
$comentarios = $conexion->query($sql);
?>

<h4>Comentarios</h4>

<div class="list-group mb-3">
<?php while ($c = $comentarios->fetch_assoc()) { ?>
    <div class="list-group-item">
        <strong><?= $c["usuario"] ?></strong><br>
        <?= $c["comentario"] ?>
        <br><small class="text-muted"><?= $c["fecha"] ?></small>
    </div>
<?php } ?>
</div>

<hr>

<h5>Agregar comentario</h5>

<form method="POST" action="agregar_comentario.php">
    <input type="hidden" name="docId" value="<?= $docId ?>">

    <label>Tu nombre:</label>
    <input type="text" name="usuario" class="form-control mb-2" required>

    <label>Comentario:</label>
    <textarea name="texto" class="form-control mb-2" required></textarea>

    <button class="btn btn-primary">Publicar</button>
</form>
