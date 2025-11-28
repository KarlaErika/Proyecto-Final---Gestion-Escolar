<?php
require_once __DIR__ . "/../../app/config/Database.php";

class AdministrativoModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function obtenerDatosAdministrativo($adminId) {
        try {
            $query = "SELECT * FROM administrativos WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $adminId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo datos administrativo: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerGruposAsignados($adminId) {
        try {
            $query = "SELECT g.* 
                      FROM administrativo_grupo ag
                      JOIN grupos g ON ag.id_grupo = g.id
                      WHERE ag.id_admin = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $adminId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo grupos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCarrerasAsignadas($adminId) {
        try {
            $query = "SELECT carrera 
                      FROM administrativo_carrera 
                      WHERE id_admin = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $adminId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo carreras: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerTodosLosGrupos() {
        try {
            $query = "SELECT * FROM grupos ORDER BY nombre";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo todos los grupos: " . $e->getMessage());
            return [];
        }
    }

    // Obtener otros administrativos
    public function obtenerOtrosAdministrativos($adminId) {
        try {
            $query = "SELECT id, nombre_completo, num_empleado, horario_atencion, ubicacion 
                      FROM administrativos 
                      WHERE id != :id
                      ORDER BY nombre_completo";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $adminId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo otros administrativos: " . $e->getMessage());
            return [];
        }
    }

    // Obtener todos los profesores
    public function obtenerTodosLosProfesores() {
        try {
            $query = "SELECT id, nombre_completo, matricula, carrera_enfocada 
                      FROM profesores 
                      ORDER BY nombre_completo";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo profesores: " . $e->getMessage());
            return [];
        }
    }

    // Obtener alumnos por grupo
    public function obtenerAlumnosPorGrupo($grupoId = null) {
        try {
            $query = "SELECT a.id, a.nombre_completo, a.matricula, a.carrera, g.nombre as grupo
                      FROM alumnos a
                      JOIN grupos g ON a.grupo_id = g.id";
            
            if ($grupoId) {
                $query .= " WHERE g.id = :grupo_id";
            }
            
            $query .= " ORDER BY g.nombre, a.nombre_completo";
            
            $stmt = $this->conn->prepare($query);
            if ($grupoId) {
                $stmt->bindParam(":grupo_id", $grupoId);
            }
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo alumnos: " . $e->getMessage());
            return [];
        }
    }
}
?>