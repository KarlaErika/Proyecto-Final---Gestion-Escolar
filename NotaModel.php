<?php
require_once __DIR__ . "/../../app/config/Database.php";

class NotaModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // Obtener todas las notas de un usuario
    public function obtenerNotas($usuarioId) {
        try {
            $query = "SELECT id, contenido, completado, fecha_creacion 
                      FROM notas 
                      WHERE id_usuario = :usuario_id 
                      ORDER BY fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario_id", $usuarioId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo notas: " . $e->getMessage());
            return [];
        }
    }

    // Agregar nueva nota
    public function agregarNota($usuarioId, $contenido) {
        try {
            $query = "INSERT INTO notas (id_usuario, contenido) 
                      VALUES (:usuario_id, :contenido)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario_id", $usuarioId);
            $stmt->bindParam(":contenido", $contenido);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error agregando nota: " . $e->getMessage());
            return false;
        }
    }

    // Marcar nota como completada/incompleta
    public function toggleCompletada($notaId, $usuarioId) {
        try {
            $query = "UPDATE notas 
                      SET completado = NOT completado 
                      WHERE id = :nota_id AND id_usuario = :usuario_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nota_id", $notaId);
            $stmt->bindParam(":usuario_id", $usuarioId);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error actualizando nota: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar nota
    public function eliminarNota($notaId, $usuarioId) {
        try {
            $query = "DELETE FROM notas 
                      WHERE id = :nota_id AND id_usuario = :usuario_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nota_id", $notaId);
            $stmt->bindParam(":usuario_id", $usuarioId);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error eliminando nota: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar contenido de nota
    public function actualizarNota($notaId, $usuarioId, $contenido) {
        try {
            $query = "UPDATE notas 
                      SET contenido = :contenido 
                      WHERE id = :nota_id AND id_usuario = :usuario_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nota_id", $notaId);
            $stmt->bindParam(":usuario_id", $usuarioId);
            $stmt->bindParam(":contenido", $contenido);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error actualizando nota: " . $e->getMessage());
            return false;
        }
    }
}
?>