<?php
require_once __DIR__ . "/../../app/config/Database.php";

class AlumnoModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // Obtener datos básicos del alumno
    public function obtenerDatosAlumno($alumnoId) {
        try {
            $query = "SELECT a.*, g.nombre as nombre_grupo, g.carrera, g.turno, g.aula, g.grado,
                             adm.nombre_completo as encargado_nombre
                      FROM alumnos a 
                      LEFT JOIN grupos g ON a.grupo_id = g.id
                      LEFT JOIN administrativos adm ON a.encargado_id = adm.id
                      WHERE a.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $alumnoId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo datos alumno: " . $e->getMessage());
            return null;
        }
    }

    // Obtener horario del alumno
    public function obtenerHorario($alumnoId) {
        try {
            $query = "SELECT gm.id_grupo, m.nombre as materia, 
                             p.nombre_completo as profesor,
                             g.turno, g.aula
                      FROM grupo_materia gm
                      JOIN materias m ON gm.id_materia = m.id
                      JOIN profesores p ON gm.id_profesor = p.id
                      JOIN grupos g ON gm.id_grupo = g.id
                      JOIN alumnos a ON a.grupo_id = g.id
                      WHERE a.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $alumnoId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo horario: " . $e->getMessage());
            return [];
        }
    }

    // Obtener calificaciones (ejemplo básico)
    public function obtenerCalificaciones($alumnoId) {
        try {
            // Esta es una estructura básica - puedes expandirla según necesites
            $query = "SELECT m.nombre as materia, 
                             'Sin calificación' as calificacion,
                             p.nombre_completo as profesor
                      FROM grupo_materia gm
                      JOIN materias m ON gm.id_materia = m.id
                      JOIN profesores p ON gm.id_profesor = p.id
                      JOIN grupos g ON gm.id_grupo = g.id
                      JOIN alumnos a ON a.grupo_id = g.id
                      WHERE a.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $alumnoId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo calificaciones: " . $e->getMessage());
            return [];
        }
    }

    // Obtener avisos para el alumno
    public function obtenerAvisos($grupoId = null) {
        try {
            $query = "SELECT titulo, mensaje, fecha, tipo 
                      FROM avisos 
                      WHERE grupo_id = :grupo_id OR grupo_id IS NULL
                      ORDER BY fecha DESC
                      LIMIT 5";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":grupo_id", $grupoId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo avisos: " . $e->getMessage());
            return [];
        }
    }

    // Obtener administrativos para contactar
    public function obtenerAdministrativos() {
        try {
            $query = "SELECT id, nombre_completo, num_empleado, horario_atencion, ubicacion 
                      FROM administrativos 
                      ORDER BY nombre_completo";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo administrativos: " . $e->getMessage());
            return [];
        }
    }

    // Obtener profesores para contactar
    public function obtenerProfesores() {
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

    // Obtener compañeros de grupo
    public function obtenerCompanerosGrupo($alumnoId) {
        try {
            $query = "SELECT a.nombre_completo, a.matricula, a.carrera, g.nombre as grupo
                      FROM alumnos a
                      JOIN grupos g ON a.grupo_id = g.id
                      WHERE a.grupo_id = (SELECT grupo_id FROM alumnos WHERE id = :id)
                      AND a.id != :id
                      ORDER BY a.nombre_completo";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $alumnoId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo compañeros: " . $e->getMessage());
            return [];
        }
    }
}
?>