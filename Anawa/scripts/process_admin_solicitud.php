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
    $idcom = $_POST['idcom']; // Comunidad para artesano
    $turno = $_POST['turno']; // Turno para delivery

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        if ($accion == 'aprobar') {
            // Actualizar el estado de la solicitud en la tabla Solicitudes
            $sql_update_solicitud = "UPDATE Solicitudes SET estado = 'aprobado' WHERE idsolicitud = ?";
            $stmt_solicitud = $conn->prepare($sql_update_solicitud);
            $stmt_solicitud->bind_param("i", $idsolicitud);
            $stmt_solicitud->execute();

            // Actualizar el idver y estado_solicitud en la tabla Usuario
            if ($tipo_solicitud == 'artesano') {
                $idver = 2; // idver para Artesano
            } else {
                $idver = 4; // idver para Delivery
            }

            $sql_update_usuario = "UPDATE Usuario SET idver = ?, estado_solicitud = 'aprobado' WHERE idusu = ?";
            $stmt_usuario = $conn->prepare($sql_update_usuario);
            $stmt_usuario->bind_param("ii", $idver, $idusu);
            $stmt_usuario->execute();

            // Si la solicitud es de Artesano, insertar en la tabla Artesano
            if ($tipo_solicitud == 'artesano') {
                $sql_insert_artesano = "INSERT INTO Artesano (idusu, idcom) VALUES (?, ?)";
                $stmt_artesano = $conn->prepare($sql_insert_artesano);
                $stmt_artesano->bind_param("ii", $idusu, $idcom);
                $stmt_artesano->execute();
                $stmt_artesano->close();
            }

            // Si la solicitud es de Delivery, insertar en la tabla Delivery
            if ($tipo_solicitud == 'delivery') {
                $sql_insert_delivery = "INSERT INTO Delivery (idusu, turno) VALUES (?, ?)";
                $stmt_delivery = $conn->prepare($sql_insert_delivery);
                $stmt_delivery->bind_param("is", $idusu, $turno);
                $stmt_delivery->execute();
                $stmt_delivery->close();
            }

            $conn->commit();
            echo "Solicitud aprobada.";
        } else if ($accion == 'rechazar') {
            // Actualizar el estado de la solicitud a 'rechazado'
            $sql_update_solicitud = "UPDATE Solicitudes SET estado = 'rechazado' WHERE idsolicitud = ?";
            $stmt_solicitud = $conn->prepare($sql_update_solicitud);
            $stmt_solicitud->bind_param("i", $idsolicitud);
            $stmt_solicitud->execute();

            // Actualizar el estado_solicitud en la tabla Usuario a 'rechazado'
            $sql_update_usuario = "UPDATE Usuario SET estado_solicitud = 'rechazado' WHERE idusu = ?";
            $stmt_usuario = $conn->prepare($sql_update_usuario);
            $stmt_usuario->bind_param("i", $idusu);
            $stmt_usuario->execute();

            $conn->commit();
            echo "Solicitud rechazada.";
        }

        // Redirigir después de la aprobación/rechazo
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
