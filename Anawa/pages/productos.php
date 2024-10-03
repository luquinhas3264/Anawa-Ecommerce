<?php
session_start();
include('../config/db.php');

// Consultar todos los productos
$sql = "SELECT p.idprod, p.nomprod, p.descripción, p.precio, c.nomCat, p.imagen1 FROM Producto p JOIN Categoria c ON p.idcat = c.idcat";
$result = $conn->query($sql);

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Anawa/assets/css/index_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- Cargar jQuery -->
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="#"><img src="/Anawa/assets/images/LogoAnawa2.png" alt="Logo"></a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="#">Productos</a></li>
                <li><a href="cart.php">Carrito (<?php echo count($_SESSION['cart']); ?>)</a></li> <!-- Enlace al carrito -->
                <li><a href="/Anawa/pages/mis_pedidos.php">Pedidos</a></li>                
            </ul>
            <div>
                <a href="/Anawa/pages/logout.php" class="logout-btn">Cerrar sesión</a>
            </div>           
        </nav>
    </header>

    <section class="products-list">
        <h2>Productos Disponibles</h2>
        <div class="products-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-item'>
                        <img src='{$row['imagen1']}' alt='{$row['nomprod']}'>
                        <h3>{$row['nomprod']}</h3>
                        <p>{$row['descripción']}</p>
                        <p>Categoría: {$row['nomCat']}</p>
                        <p>Precio: {$row['precio']} Bs</p>
                        <form class='add-to-cart-form'>
                            <input type='hidden' name='idprod' value='{$row['idprod']}'>
                            <label for='cantidad'>Cantidad:</label>
                            <input type='number' name='cantidad' min='1' value='1'>
                            <button type='submit' class='add-to-cart-btn'>Añadir al Carrito</button>
                        </form>
                    </div>";
                }
            } else {
                echo "<p>No hay productos disponibles.</p>";
            }
            ?>
        </div>
    </section>

    <script>
    $(document).ready(function() {
        // Manejar el envío del formulario con Ajax
        $('.add-to-cart-form').submit(function(e) {
            e.preventDefault(); // Prevenir la recarga de la página

            var formData = $(this).serialize(); // Obtener los datos del formulario

            $.ajax({
                type: 'POST',
                url: '../scripts/add_to_cart.php', // El archivo PHP que procesará la solicitud
                data: formData,
                success: function(response) {
                    alert("Producto añadido al carrito!");
                    // Aquí podrías actualizar la cantidad de productos en el carrito en el header
                },
                error: function() {
                    alert("Error al añadir el producto al carrito.");
                }
            });
        });
    });
    </script>
</body>
</html>
