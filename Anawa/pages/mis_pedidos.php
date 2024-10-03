<?php
session_start();
include('../config/db.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$idusu = $_SESSION['user_id'];

// Consultar los pedidos del usuario actual
$query_pedidos = "
    SELECT P.idped, DP.cantidad, P.idusu, E.estado, E.fechaent, U.nomusu as delivery_name, U.celular as delivery_phone
    FROM Pedido P
    JOIN DetallePedido DP ON P.idped = DP.idped
    LEFT JOIN Entrega E ON P.idped = E.idped
    LEFT JOIN Usuario U ON E.idusu = U.idusu
    WHERE P.idusu = '$idusu'";

$pedidos = $conn->query($query_pedidos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <link rel="stylesheet" href="/Anawa/assets/css/mis_pedidos_styles.css">
</head>
<body>
    <div class="pedidos-container">
        <h1>Mis Pedidos</h1>

        <section>
            <h2>Pedidos Realizados</h2>
            <?php if ($pedidos->num_rows > 0): ?>
                <table class="pedidos-table">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Fecha de Entrega</th>
                            <th>Delivery</th>
                            <th>Teléfono Delivery</th>
                            <th>Contactar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $pedido['idped']; ?></td>
                            <td><?php echo $pedido['cantidad']; ?></td>
                            <td><?php echo $pedido['estado'] ? $pedido['estado'] : 'En espera'; ?></td>
                            <td><?php echo $pedido['fechaent'] ? $pedido['fechaent'] : 'Pendiente'; ?></td>
                            <td><?php echo $pedido['delivery_name'] ? $pedido['delivery_name'] : 'No asignado'; ?></td>
                            <td><?php echo $pedido['delivery_phone'] ? $pedido['delivery_phone'] : 'N/A'; ?></td>
                            <td>
                                <?php if ($pedido['delivery_phone']): ?>
                                    <a href="https://wa.me/<?php echo $pedido['delivery_phone']; ?>" target="_blank">
                                        <button class="btn-whatsapp">Contactar por WhatsApp</button>
                                    </a>
                                <?php else: ?>
                                    <span>No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No has realizado ningún pedido aún.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
