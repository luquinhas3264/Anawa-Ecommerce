<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "Por favor, rellena todos los campos.";
        exit;
    }

    // Buscar el usuario por su correo electrónico
    $sql = "SELECT * FROM Usuario WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar si la cuenta está verificada
        if ($user['verificado'] == 0) {
            echo "Tu cuenta no ha sido verificada. Por favor, revisa tu correo para verificar tu cuenta.";
            exit;
        }

        // Verificar la contraseña
        if (password_verify($password, $user['contraseña'])) {
            $_SESSION['user_id'] = $user['idusu'];
            $_SESSION['username'] = $user['nomusu'];
            header("Location: ../pages/home.php");
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "El usuario no existe.";
    }

    $stmt->close();
}

$conn->close();
?>
