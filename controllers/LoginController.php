<?php

require_once __DIR__ . "/../models/LoginModel.php";

class LoginController {
    
    public function handleLogin() {
        session_start();
        
        
        // 1. Recibir datos
        $usuario = $_POST['usuario'] ?? '';
        $contraseña = $_POST['contraseña'] ?? '';
        $rol = $_POST['rol'] ?? '';

        
        // 2. Validar que no estén vacíos
        if (empty($usuario) || empty($contraseña) || empty($rol)) {
            $_SESSION['error'] = "Todos los campos son obligatorios";
            header("Location: /gestion_escolar/");
            exit;
        }

        // 3. Usar el modelo para verificar credenciales en BD
        $loginModel = new LoginModel();
        
        $usuarioData = $loginModel->verificarCredenciales($usuario, $contraseña, $rol);
        
        var_dump($usuarioData);
        echo "<br>";

        if ($usuarioData) {
            
            // 4. Iniciar sesión con datos de BD
            $_SESSION["usuario_id"] = $usuarioData['id'];
            $_SESSION["usuario"] = $usuarioData['usuario'];
            $_SESSION["rol"] = $usuarioData['rol'];

            // 5. Obtener datos específicos del usuario
            $datosUsuario = $loginModel->obtenerDatosUsuario($usuarioData['id'], $usuarioData['rol']);
            if ($datosUsuario) {
                $_SESSION["datos_usuario"] = $datosUsuario;
            }

            // 6. Redirigir según el rol
            $this->redirigirSegunRol($usuarioData['rol']);
        } else {
            $_SESSION['error'] = "Usuario o contraseña incorrectos";
            header("Location: /gestion_escolar/");
            exit;
        }
    }

    private function redirigirSegunRol($rol) {
        switch ($rol) {
            case 'alumno':
                header("Location: /gestion_escolar/index.php?p=alumnos");
                break;
            case 'profesor':
                header("Location: /gestion_escolar/index.php?p=profesores");
                break;
            case 'administrativo':
                header("Location: /gestion_escolar/index.php?p=administrativo");
                break;
            default:
                header("Location: /gestion_escolar/");
                break;
        }
        exit;
    }
}

// Ejecutar el controlador si se accede directamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'])) {
    $loginController = new LoginController();
    $loginController->handleLogin();
}
?>