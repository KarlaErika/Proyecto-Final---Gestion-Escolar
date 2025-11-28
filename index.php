<?php
// C:\xampp\htdocs\gestion_escolar\index.php

// Manejar logout con el controlador
if (isset($_GET['p']) && $_GET['p'] === 'logout') {
    require_once __DIR__ . "/app/controllers/LogoutController.php";
    $logoutController = new LogoutController();
    $logoutController->logout();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/app/controllers/PaginasController.php";

$pagina = $_GET['p'] ?? 'index';
$controlador = new PaginasController();

switch ($pagina) {
    case 'alumnos':
        $controlador->alumnos();
        break;
    case 'profesores':
        $controlador->profesores();
        break;
    case 'administrativo':
        $controlador->administrativos();
        break;
    default:
        $controlador->index();
        break;
}
?>