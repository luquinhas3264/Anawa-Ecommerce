<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar sesión</h2>
        <form action="../scripts/process_login.php" method="POST">
            <div class="input-icon">
                <label for="email">Correo electrónico:</label>
                <i class="icon-normal fas fa-envelope"></i>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-icon">
                <label for="password">Contraseña:</label>
                <i class="icon-normal fas fa-lock"></i>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Iniciar sesión</button>
            

        </form>
        <p>¿No tienes una cuenta? <a href="signup.php">Regístrate aquí</a>.</p>
        <p>¿Olvidaste tu contraseña? <a href="forgot_password.php">Recuperala</a>.</p>

        
        
    </div>
</body>
</html>

            
