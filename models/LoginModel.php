<?php
require_once __DIR__ . "/../../app/config/Database.php";

class LoginModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function verificarCredenciales($usuario, $password, $rol) {
        try {
            // Convertir el rol del formulario al formato de la BD
            $rolBD = $this->convertirRolABD($rol);
            
            $query = "SELECT id, usuario, contraseña, rol 
                      FROM usuarios 
                      WHERE usuario = :usuario 
                      AND rol = :rol";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->bindParam(":rol", $rolBD);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                // Verificar contraseña (texto plano por ahora)
                if ($password === $resultado['contraseña']) {
                    // Quitar la contraseña del resultado por seguridad
                    unset($resultado['contraseña']);
                    return $resultado;
                }
            }

            return false;

        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    private function convertirRolABD($rolFormulario) {
        // Convertir el rol del formulario al formato de la BD
        switch ($rolFormulario) {
            case 'Alumno':
                return 'alumno';
            case 'Profesores':
                return 'profesor';
            case 'Administrativo':
                return 'administrativo';
            default:
                return $rolFormulario;
        }
    }

    // Método para obtener datos específicos del usuario según su rol
    public function obtenerDatosUsuario($usuarioId, $rol) {
        try {
            switch ($rol) {
                case 'alumno':
                    $query = "SELECT a.*, g.nombre as nombre_grupo 
                              FROM alumnos a 
                              LEFT JOIN grupos g ON a.grupo_id = g.id 
                              WHERE a.id = :id";
                    break;
                case 'profesor':
                    $query = "SELECT * FROM profesores WHERE id = :id";
                    break;
                case 'administrativo':
                    $query = "SELECT * FROM administrativos WHERE id = :id";
                    break;
                default:
                    return null;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $usuarioId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo datos usuario: " . $e->getMessage());
            return null;
        }
    }
}
?>