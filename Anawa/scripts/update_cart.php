<?php
session_start();

$idprod = $_POST['idprod'];
$cantidad = $_POST['cantidad'];

// Actualizar la cantidad del producto en el carrito
if (isset($_SESSION['cart'][$idprod])) {
    $_SESSION['cart'][$idprod]['cantidad'] = $cantidad;
}

header("Location: ../pages/cart.php");
?>
