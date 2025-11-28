<?php
require_once __DIR__ . "/../models/ChatModel.php";

class ChatController {
    private $chatModel;

    public function __construct() {
        $this->chatModel = new ChatModel();
    }

    public function handleRequest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autenticado']);
            exit;
        }

        $action = $_POST['action'] ?? '';
        $usuarioId = $_SESSION['usuario_id'];
        $rol = $_SESSION['rol'];

        switch ($action) {
            case 'get_conversations':
                $this->getConversations($usuarioId, $rol);
                break;
            case 'get_messages':
                $this->getMessages($usuarioId);
                break;
            case 'send_message':
                $this->sendMessage($usuarioId);
                break;
            case 'create_conversation':
                $this->createConversation($usuarioId);
                break;
            case 'search_users':
                $this->searchUsers($usuarioId);
                break;
            default:
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
                break;
        }
    }

    private function getConversations($usuarioId, $rol) {
        $conversaciones = $this->chatModel->obtenerConversaciones($usuarioId, $rol);
        echo json_encode(['success' => true, 'conversaciones' => $conversaciones]);
    }

    private function getMessages($usuarioId) {
        $conversacionId = $_POST['conversacion_id'] ?? 0;
        
        if ($conversacionId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de conversación no válido']);
            return;
        }

        $mensajes = $this->chatModel->obtenerMensajes($conversacionId, $usuarioId);
        echo json_encode(['success' => true, 'mensajes' => $mensajes]);
    }

    private function sendMessage($usuarioId) {
        $conversacionId = $_POST['conversacion_id'] ?? 0;
        $mensaje = trim($_POST['mensaje'] ?? '');
        
        if ($conversacionId <= 0 || empty($mensaje)) {
            echo json_encode(['success' => false, 'error' => 'Datos no válidos']);
            return;
        }

        $result = $this->chatModel->enviarMensaje($conversacionId, $usuarioId, $mensaje);
        echo json_encode($result);
    }

    private function createConversation($usuarioId) {
        $otroUsuarioId = $_POST['usuario_id'] ?? 0;
        
        if ($otroUsuarioId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario no válido']);
            return;
        }

        $result = $this->chatModel->crearConversacion($usuarioId, $otroUsuarioId);
        echo json_encode($result);
    }

    private function searchUsers($usuarioId) {
        $termino = trim($_POST['termino'] ?? '');
        
        if (empty($termino)) {
            echo json_encode(['success' => true, 'usuarios' => []]);
            return;
        }

        $usuarios = $this->chatModel->buscarUsuarios($termino, $usuarioId);
        echo json_encode(['success' => true, 'usuarios' => $usuarios]);
    }
}

// Ejecutar el controlador si es una petición AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $chatController = new ChatController();
    $chatController->handleRequest();
}
?>