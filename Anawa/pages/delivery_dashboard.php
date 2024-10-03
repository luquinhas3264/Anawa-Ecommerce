<?php
include '../config/db.php';
session_start();

// Verificar si el usuario está logueado y si es delivery
if (!isset($_SESSION['user_id']) || $_SESSION['idver'] != 4) {
    echo "Acceso denegado.";
    exit;
}

// Obtener el idusu de la sesión
$idusu = $_SESSION['user_id'];

// Obtener los pedidos disponibles para ser entregados
$query_pedidos_disponibles = "
    SELECT P.idped, Pr.nomprod, DP.cantidad, P.idusu as cliente, P.latitud, P.longitud
    FROM Pedido P
    JOIN DetallePedido DP ON P.idped = DP.idped
    JOIN Producto Pr ON DP.idprod = Pr.idprod
    LEFT JOIN Entrega E ON P.idped = E.idped
    WHERE E.idped IS NULL";
$pedidos_disponibles = $conn->query($query_pedidos_disponibles);

// Obtener los pedidos asignados al delivery actual
$query_pedidos_asignados = "
    SELECT E.idped, E.estado, E.fechaent, P.latitud, P.longitud
    FROM Entrega E
    INNER JOIN Pedido P ON E.idped = P.idped
    WHERE E.idusu = '$idusu'";
$pedidos_asignados = $conn->query($query_pedidos_asignados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Delivery</title>
    <link rel="stylesheet" href="/Anawa/assets/css/delivery_dashboard_styles.css">
</head>
<body>
    <div class="delivery-container">
        <h1>Bienvenido al Panel de Delivery</h1>

        <!-- Mostrar pedidos donde los compradores solicitan delivery -->
        <section>
            <h2>Pedidos Disponibles</h2>
            <?php if ($pedidos_disponibles->num_rows > 0): ?>
                <table class="delivery-table">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Cliente</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pedido = $pedidos_disponibles->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $pedido['idped']; ?></td>
                            <td><?php echo $pedido['nomprod']; ?></td>
                            <td><?php echo $pedido['cantidad']; ?></td>
                            <td><?php echo $pedido['cliente']; ?></td>
                            <td>
                                <?php if ($pedido['latitud'] && $pedido['longitud']): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo $pedido['latitud']; ?>,<?php echo $pedido['longitud']; ?>" target="_blank">
                                        Ver Ubicación en Google Maps
                                    </a>
                                <?php else: ?>
                                    <span>No disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="../scripts/asignar_pedido.php" method="POST">
                                    <input type="hidden" name="idped" value="<?php echo $pedido['idped']; ?>">
                                    <button type="submit" class="btn-aceptar">Aceptar Pedido</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay pedidos disponibles en este momento.</p>
            <?php endif; ?>
        </section>

        <!-- Mostrar pedidos asignados -->
        <section>
            <h2>Mis Pedidos Asignados</h2>
            <?php if ($pedidos_asignados->num_rows > 0): ?>
                <table class="delivery-table">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Estado</th>
                            <th>Fecha de Entrega</th>
                            <th>Ubicación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pedido = $pedidos_asignados->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $pedido['idped']; ?></td>
                            <td><?php echo $pedido['estado']; ?></td>
                            <td><?php echo $pedido['fechaent']; ?></td>
                            <td>
                                <?php if ($pedido['latitud'] && $pedido['longitud']): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo $pedido['latitud']; ?>,<?php echo $pedido['longitud']; ?>" target="_blank">
                                        Ver Ubicación en Google Maps
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
                <p>No tienes pedidos asignados.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
