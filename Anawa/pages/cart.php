<?php
session_start();

// Inicializar variables
$total_price = 0;

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Tu carrito está vacío. <a href='productos.php'>Volver a productos</a></p>";
    exit();
}

// Obtener la ubicación seleccionada, si existe
$lat = isset($_SESSION['lat']) ? $_SESSION['lat'] : null;
$lng = isset($_SESSION['lng']) ? $_SESSION['lng'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="/Anawa/assets/css/cart_styles.css"> <!-- Enlazar el archivo CSS -->
</head>
<body>
    <div class="cart-container">
        <h2>Tu Carrito</h2>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $product): ?>
                    <tr>
                        <td class="cart-product">
                            <img src="<?php echo $product['imagen']; ?>" alt="<?php echo $product['nomprod']; ?>" class="product-image">
                            <div class="product-info">
                                <h3><?php echo $product['nomprod']; ?></h3>
                            </div>
                        </td>
                        <td><?php echo $product['precio']; ?> Bs</td>
                        <td>
                            <form action="../scripts/update_cart.php" method="POST">
                                <input type="number" name="cantidad" value="<?php echo $product['cantidad']; ?>" min="1" class="input-cantidad">
                                <input type="hidden" name="idprod" value="<?php echo $product['idprod']; ?>">
                                <button type="submit" class="btn-update">Actualizar</button>
                            </form>
                        </td>
                        <td><?php echo $product['precio'] * $product['cantidad']; ?> Bs</td>
                        <td>
                            <form action="../scripts/remove_from_cart.php" method="POST">
                                <input type="hidden" name="idprod" value="<?php echo $product['idprod']; ?>">
                                <button type="submit" class="btn-remove">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total_price += $product['precio'] * $product['cantidad']; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Geolocalización para obtener la ubicación del comprador -->
        <div class="cart-location">
            <h3>Ubicación de Entrega</h3>
            <p>
                <button id="get-location" class="btn-location">Obtener Mi Ubicación</button>
            </p>

            <!-- Mostrar ubicación seleccionada, si existe -->
            <p id="location-info">
                <?php if ($lat && $lng): ?>
                    Ubicación seleccionada: Latitud <?php echo $lat; ?>, Longitud <?php echo $lng; ?>
                <?php else: ?>
                    No has seleccionado una ubicación aún.
                <?php endif; ?>
            </p>
        </div>

        <div class="cart-summary">
            <h3>Total a Pagar: <span><?php echo $total_price; ?> Bs</span></h3>
            <form action="../scripts/checkout.php" method="POST">
                <input type="hidden" id="lat" name="lat" value="<?php echo $lat; ?>">
                <input type="hidden" id="lng" name="lng" value="<?php echo $lng; ?>">
                <button type="submit" class="btn-checkout">Realizar Pedido</button>
            </form>
        </div>
    </div>

    <!-- Script para obtener la ubicación del comprador -->
    <script>
        document.getElementById('get-location').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Mostrar la ubicación seleccionada en el HTML
                    document.getElementById('location-info').innerHTML = `Ubicación seleccionada: Latitud ${lat}, Longitud ${lng}`;

                    // Guardar la latitud y longitud en los campos ocultos para enviarlos en el formulario
                    document.getElementById('lat').value = lat;
                    document.getElementById('lng').value = lng;
                }, function(error) {
                    alert('Error al obtener la ubicación: ' + error.message);
                }, {
                    enableHighAccuracy: true, // Aumentar la precisión
                    timeout: 10000, // Tiempo máximo para obtener la ubicación
                    maximumAge: 0 // No usar una ubicación en caché
                });
            } else {
                alert('La geolocalización no está disponible en este navegador.');
            }
        });

    </script>
</body>
</html>
