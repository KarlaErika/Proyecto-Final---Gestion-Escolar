<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Gesture - Login</title>
    <link rel="stylesheet" href="/gestion_escolar/public/css/styles.css">

    <!-- Íconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="background"></div>

    <div class="login-container">
        <div class="top-bar"></div>

        <div class="form-content">

            <div class="logo-box">
                <img src="/gestion_escolar/public/imagenes/Logo.png" alt="Logo del sistema" class="logo-img">
            </div>

            <h1>School Gesture</h1>
            <?php if (!empty($error)): ?>
                <div style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

        <!-- 🔥 FORMULARIO REAL -->
        <form action="/gestion_escolar/app/controllers/LoginController.php" method="POST">

            <!-- SELECTOR DE ROL -->
            <div class="role-selector">

                <button type="button" class="role active" data-role="Alumno">
                    <i class="fa-solid fa-user"></i>
                    Alumno
                </button>

                <button type="button" class="role" data-role="Profesores">
                    <i class="fa-solid fa-graduation-cap"></i>
                    Profesores
                </button>

                <button type="button" class="role" data-role="Administrativo">
                    <i class="fa-solid fa-briefcase"></i>
                    Administrativo
                </button>

            </div>

            <!-- 🔥 INPUT OCULTO QUE ENVÍA EL ROL -->
            <input type="hidden" name="rol" id="rol" value="Alumno">

            <label>Usuario</label>
            <input type="text" name="usuario" id="usuario" placeholder="Ingresa tu usuario">

            <label>Contraseña</label>
            <input type="password" name="contraseña" id="contraseña" placeholder="Ingresa tu contraseña">

            <button class="btn-login" type="submit">Iniciar Sesión</button>

        </form>

        </div>
    </div>

    <script src="/gestion_escolar/public/java/script.js"></script>

</body>
</html>
