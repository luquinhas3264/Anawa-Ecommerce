<?php
session_start();
include('../config/db.php');

// Verificar si el comprador ha hecho un pedido
if (!isset($_GET['idped'])) {
    echo "Error: No se ha recibido el ID del pedido.";
    exit();
}

$id_pedido = $_GET['idped']; // Obtener el ID del pedido desde la URL

// Consultar detalles del pedido
$query_pedido = "SELECT * FROM Pedido WHERE idped = '$id_pedido'";
$result_pedido = $conn->query($query_pedido);

if ($result_pedido->num_rows > 0) {
    $pedido = $result_pedido->fetch_assoc();
} else {
    echo "Error: No se encontró el pedido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
</head>
<body>
    <h2>Pedido Realizado</h2>
    <p>Tu pedido con ID <b><?php echo $id_pedido; ?></b> ha sido realizado con éxito. Uno de nuestros deliveries lo tomará pronto.</p>
    <a href="productos.php">Volver a productos</a>
</body>
</html>
