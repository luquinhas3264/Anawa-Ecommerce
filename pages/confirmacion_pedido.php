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

// Ruta del QR para transferencia bancaria
$qr_bancario = '/Anawa/assets/images/qr_transferencia.png';

// Verificar si se ha enviado el formulario de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metodoPago = $_POST['metodoPago'];
    
    // Verificar si se seleccionó un método de pago
    if (empty($metodoPago)) {
        echo "<p>Error: No se ha seleccionado un método de pago.</p>";
    } else {
        // Verificar que existe el user_id en sesión
        if (!isset($_SESSION['user_id'])) {
            echo "<p>Error: Usuario no autenticado.</p>";
            exit();
        }

        $idusu = $_SESSION['user_id'];

        // Si el método de pago es "tarjeta", procede con la validación para tarjeta de crédito
        if ($metodoPago === 'tarjeta') {
            // Aquí puedes añadir validaciones adicionales para los campos de la tarjeta si es necesario
            $insert_pago = "INSERT INTO Pago (fechapag, método, idped, estado_deposito) VALUES (NOW(), '$metodoPago', '$id_pedido', 'pendiente')";

            if ($conn->query($insert_pago) === TRUE) {
                // Crear una solicitud de confirmación para el administrador
                $insert_solicitud = "INSERT INTO Solicitudes (idusu, tipo_solicitud, estado) VALUES ('$idusu', 'pago', 'pendiente')";
                if ($conn->query($insert_solicitud) === TRUE) {
                    echo "<p>Tu solicitud de pago ha sido enviada. Espera la confirmación del administrador.</p>";
                } else {
                    echo "<p>Error al enviar la solicitud: " . $conn->error . "</p>";
                }
            } else {
                echo "<p>Error al procesar el pago: " . $conn->error . "</p>";
            }
        }
        
        // Si el método de pago es "transferencia", verifica si se marcó el checkbox
        elseif ($metodoPago === 'transferencia' && isset($_POST['confirmTransferencia'])) {
            // Insertar pago como transferencia
            $insert_pago = "INSERT INTO Pago (fechapag, método, idped, estado_deposito) VALUES (NOW(), '$metodoPago', '$id_pedido', 'pendiente')";
                
            if ($conn->query($insert_pago) === TRUE) {
                // Crear una solicitud de confirmación para el administrador
                $insert_solicitud = "INSERT INTO Solicitudes (idusu, tipo_solicitud, estado) VALUES ('$idusu', 'pago', 'pendiente')";
                if ($conn->query($insert_solicitud) === TRUE) {
                    echo "<p>Tu solicitud de pago ha sido enviada. Espera la confirmación del administrador.</p>";
                } else {
                    echo "<p>Error al enviar la solicitud: " . $conn->error . "</p>";
                }
            } else {
                echo "<p>Error al procesar el pago: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>Error: Debes confirmar que realizaste la transferencia.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <link rel="stylesheet" href="/Anawa/assets/css/confirmacion_styles.css"> <!-- Estilos adicionales -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Para manejar eventos dinámicos -->
</head>
<body>
    <div class="container">
        <h2>Confirmación de Pedido</h2>
        <p>Tu pedido con ID <b><?php echo $id_pedido; ?></b> ha sido realizado con éxito.</p>

        <!-- Mensaje de confirmación que aparecerá después del envío -->
        <p id="successMessage" style="display:none;">Tu solicitud de pago ha sido enviada. Espera la confirmación del administrador.</p>

        <!-- Formulario para el pago -->
        <form action="" method="POST" id="paymentForm">
            <input type="hidden" name="idped" value="<?php echo $id_pedido; ?>">
            
            <label for="metodoPago">Método de pago:</label>
            <select name="metodoPago" id="metodoPago" required>
                <option value="">Selecciona...</option>
                <option value="tarjeta">Tarjeta de Crédito</option>
                <option value="transferencia">Transferencia Bancaria</option>
            </select>

            <!-- Campos para tarjeta de crédito -->
            <div id="tarjetaFields" style="display: none;">
                <h4>Detalles de la Tarjeta de Crédito</h4>
                <label for="numeroTarjeta">Número de tarjeta:</label>
                <input type="text" id="numeroTarjeta" name="numeroTarjeta" maxlength="16" placeholder="XXXX XXXX XXXX XXXX">

                <label for="expiracionTarjeta">Fecha de expiración:</label>
                <input type="month" id="expiracionTarjeta" name="expiracionTarjeta" required>

                <label for="cvvTarjeta">CVV:</label>
                <input type="text" id="cvvTarjeta" name="cvvTarjeta" maxlength="3" placeholder="123" required>
            </div>

            <!-- Campos para transferencia bancaria -->
            <div id="transferenciaFields" style="display: none;">
                <h4>Transferencia Bancaria</h4>
                <p>Escanea el siguiente código QR para realizar la transferencia:</p>
                <img src="<?php echo $qr_bancario; ?>" alt="Código QR para transferencia" style="max-width: 200px;">
                <p>Una vez realizada la transferencia, confirma en esta plataforma.</p>

                <!-- Checkbox para confirmar la transferencia -->
                <div>
                    <label for="confirmTransferencia">
                        <input type="checkbox" id="confirmTransferencia" name="confirmTransferencia"> Ya he realizado la transferencia
                    </label>
                </div>
            </div>

            <button type="submit" id="submitPago" disabled>Pagar</button>
        </form>

        <script>
            // Mostrar u ocultar campos según el método de pago seleccionado
            $('#metodoPago').change(function() {
                var metodo = $(this).val();
                if (metodo === 'tarjeta') {
                    $('#tarjetaFields').show();
                    $('#transferenciaFields').hide();
                    $('#submitPago').prop('disabled', false); // Habilitar botón para tarjeta de crédito
                } else if (metodo === 'transferencia') {
                    $('#tarjetaFields').hide();
                    $('#transferenciaFields').show();
                    $('#submitPago').prop('disabled', true); // Deshabilitar botón hasta que se marque el checkbox
                } else {
                    $('#tarjetaFields').hide();
                    $('#transferenciaFields').hide();
                    $('#submitPago').prop('disabled', true); // Deshabilitar por defecto
                }
            });

            // Habilitar el botón de pago cuando el checkbox de transferencia esté marcado
            $('#confirmTransferencia').change(function() {
                if ($(this).is(':checked')) {
                    $('#submitPago').prop('disabled', false);
                } else {
                    $('#submitPago').prop('disabled', true);
                }
            });

            // Ocultar el formulario y mostrar el mensaje de éxito después del envío
            $('#paymentForm').submit(function (event) {
                event.preventDefault(); // Evitar el comportamiento por defecto del formulario

                $.ajax({
                    type: 'POST',
                    url: '', // Envía el formulario al servidor actual
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#paymentForm').hide(); // Ocultar el formulario
                        $('#successMessage').show(); // Mostrar el mensaje de éxito
                    },
                    error: function () {
                        alert('Hubo un error al enviar la solicitud. Por favor, inténtalo de nuevo.');
                    }
                });
            });
        </script>

        <a href="productos.php">Volver a productos</a>
    </div>
</body>
</html>
