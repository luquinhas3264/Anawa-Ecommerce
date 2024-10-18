<?php
session_start();
include('../config/db.php');

// Verificar si hay una sesión activa y si el usuario es un artesano (idver = 2)
if (!isset($_SESSION['user_id']) || $_SESSION['idver'] != 2) {
    echo "Acceso denegado.";
    exit;
}

// Procesar el formulario de subir producto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomprod = $_POST['nomprod'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $idcat = $_POST['idcat'];
    $idusu = $_SESSION['user_id'];

    // Definir el directorio donde se guardarán las imágenes
    $target_dir = "/Anawa/uploads/product_images/";
    
    // Obtener las rutas de las imágenes y moverlas al directorio
    $imagen1 = $target_dir . basename($_FILES["imagen1"]["name"]);
    $imagen2 = !empty($_FILES["imagen2"]["tmp_name"]) ? $target_dir . basename($_FILES["imagen2"]["name"]) : null;
    $imagen3 = !empty($_FILES["imagen3"]["tmp_name"]) ? $target_dir . basename($_FILES["imagen3"]["name"]) : null;

    // Mover las imágenes a la carpeta de destino en el servidor
    move_uploaded_file($_FILES["imagen1"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $imagen1);
    if ($imagen2) {
        move_uploaded_file($_FILES["imagen2"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $imagen2);
    }
    if ($imagen3) {
        move_uploaded_file($_FILES["imagen3"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $imagen3);
    }

    // Insertar el producto en la base de datos junto con las rutas de las imágenes
    $sql = "INSERT INTO Producto (nomprod, descripción, precio, idusu, idcat, imagen1, imagen2, imagen3) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiisss", $nomprod, $descripcion, $precio, $idusu, $idcat, $imagen1, $imagen2, $imagen3);

    if ($stmt->execute()) {
        header("Location: ../pages/artesano_dashboard.php?status=success");
    } else {
        header("Location: ../pages/artesano_dashboard.php?status=error");
    }

    $stmt->close();
    $conn->close();
}
?>
