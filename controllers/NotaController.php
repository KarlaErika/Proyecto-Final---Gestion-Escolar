<?php
require_once __DIR__ . "/../models/NotaModel.php";

class NotaController {
    private $notaModel;

    public function __construct() {
        $this->notaModel = new NotaModel();
    }

    // Manejar todas las acciones del bloc de notas
    public function handleRequest() {
        // SOLUCIÓN: Verificar si la sesión ya está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autenticado']);
            exit;
        }

        $action = $_POST['action'] ?? '';
        $usuarioId = $_SESSION['usuario_id'];

        switch ($action) {
            case 'get_notes':
                $this->getNotes($usuarioId);
                break;
            case 'add_note':
                $this->addNote($usuarioId);
                break;
            case 'toggle_note':
                $this->toggleNote($usuarioId);
                break;
            case 'delete_note':
                $this->deleteNote($usuarioId);
                break;
            case 'update_note':
                $this->updateNote($usuarioId);
                break;
            default:
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
                break;
        }
    }

    private function getNotes($usuarioId) {
        $notas = $this->notaModel->obtenerNotas($usuarioId);
        echo json_encode(['success' => true, 'notes' => $notas]);
    }

    private function addNote($usuarioId) {
        $contenido = trim($_POST['contenido'] ?? '');
        
        if (empty($contenido)) {
            echo json_encode(['success' => false, 'error' => 'La nota no puede estar vacía']);
            return;
        }

        $success = $this->notaModel->agregarNota($usuarioId, $contenido);
        
        if ($success) {
            $notas = $this->notaModel->obtenerNotas($usuarioId);
            echo json_encode(['success' => true, 'notes' => $notas]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al agregar nota']);
        }
    }

    private function toggleNote($usuarioId) {
        $notaId = $_POST['note_id'] ?? 0;
        
        if ($notaId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de nota no válido']);
            return;
        }

        $success = $this->notaModel->toggleCompletada($notaId, $usuarioId);
        echo json_encode(['success' => $success]);
    }

    private function deleteNote($usuarioId) {
        $notaId = $_POST['note_id'] ?? 0;
        
        if ($notaId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de nota no válido']);
            return;
        }

        $success = $this->notaModel->eliminarNota($notaId, $usuarioId);
        
        if ($success) {
            $notas = $this->notaModel->obtenerNotas($usuarioId);
            echo json_encode(['success' => true, 'notes' => $notas]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al eliminar nota']);
        }
    }

    private function updateNote($usuarioId) {
        $notaId = $_POST['note_id'] ?? 0;
        $contenido = trim($_POST['contenido'] ?? '');
        
        if ($notaId <= 0 || empty($contenido)) {
            echo json_encode(['success' => false, 'error' => 'Datos no válidos']);
            return;
        }

        $success = $this->notaModel->actualizarNota($notaId, $usuarioId, $contenido);
        echo json_encode(['success' => $success]);
    }
}

// Ejecutar el controlador si es una petición AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $notaController = new NotaController();
    $notaController->handleRequest();
    exit; // Importante: salir después de ejecutar
}
?>