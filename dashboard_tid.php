<?php
// --------------------- CONEXIÓN BD ----------------------
session_start();
include("conexion.php"); // aquí tienes tu conexión mysqli

// Obtener lista de publicaciones
$sql = "SELECT * FROM publicaciones ORDER BY Id_publicacion DESC";
$publicaciones = $conexion->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard TID</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f4f4f4; }
        .card { border-radius: 12px; }
        iframe { width: 100%; height: 450px; border: none; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Panel de Publicaciones - TID</h2>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Imágenes</th>
                <th>Documentos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($p = $publicaciones->fetch_assoc()) { ?>
            <tr>
                <td><?= $p["Id_publicacion"] ?></td>
                <td><?= $p["Titulo"] ?></td>
                <td><?= $p["Tipo_publicacion"] ?></td>

                <!-- IMÁGENES RELACIONADAS -->
                <td>
                    <?php
                    $sqlImg = "SELECT * FROM imagenes WHERE Id_publicacion = " . $p["Id_publicacion"];
                    $imgs = $conexion->query($sqlImg);

                    while ($img = $imgs->fetch_assoc()) {
                        echo "<span class='badge bg-info text-dark'>{$img['Descripcion']}</span> ";
                    }
                    ?>
                </td>

                <!-- DOCUMENTOS RELACIONADOS -->
                <td>
                    <?php
                    $sqlDoc = "SELECT * FROM documentos WHERE usuario = '{$p['Id_publicacion']}'";
                    $docs = $conexion->query($sqlDoc);

                    while ($d = $docs->fetch_assoc()) {
                        echo "
                            <div class='d-flex align-items-center gap-2'>
                                <span class='badge bg-secondary'>{$d['nombre']}</span>
                                <a href='../documentos/descargar.php?id={$d['id']}' class='btn btn-sm btn-success'>Descargar</a>
                                <button onclick='verPDF({$d['id']})' class='btn btn-sm btn-primary'>Ver</button>
                                <button onclick='abrirComentarios({$d['id']})' class='btn btn-sm btn-warning'>Comentarios</button>
                                <button onclick='abrirCompartir({$d['id']})' class='btn btn-sm btn-info'>Compartir</button>
                            </div>
                        ";
                    }
                    ?>
                </td>

                <!-- ACCIONES -->
                <td>
                    <button class="btn btn-sm btn-primary" onclick="abrirSubir(<?= $p['Id_publicacion'] ?>)">Subir Documento</button>
                </td>
            </tr>

        <?php } ?>

        </tbody>
    </table>
</div>

<!-- 🔵 MODAL SUBIR DOCUMENTO -->
<div class="modal fade" id="modalSubir" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="../documentos/subir.php" method="POST" enctype="multipart/form-data">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Subir Documento</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="pubId" name="pubId">

                <label>Seleccione PDF:</label>
                <input type="file" name="archivo" accept="application/pdf" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success">Subir</button>
            </div>
        </form>
    </div>
</div>

<!-- 🔵 MODAL VISTA PDF -->
<div class="modal fade" id="modalPDF" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Vista Previa del Documento</h5>
                <button class="btn-close bg-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <iframe id="visorPDF"></iframe>
            </div>

        </div>
    </div>
</div>

<!-- 🔵 MODAL COMENTARIOS -->
<div class="modal fade" id="modalComentarios" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Comentarios del Documento</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="contenedorComentarios"></div>

        </div>
    </div>
</div>

<!-- 🔵 MODAL COMPARTIR -->
<div class="modal fade" id="modalCompartir" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="../documentos/compartir.php" method="POST">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Compartir Documento</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="docId" id="docIdCompartir">
                <label>Compartir con:</label>
                <input type="email" name="correoDestino" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success">Compartir</button>
            </div>
        </form>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function abrirSubir(id) {
    document.getElementById("pubId").value = id;
    new bootstrap.Modal(document.getElementById("modalSubir")).show();
}

function verPDF(id) {
    document.getElementById("visorPDF").src = "../documentos/obtener.php?id=" + id;
    new bootstrap.Modal(document.getElementById("modalPDF")).show();
}

function abrirComentarios(id) {
    fetch("../documentos/comentarios.php?id=" + id)
        .then(x => x.text())
        .then(html => {
            document.getElementById("contenedorComentarios").innerHTML = html;
            new bootstrap.Modal(document.getElementById("modalComentarios")).show();
        });
}

function abrirCompartir(id) {
    document.getElementById("docIdCompartir").value = id;
    new bootstrap.Modal(document.getElementById("modalCompartir")).show();
}
</script>

</body>
</html>
