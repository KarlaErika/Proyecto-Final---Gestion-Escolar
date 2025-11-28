<?php
class PaginasController {

    // Página de alumnos
    public function alumnos() {
        
        // Verificar sesión con datos de BD
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
            header("Location: /gestion_escolar/");
            exit;
        }
        
        // Cargar modelo y obtener datos
        require_once __DIR__ . "/../models/AlumnoModel.php";
        require_once __DIR__ . "/../models/NotaModel.php";
        $notaModel = new NotaModel();
        $alumnoModel = new AlumnoModel();
        
        // Obtener datos del alumno
        $datosAlumno = $alumnoModel->obtenerDatosAlumno($_SESSION['usuario_id']);
        $horario = $alumnoModel->obtenerHorario($_SESSION['usuario_id']);
        $calificaciones = $alumnoModel->obtenerCalificaciones($_SESSION['usuario_id']);
        $avisos = $alumnoModel->obtenerAvisos($datosAlumno['grupo_id'] ?? null);
        
         // DATOS PARA CONTACTAR
        $administrativos = $alumnoModel->obtenerAdministrativos();
        $profesores = $alumnoModel->obtenerProfesores();
        $companeros = $alumnoModel->obtenerCompanerosGrupo($_SESSION['usuario_id']);
        // Pasar datos a la vista
        include __DIR__ . "/../views/alumnos.php";
    }

    // Página de profesores
    public function profesores() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
            header("Location: /gestion_escolar/");
            exit;
        }
        
        require_once __DIR__ . "/../models/ProfesorModel.php";
        require_once __DIR__ . "/../models/NotaModel.php";
        $notaModel = new NotaModel();
        $profesorModel = new ProfesorModel();
        
        $datosProfesor = $profesorModel->obtenerDatosProfesor($_SESSION['usuario_id']);
        $materias = $profesorModel->obtenerMateriasImpartidas($_SESSION['usuario_id']);
        $grupos = $profesorModel->obtenerGruposAsignados($_SESSION['usuario_id']);
        $horario = $profesorModel->obtenerHorarioProfesor($_SESSION['usuario_id']);
        
         // DATOS PARA CONTACTAR
        $administrativos = $profesorModel->obtenerAdministrativos();
        $otrosProfesores = $profesorModel->obtenerOtrosProfesores($_SESSION['usuario_id']);
        $alumnos = $profesorModel->obtenerAlumnosGrupos($_SESSION['usuario_id']);

        include __DIR__ . "/../views/profesores.php";
    }

    // Página de administrativos
    public function administrativos() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrativo') {
            header("Location: /gestion_escolar/");
            exit;
        }
        
        require_once __DIR__ . "/../models/AdministrativoModel.php";
        require_once __DIR__ . "/../models/NotaModel.php";
        $notaModel = new NotaModel();
        $adminModel = new AdministrativoModel();
        
        $datosAdmin = $adminModel->obtenerDatosAdministrativo($_SESSION['usuario_id']);
        $grupos = $adminModel->obtenerGruposAsignados($_SESSION['usuario_id']);
        $carreras = $adminModel->obtenerCarrerasAsignadas($_SESSION['usuario_id']);
        $todosLosGrupos = $adminModel->obtenerTodosLosGrupos();
        

        // DATOS PARA CONTACTAR
        $otrosAdministrativos = $adminModel->obtenerOtrosAdministrativos($_SESSION['usuario_id']);
        $profesores = $adminModel->obtenerTodosLosProfesores();
        $alumnos = $adminModel->obtenerAlumnosPorGrupo();
        $notas = $notaModel->obtenerNotas($_SESSION['usuario_id']);

        include __DIR__ . "/../views/administrativo.php";
    }

    // Página principal (login)
    public function index() {
        // Si ya está logueado, redirigir a su dashboard
        if (isset($_SESSION['usuario_id'])) {
            $this->redirigirSegunRol($_SESSION['rol']);
        }
        
        // Mostrar mensajes de error si existen
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['error']); // Limpiar el error después de mostrarlo
        
        include __DIR__ . "/../views/index.php";
    }

    private function redirigirSegunRol($rol) {
        switch ($rol) {
            case 'alumno':
                header("Location: /gestion_escolar/index.php?p=alumnos");
                break;
            case 'profesor':
                header("Location: /gestion_escolar/index.php?p=profesores");
                break;
            case 'administrativo':
                header("Location: /gestion_escolar/index.php?p=administrativo");
                break;
        }
        exit;
    }
}
?>