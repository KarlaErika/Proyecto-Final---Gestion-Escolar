<?php
// app/controllers/LogoutController.php
class LogoutController {
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: /gestion_escolar/");
        exit;
    }
}
?>