<?php
session_start();
include '../config/db.php';

// Verificar si el usuario es delivery y está logueado
if (!isset($_SESSION['user_id']) || $_SESSION['idver'] != 4) {
    echo "Acceso denegado.";
    exit;
}

$idusu = $_SESSION['user_id'];
$idped = $_POST['idped'];

// Asignar el pedido al delivery
$sql = "INSERT INTO Entrega (idped, idusu, estado, fechaent) VALUES ('$idped', '$idusu', 'En camino', CURDATE())";

if ($conn->query($sql) === TRUE) {
    echo "Pedido asignado correctamente.";
    header("Location: ../pages/delivery_dashboard.php"); // Redirigir al panel de delivery
} else {
    echo "Error al asignar el pedido: " . $conn->error;
}

$conn->close();
?>
