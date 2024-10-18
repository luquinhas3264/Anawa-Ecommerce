<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['idver'] != 1) {
    echo "Acceso denegado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idpago = $_POST['idpago'];
    $accion = $_POST['accion'];

    if ($accion === 'aprobar') {
        // Aprobar el pago
        $sql_update_pago = "UPDATE Pago SET estado_deposito = 'confirmado' WHERE idpago = '$idpago'";
        if ($conn->query($sql_update_pago) === TRUE) {
            // Asignar un delivery automáticamente
            $sql_pedido = "SELECT idped FROM Pago WHERE idpago = '$idpago'";
            $result_pedido = $conn->query($sql_pedido);
            $pedido = $result_pedido->fetch_assoc();

            // Consultar un delivery disponible
            $sql_delivery = "SELECT idusu FROM Usuario WHERE idver = 4 LIMIT 1"; // Tomar el primer delivery disponible
            $result_delivery = $conn->query($sql_delivery);
            $delivery = $result_delivery->fetch_assoc();

            // Asignar el delivery
            $idped = $pedido['idped'];
            $iddelivery = $delivery['idusu'];
            $sql_asignar_entrega = "INSERT INTO Entrega (idped, idusu, estado, fechaent) VALUES ('$idped', '$iddelivery', 'En camino', CURDATE())";
            $conn->query($sql_asignar_entrega);

            echo "Pago aprobado y entrega asignada.";
        } else {
            echo "Error al aprobar el pago: " . $conn->error;
        }
    } elseif ($accion === 'rechazar') {
        // Rechazar el pago
        $sql_update_pago = "UPDATE Pago SET estado_deposito = 'rechazado' WHERE idpago = '$idpago'";
        $conn->query($sql_update_pago);
        echo "Pago rechazado.";
    }
}

$conn->close();
?>
