<?php
require_once __DIR__ . "/../../app/config/Database.php";

class ChatModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // Obtener conversaciones del usuario
    public function obtenerConversaciones($usuarioId, $rol) {
        try {
            $query = "SELECT DISTINCT 
                         c.id as conversacion_id,
                         CASE 
                             WHEN c.id_usuario1 = :usuario_id THEN u2.usuario
                             ELSE u1.usuario
                         END as nombre_usuario,
                         CASE 
                             WHEN c.id_usuario1 = :usuario_id THEN u2.rol
                             ELSE u1.rol
                         END as rol_usuario,
                         (SELECT mensaje FROM mensajes 
                          WHERE conversacion_id = c.id 
                          ORDER BY fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                         (SELECT fecha_envio FROM mensajes 
                          WHERE conversacion_id = c.id 
                          ORDER BY fecha_envio DESC LIMIT 1) as fecha_ultimo_mensaje,
                         (SELECT COUNT(*) FROM mensajes 
                          WHERE conversacion_id = c.id AND leido = 0 
                          AND usuario_id != :usuario_id) as mensajes_no_leidos
                      FROM conversaciones c
                      JOIN usuarios u1 ON c.id_usuario1 = u1.id
                      JOIN usuarios u2 ON c.id_usuario2 = u2.id
                      WHERE c.id_usuario1 = :usuario_id OR c.id_usuario2 = :usuario_id
                      ORDER BY fecha_ultimo_mensaje DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario_id", $usuarioId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo conversaciones: " . $e->getMessage());
            return [];
        }
    }

    // Obtener mensajes de una conversación
    public function obtenerMensajes($conversacionId, $usuarioId) {
        try {
            // Verificar que el usuario pertenece a la conversación
            $queryVerificar = "SELECT id FROM conversaciones 
                             WHERE id = :conversacion_id 
                             AND (id_usuario1 = :usuario_id OR id_usuario2 = :usuario_id)";
            $stmtVerificar = $this->conn->prepare($queryVerificar);
            $stmtVerificar->bindParam(":conversacion_id", $conversacionId);
            $stmtVerificar->bindParam(":usuario_id", $usuarioId);
            $stmtVerificar->execute();

            if (!$stmtVerificar->fetch()) {
                return ['error' => 'No tienes acceso a esta conversación'];
            }

            // Obtener mensajes
            $query = "SELECT m.*, u.usuario, u.rol
                      FROM mensajes m
                      JOIN usuarios u ON m.usuario_id = u.id
                      WHERE m.conversacion_id = :conversacion_id
                      ORDER BY m.fecha_envio ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":conversacion_id", $conversacionId);
            $stmt->execute();

            // Marcar mensajes como leídos
            $this->marcarMensajesLeidos($conversacionId, $usuarioId);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obteniendo mensajes: " . $e->getMessage());
            return ['error' => 'Error al obtener mensajes'];
        }
    }

    // Enviar mensaje
    public function enviarMensaje($conversacionId, $usuarioId, $mensaje) {
        try {
            // Verificar que el usuario pertenece a la conversación
            $queryVerificar = "SELECT id FROM conversaciones 
                             WHERE id = :conversacion_id 
                             AND (id_usuario1 = :usuario_id OR id_usuario2 = :usuario_id)";
            $stmtVerificar = $this->conn->prepare($queryVerificar);
            $stmtVerificar->bindParam(":conversacion_id", $conversacionId);
            $stmtVerificar->bindParam(":usuario_id", $usuarioId);
            $stmtVerificar->execute();

            if (!$stmtVerificar->fetch()) {
                return ['success' => false, 'error' => 'No tienes acceso a esta conversación'];
            }

            $query = "INSERT INTO mensajes (conversacion_id, usuario_id, mensaje, fecha_envio) 
                      VALUES (:conversacion_id, :usuario_id, :mensaje, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":conversacion_id", $conversacionId);
            $stmt->bindParam(":usuario_id", $usuarioId);
            $stmt->bindParam(":mensaje", $mensaje);
            
            if ($stmt->execute()) {
                return ['success' => true, 'mensaje_id' => $this->conn->lastInsertId()];
            } else {
                return ['success' => false, 'error' => 'Error al enviar mensaje'];
            }

        } catch (PDOException $e) {
            error_log("Error enviando mensaje: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error al enviar mensaje'];
        }
    }

    // Crear nueva conversación
    public function crearConversacion($usuarioId1, $usuarioId2) {
        try {
            // Verificar si ya existe una conversación
            $queryVerificar = "SELECT id FROM conversaciones 
                             WHERE (id_usuario1 = :usuario1 AND id_usuario2 = :usuario2)
                             OR (id_usuario1 = :usuario2 AND id_usuario2 = :usuario1)";
            $stmtVerificar = $this->conn->prepare($queryVerificar);
            $stmtVerificar->bindParam(":usuario1", $usuarioId1);
            $stmtVerificar->bindParam(":usuario2", $usuarioId2);
            $stmtVerificar->execute();

            $conversacionExistente = $stmtVerificar->fetch();

            if ($conversacionExistente) {
                return ['success' => true, 'conversacion_id' => $conversacionExistente['id']];
            }

            // Crear nueva conversación
            $query = "INSERT INTO conversaciones (id_usuario1, id_usuario2, fecha_creacion) 
                      VALUES (:usuario1, :usuario2, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario1", $usuarioId1);
            $stmt->bindParam(":usuario2", $usuarioId2);
            
            if ($stmt->execute()) {
                return ['success' => true, 'conversacion_id' => $this->conn->lastInsertId()];
            } else {
                return ['success' => false, 'error' => 'Error al crear conversación'];
            }

        } catch (PDOException $e) {
            error_log("Error creando conversación: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error al crear conversación'];
        }
    }

    // Marcar mensajes como leídos
    private function marcarMensajesLeidos($conversacionId, $usuarioId) {
        try {
            $query = "UPDATE mensajes SET leido = 1 
                      WHERE conversacion_id = :conversacion_id 
                      AND usuario_id != :usuario_id 
                      AND leido = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":conversacion_id", $conversacionId);
            $stmt->bindParam(":usuario_id", $usuarioId);
            $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error marcando mensajes como leídos: " . $e->getMessage());
        }
    }

    // Buscar usuarios para chat
   // Buscar usuarios por matrícula o número de empleado
public function buscarUsuarios($terminoBusqueda, $usuarioActualId) {
    try {
        $query = "SELECT u.id, u.usuario, u.rol, 
                         COALESCE(a.matricula, p.matricula, ad.num_empleado) as identificador,
                         CASE 
                             WHEN u.rol = 'alumno' THEN a.nombre_completo
                             WHEN u.rol = 'profesor' THEN p.nombre_completo
                             WHEN u.rol = 'administrativo' THEN ad.nombre_completo
                         END as nombre_completo
                  FROM usuarios u
                  LEFT JOIN alumnos a ON u.id = a.id AND u.rol = 'alumno'
                  LEFT JOIN profesores p ON u.id = p.id AND u.rol = 'profesor'
                  LEFT JOIN administrativos ad ON u.id = ad.id AND u.rol = 'administrativo'
                  WHERE (a.matricula LIKE :termino 
                         OR p.matricula LIKE :termino 
                         OR ad.num_empleado LIKE :termino
                         OR u.usuario LIKE :termino)
                  AND u.id != :usuario_actual
                  AND u.rol IN ('alumno', 'profesor', 'administrativo')
                  ORDER BY u.rol, nombre_completo
                  LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $terminoLike = "%$terminoBusqueda%";
        $stmt->bindParam(":termino", $terminoLike);
        $stmt->bindParam(":usuario_actual", $usuarioActualId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error buscando usuarios: " . $e->getMessage());
        return [];
    }
}

// Obtener información completa del usuario para mostrar en el chat
public function obtenerInfoUsuario($usuarioId) {
    try {
        $query = "SELECT u.id, u.usuario, u.rol,
                         CASE 
                             WHEN u.rol = 'alumno' THEN a.nombre_completo
                             WHEN u.rol = 'profesor' THEN p.nombre_completo
                             WHEN u.rol = 'administrativo' THEN ad.nombre_completo
                         END as nombre_completo,
                         CASE 
                             WHEN u.rol = 'alumno' THEN a.matricula
                             WHEN u.rol = 'profesor' THEN p.matricula
                             WHEN u.rol = 'administrativo' THEN ad.num_empleado
                         END as identificador,
                         CASE 
                             WHEN u.rol = 'alumno' THEN a.carrera
                             WHEN u.rol = 'profesor' THEN p.carrera_enfocada
                             WHEN u.rol = 'administrativo' THEN ad.responsabilidad
                         END as informacion_adicional
                  FROM usuarios u
                  LEFT JOIN alumnos a ON u.id = a.id AND u.rol = 'alumno'
                  LEFT JOIN profesores p ON u.id = p.id AND u.rol = 'profesor'
                  LEFT JOIN administrativos ad ON u.id = ad.id AND u.rol = 'administrativo'
                  WHERE u.id = :usuario_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuarioId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error obteniendo info usuario: " . $e->getMessage());
        return null;
    }
}

// Actualizar también la función de obtener conversaciones para mostrar mejor información
public function obtenerConversaciones($usuarioId, $rol) {
    try {
        $query = "SELECT DISTINCT 
                     c.id as conversacion_id,
                     CASE 
                         WHEN c.id_usuario1 = :usuario_id THEN c.id_usuario2
                         ELSE c.id_usuario1
                     END as otro_usuario_id,
                     CASE 
                         WHEN c.id_usuario1 = :usuario_id THEN u2.usuario
                         ELSE u1.usuario
                     END as nombre_usuario,
                     CASE 
                         WHEN c.id_usuario1 = :usuario_id THEN u2.rol
                         ELSE u1.rol
                     END as rol_usuario,
                     CASE 
                         WHEN c.id_usuario1 = :usuario_id THEN 
                             CASE 
                                 WHEN u2.rol = 'alumno' THEN a2.matricula
                                 WHEN u2.rol = 'profesor' THEN p2.matricula
                                 WHEN u2.rol = 'administrativo' THEN ad2.num_empleado
                             END
                         ELSE 
                             CASE 
                                 WHEN u1.rol = 'alumno' THEN a1.matricula
                                 WHEN u1.rol = 'profesor' THEN p1.matricula
                                 WHEN u1.rol = 'administrativo' THEN ad1.num_empleado
                             END
                     END as identificador,
                     CASE 
                         WHEN c.id_usuario1 = :usuario_id THEN 
                             CASE 
                                 WHEN u2.rol = 'alumno' THEN a2.nombre_completo
                                 WHEN u2.rol = 'profesor' THEN p2.nombre_completo
                                 WHEN u2.rol = 'administrativo' THEN ad2.nombre_completo
                             END
                         ELSE 
                             CASE 
                                 WHEN u1.rol = 'alumno' THEN a1.nombre_completo
                                 WHEN u1.rol = 'profesor' THEN p1.nombre_completo
                                 WHEN u1.rol = 'administrativo' THEN ad1.nombre_completo
                             END
                     END as nombre_completo,
                     (SELECT mensaje FROM mensajes 
                      WHERE conversacion_id = c.id 
                      ORDER BY fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                     (SELECT fecha_envio FROM mensajes 
                      WHERE conversacion_id = c.id 
                      ORDER BY fecha_envio DESC LIMIT 1) as fecha_ultimo_mensaje,
                     (SELECT COUNT(*) FROM mensajes 
                      WHERE conversacion_id = c.id AND leido = 0 
                      AND usuario_id != :usuario_id) as mensajes_no_leidos
                  FROM conversaciones c
                  JOIN usuarios u1 ON c.id_usuario1 = u1.id
                  JOIN usuarios u2 ON c.id_usuario2 = u2.id
                  LEFT JOIN alumnos a1 ON u1.id = a1.id AND u1.rol = 'alumno'
                  LEFT JOIN profesores p1 ON u1.id = p1.id AND u1.rol = 'profesor'
                  LEFT JOIN administrativos ad1 ON u1.id = ad1.id AND u1.rol = 'administrativo'
                  LEFT JOIN alumnos a2 ON u2.id = a2.id AND u2.rol = 'alumno'
                  LEFT JOIN profesores p2 ON u2.id = p2.id AND u2.rol = 'profesor'
                  LEFT JOIN administrativos ad2 ON u2.id = ad2.id AND u2.rol = 'administrativo'
                  WHERE c.id_usuario1 = :usuario_id OR c.id_usuario2 = :usuario_id
                  ORDER BY fecha_ultimo_mensaje DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuarioId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error obteniendo conversaciones: " . $e->getMessage());
        return [];
    }
}
}
?>