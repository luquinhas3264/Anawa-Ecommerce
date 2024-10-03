<?php
session_start();
include('../config/db.php');

$idprod = $_POST['idprod'];
$cantidad = $_POST['cantidad'];

// Consultar los detalles del producto
$sql = "SELECT idprod, nomprod, precio, imagen1 FROM Producto WHERE idprod = '$idprod'";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

// Si el producto ya está en el carrito, actualizar la cantidad
if (isset($_SESSION['cart'][$idprod])) {
    $_SESSION['cart'][$idprod]['cantidad'] += $cantidad;
} else {
    // Agregar un nuevo producto al carrito
    $_SESSION['cart'][$idprod] = [
        'idprod' => $product['idprod'],
        'nomprod' => $product['nomprod'],
        'precio' => $product['precio'],
        'cantidad' => $cantidad,
        'imagen' => $product['imagen1']
    ];
}

// No redirigir a ninguna página, solo devolver una respuesta.
echo "Producto añadido correctamente";
?>
