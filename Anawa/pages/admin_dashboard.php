<?php
session_start();
include('../config/db.php');

// Verificar si es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['idver'] != 1) {
    echo "Acceso denegado.";
    exit;
}

// Obtener todas las solicitudes pendientes con más información del usuario
$sql = "SELECT Solicitudes.*, Usuario.nomusu, Usuario.ci, Usuario.celular, Comunidad.nomcom 
        FROM Solicitudes
        LEFT JOIN Usuario ON Solicitudes.idusu = Usuario.idusu
        LEFT JOIN Comunidad ON Solicitudes.idcom = Comunidad.idcom
        WHERE Solicitudes.estado = 'pendiente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Anawa/assets/css/admin_dashboard.css">
</head>
<body>
    <section class="admin-container">
        <h2>Solicitudes Pendientes</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Carnet</th>
                        <th>Celular</th>
                        <th>Tipo de Solicitud</th>
                        <th>Comunidad/Turno</th>
                        <th>Fecha de Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nomusu']); ?></td>
                            <td><?php echo htmlspecialchars($row['ci']); ?></td>
                            <td><?php echo htmlspecialchars($row['celular']); ?></td>
                            <td><?php echo htmlspecialchars($row['tipo_solicitud']); ?></td>
                            <td>
                                <?php
                                if ($row['tipo_solicitud'] == 'artesano') {
                                    echo htmlspecialchars($row['nomcom']);
                                } else {
                                    echo htmlspecialchars($row['turno']);
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['fecha_solicitud']); ?></td>
                            <td>
                            <form action="../scripts/process_admin_solicitud.php" method="POST">
                                <input type="hidden" name="idsolicitud" value="<?php echo $row['idsolicitud']; ?>">
                                <input type="hidden" name="idusu" value="<?php echo $row['idusu']; ?>">
                                <input type="hidden" name="tipo_solicitud" value="<?php echo $row['tipo_solicitud']; ?>">
                                <input type="hidden" name="idcom" value="<?php echo $row['idcom']; ?>">
                                <input type="hidden" name="turno" value="<?php echo $row['turno']; ?>">
                                <input type="submit" name="accion" value="aprobar">
                                <input type="submit" name="accion" value="rechazar">
                            </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay solicitudes pendientes.</p>
        <?php endif; ?>

    </section>
</body>
</html>

<?php
$conn->close();
?>
