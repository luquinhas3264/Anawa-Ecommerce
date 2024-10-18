<?php
session_start();
include('../config/db.php');

$idprod = $_POST['idprod'];
$cantidad = $_POST['cantidad'];

// Consultar los detalles del producto
$sql = "SELECT idprod, nomprod, precio, imagen1 FROM Producto WHERE idprod = '$idprod'";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
    // Si no se encuentra el producto, devolver un error
    echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
    exit();
}

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

// Calcular el total de productos en el carrito
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_count += $item['cantidad'];
}

// Devolver la respuesta JSON con el estado y el número de productos en el carrito
echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
exit();
?>
