<?php
require_once __DIR__ . "/../../app/config/Database.php";

class ProfesorModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function obtenerDatosProfesor($profesorId) {
        try {
            $query = "SELECT * FROM profesores WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $profesorId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo datos profesor: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerMateriasImpartidas($profesorId) {
        try {
            $query = "SELECT DISTINCT m.nombre as materia, g.nombre as grupo, g.id as grupo_id
                      FROM profesor_materia pm
                      JOIN materias m ON pm.id_materia = m.id
                      JOIN grupo_materia gm ON gm.id_materia = m.id AND gm.id_profesor = pm.id_profesor
                      JOIN grupos g ON gm.id_grupo = g.id
                      WHERE pm.id_profesor = :id
                      ORDER BY g.nombre, m.nombre";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $profesorId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo materias: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerGruposAsignados($profesorId) {
        try {
            $query = "SELECT DISTINCT g.nombre, g.carrera, g.grado, g.aula
                      FROM grupo_materia gm
                      JOIN grupos g ON gm.id_grupo = g.id
                      WHERE gm.id_profesor = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $profesorId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo grupos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerHorarioProfesor($profesorId) {
        try {
            $query = "SELECT m.nombre as materia, g.nombre as grupo, g.turno, g.aula
                      FROM grupo_materia gm
                      JOIN materias m ON gm.id_materia = m.id
                      JOIN grupos g ON gm.id_grupo = g.id
                      WHERE gm.id_profesor = :id
                      ORDER BY g.turno";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $profesorId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo horario profesor: " . $e->getMessage());
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

    // Obtener otros profesores
    public function obtenerOtrosProfesores($profesorId) {
        try {
            $query = "SELECT id, nombre_completo, matricula, carrera_enfocada 
                      FROM profesores 
                      WHERE id != :id
                      ORDER BY nombre_completo";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $profesorId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo otros profesores: " . $e->getMessage());
            return [];
        }
    }

    // Obtener alumnos de los grupos que imparte (agrupados por grupo)
    public function obtenerAlumnosGrupos($profesorId) {
    try {
        $query = "SELECT g.id as grupo_id, g.nombre as grupo_nombre, 
                         a.id, a.nombre_completo, a.matricula, a.carrera
                  FROM alumnos a
                  JOIN grupos g ON a.grupo_id = g.id
                  JOIN grupo_materia gm ON gm.id_grupo = g.id
                  WHERE gm.id_profesor = :id
                  GROUP BY a.id, g.id  -- Esto elimina duplicados
                  ORDER BY g.nombre, a.nombre_completo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $profesorId);
        $stmt->execute();

        $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agrupar alumnos por grupo
        $alumnosPorGrupo = [];
        foreach ($alumnos as $alumno) {
            $grupoId = $alumno['grupo_id'];
            if (!isset($alumnosPorGrupo[$grupoId])) {
                $alumnosPorGrupo[$grupoId] = [
                    'grupo_nombre' => $alumno['grupo_nombre'],
                    'alumnos' => []
                ];
            }
            $alumnosPorGrupo[$grupoId]['alumnos'][] = $alumno;
        }
        
        return $alumnosPorGrupo;

    } catch (PDOException $e) {
        error_log("Error obteniendo alumnos: " . $e->getMessage());
        return [];
    }
}
}
?>