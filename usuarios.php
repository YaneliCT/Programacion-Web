<?php 
include("conexion.php"); // conexión centralizada

// Consulta con INNER JOIN para traer dirección y rol
$sql = "SELECT 
            u.Id_usuario, 
            u.Nombre, 
            u.Apellido_P, 
            u.Apellido_M, 
            u.Correo, 
            d.Nombre AS direccion, 
            r.Nombre AS rol 
        FROM usuario u 
        INNER JOIN direccion d ON u.Id_direccion = d.Id_direccion 
        INNER JOIN rol r ON u.Id_rol = r.Id_rol";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel de administración</title>
    <link rel="stylesheet" href="usuarios.css" />
</head>
<body>
    <div class="contenedor">
        <h1>Panel de administración de usuarios</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado->fetch_assoc()): 
                    $nombreCompleto = $row['Nombre'] . ' ' . $row['Apellido_P'] . ' ' . $row['Apellido_M'];
                ?>
                <tr>
                    <td><?= $row['Id_usuario'] ?></td>
                    <td><?= $nombreCompleto ?></td>
                    <td><?= $row['Correo'] ?></td>
                    <td><?= $row['direccion'] ?></td>
                    <td><?= $row['rol'] ?></td>
                    <td>
                        <a href="modificar.php?id=<?= $row['Id_usuario'] ?>">
                            <button>Modificar</button>
                        </a>
                        <a href="eliminar.php?id=<?= $row['Id_usuario'] ?>" 
                           onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                            <button class="eliminar">Eliminar</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="login.php" class="volver">Cerrar sesión</a>
    </div>
</body>
</html>
