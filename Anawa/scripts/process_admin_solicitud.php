<?php
session_start();
include('../config/db.php');

// Verificar si es un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['idver'] != 1) {
    echo "Acceso denegado.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idsolicitud = $_POST['idsolicitud'];
    $idusu = $_POST['idusu'];
    $tipo_solicitud = $_POST['tipo_solicitud'];
    $accion = $_POST['accion'];

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        if ($accion == 'aprobar') {
            // Actualizar el estado de la solicitud
            $sql_update_solicitud = "UPDATE Solicitudes SET estado = 'aprobado' WHERE idsolicitud = ?";
            $stmt_solicitud = $conn->prepare($sql_update_solicitud);
            $stmt_solicitud->bind_param("i", $idsolicitud);
            $stmt_solicitud->execute();

            // Actualizar el idver en la tabla Usuario
            if ($tipo_solicitud == 'artesano') {
                $idver = 2; // idver para Artesano
            } else {
                $idver = 4; // idver para Delivery
            }

            $sql_update_usuario = "UPDATE Usuario SET idver = ? WHERE idusu = ?";
            $stmt_usuario = $conn->prepare($sql_update_usuario);
            $stmt_usuario->bind_param("ii", $idver, $idusu);
            $stmt_usuario->execute();

            $conn->commit();
            echo "Solicitud aprobada.";
        } else if ($accion == 'rechazar') {
            // Actualizar el estado de la solicitud
            $sql_update_solicitud = "UPDATE Solicitudes SET estado = 'rechazado' WHERE idsolicitud = ?";
            $stmt_solicitud = $conn->prepare($sql_update_solicitud);
            $stmt_solicitud->bind_param("i", $idsolicitud);
            $stmt_solicitud->execute();

            $conn->commit();
            echo "Solicitud rechazada.";
        }

        header("Location: ../pages/admin_dashboard.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    $stmt_solicitud->close();
    $stmt_usuario->close();
}

$conn->close();
?>
