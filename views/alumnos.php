<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Gesture Dashboard</title>
    <link rel="stylesheet" href="/gestion_escolar/public/css/alumnos.css">
    <link href="https://fontawesome.com/">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="/gestion_escolar/public/imagenes/Logo.png" alt="School Gesture Logo" class="logo-img">
            <h1 class="logo-text">School Gesture</h1>
            </div>
            <div class="profile-wrapper">
            <div class="user-info">
                <div class="user-text">
                    <span class="user-name"><?php echo $datosAlumno['nombre_completo'] ?? 'Usuario'; ?></span>
                    <span class="user-sub-role">Alumno</span>
                </div>
                
                <div class="profile-picture" id="profile-toggler"></div>
            </div>

            <div class="profile-actions" id="profile-actions-menu" >
                
                <a href="#" class="action-button">
                    <div class="button-icon help"></div>
                    Recibir Ayuda
                </a>
                
                <a href="/gestion_escolar/index.php?p=logout" id="logout-button" class="action-button">
                    <div class="button-icon logout"></div>
                    Cerrar Sesion
                </a>
            </div>
        </div>
    </div>
    </header>
    
    <nav class="main-nav">
        <button class="nav-button active" data-target="inicio" onclick="showContent('inicio')">Inicio</button>
        <button class="nav-button" data-target="info-personal" onclick="showContent('info-personal')">Info Personal</button>
        <button class="nav-button" data-target="consultar" onclick="showContent('consultar')">Consultar</button>
        <button class="nav-button" data-target="contactar" onclick="showContent('contactar')">Contactar</button>
    </nav>
    <main class="dashboard-container">
        
        <section id="inicio-content" class="dashboard-content content-section active">
            <section class="card wide-card notices-card">
                <h2 class="card-title">Avisos Importantes</h2>
                 <?php if (!empty($avisos)): ?>
                    <?php foreach ($avisos as $aviso): ?>
                        <div class="card-item" style="height:90px; padding:10px; background:#f5f5f5; margin-bottom:10px; border-radius:4px;">
                            <strong><?php echo $aviso['titulo'] ?? 'Aviso'; ?></strong><br>
                            <?php echo $aviso['mensaje'] ?? 'Mensaje del aviso'; ?><br>
                            <small><?php echo date('d/m/Y', strtotime($aviso['fecha'] ?? 'now')); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card-item placeholder" style="height:90px;">HOY ENTREGA DE PROYECTO!!</div>
                    <div class="card-item placeholder" style="height:90px;"></div>
                    <div class="card-item placeholder" style="height:90px;"></div>
                <?php endif; ?>
            </section>

            <section class="card wide-card events-card">
                <h2 class="card-title">Eventos</h2>
                <div class="card-item placeholder" style="height:90px;">No hay eventos que se aproximen.</div>
                <div class="card-item placeholder" style="height:90px;"></div>
                <div class="card-item placeholder" style="height:90px;"></div>
                <div class="card-item placeholder" style="height:90px;"></div>
            </section>

        <div class="right-column">
            <section class="card notes-card">
                <h2 class="card-title">Bloc de Notas</h2>
                <div class="notes-input-group">
                    <input type="text" placeholder="Agregar Nota..." class="note-input" id="new-note-input">
                    <button class="add-note-btn" id="add-note-btn">+</button>
                </div>
                <div id="notes-container">
                    <?php if (!empty($notas)): ?>
                        <?php foreach ($notas as $nota): ?>
                            <div class="note-item" data-note-id="<?php echo $nota['id']; ?>">
                                <input type="checkbox" class="note-checkbox" <?php echo $nota['completado'] ? 'checked' : ''; ?>>
                                <span class="note-text <?php echo $nota['completado'] ? 'completed' : ''; ?>">
                                    <?php echo htmlspecialchars($nota['contenido']); ?>
                                </span>
                                <button class="delete-note-btn">X</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-notes-message">No hay notas. ¡Agrega una nueva!</div>
                    <?php endif; ?>
                </div>
            </section>
        
            <main class="dashboard-container-mensajes">
            <section class="card messages-card">
                    <h2 class="card-title messages-header" onclick="toggleMessages()">Mensajes Recibidos <span class="badge">9+</span></h2>
                    <div id="messages-body-content" class="messages-body">
                        <p>Aqui van los mensajes...</p>
                    </div>
            </section>
        </div>
        </section>

        <section id="info-personal-content" class="content-section info-personal-grid" style="display: none;">
    
        <div class="info-personal-grid-container">
        
                <!-- Primera fila -->
            <div class="fila">
                <div class="campo">Nombre Completo: <br><strong><?php echo $datosAlumno['nombre_completo'] ?? 'No disponible'; ?></strong></div>
                <div class="campo">Carrera: <br><strong><?php echo $datosAlumno['carrera'] ?? 'No disponible'; ?></strong></div>
                <div class="campo">Matrícula: <br><strong><?php echo $datosAlumno['matricula'] ?? 'No disponible'; ?></strong></div>
            </div>

            <!-- Segunda fila -->
            <div class="fila">
                <div class="campo">Encargado/a de Carrera: <br><strong><?php echo $datosAlumno['encargado_nombre'] ?? 'No disponible'; ?></strong></div>
                <div class="campo">Horario asignado: <br><strong><?php echo $datosAlumno['turno'] ?? 'No disponible'; ?></strong></div>
                <div class="campo-peque">Aula: <br><strong><?php echo $datosAlumno['aula'] ?? 'No disponible'; ?></strong></div>
                <div class="campo-peque">Grupo: <br><strong><?php echo $datosAlumno['nombre_grupo'] ?? 'No disponible'; ?></strong></div>
            </div>

            <!-- Tercera fila -->
            <div class="fila">
                <div class="caja-grande">
                    <strong>Carga de Materias:</strong><br>
                    <?php if (!empty($horario)): ?>
                        <?php foreach ($horario as $materia): ?>
                            • <?php echo $materia['materia'] ?? 'Materia'; ?><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No hay materias asignadas
                    <?php endif; ?>
                </div>
                <div class="caja-grande">
                    <strong>Profesores Asignados:</strong><br>
                    <?php if (!empty($horario)): ?>
                        <?php foreach ($horario as $materia): ?>
                            • <?php echo $materia['profesor'] ?? 'Profesor'; ?><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No hay profesores asignados
                    <?php endif; ?>
                </div>
            </div>

        </div>
        </section>

        <section id="consultar-content" class="content-section" style="display: none;">
            <div class="container">

        <div id="horario-header" class="seccion-header">
            Horario de Clase
        </div>
        <div id="horario-content" class="seccion-contenido">
            <div class="general-grid">
                <?php if (!empty($horario)): ?>
                    <?php foreach ($horario as $materia): ?>
                        <div class="general-card">
                            <strong><?php echo $materia['profesor'] ?? 'Profesor'; ?></strong>
                            <?php echo $materia['materia'] ?? 'Materia'; ?><br>
                            <small>Aula: <?php echo $materia['aula'] ?? 'N/A'; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="general-card">
                        <strong>No hay horario disponible</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="calificaciones-header" class="seccion-header">
            Calificaciones
        </div>
        <div id="calificaciones-content" class="seccion-contenido ">
            <div class="general-grid">
                <?php if (!empty($calificaciones)): ?>
                    <?php foreach ($calificaciones as $calif): ?>
                        <div class="general-card">
                            <strong><?php echo $calif['materia'] ?? 'Materia'; ?></strong>
                            Calificación: <?php echo $calif['calificacion'] ?? 'Sin calificación'; ?><br>
                            <small>Prof: <?php echo $calif['profesor'] ?? 'N/A'; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="general-card">
                        <strong>No hay calificaciones disponibles</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="reincripciones-header" class="seccion-header">
            Reincripciones/Incripciones
        </div>
        <div id="reincripciones-content" class="seccion-contenido">
            <div class="general-grid">
                <div class="general-card">
                    <strong>Estado: INSCRITO</strong>
                    PRIMER SEMESTRE
                </div>
                
            </div>
        </div>
    </div>
        </section>
        
        <section id="contactar-content" class="content-section" style="display: none;">
            <div class="container">
                <div id="administrativo-header" class="seccion-header">
                        Administrativo
                </div>
                    <div id="administrativo-content" class="seccion-contenido">
                        <div class="general-grid">
                            <?php if (!empty($administrativos)): ?>
                                <?php foreach ($administrativos as $admin): ?>
                                    <div class="general-card">
                                        <span class="tamano"><?php echo $admin['num_empleado'] ?? 'N/A'; ?></span>
                                        <span class="espacio"><?php echo $admin['nombre_completo'] ?? 'Administrativo'; ?></span>
                                        <span class="espacio"><?php echo $admin['horario_atencion'] ?? 'Horario no disponible'; ?></span>
                                        <span class="espacio"><?php echo $admin['ubicacion'] ?? 'Ubicación no disponible'; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="general-card">
                                    <span class="tamano">No hay administrativos disponibles</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <div id="docentes-header" class="seccion-header">
                    Docentes
                </div>
                <div id="docentes-content" class="seccion-contenido">
                    <div class="general-grid">
                        <?php if (!empty($profesores)): ?>
                            <?php foreach ($profesores as $profesor): ?>
                                <div class="general-card">
                                    <span class="tamano"><?php echo $profesor['matricula'] ?? 'N/A'; ?></span>
                                    <span class="espacio"><?php echo $profesor['nombre_completo'] ?? 'Profesor'; ?></span>
                                    <span class="espacio"><?php echo $profesor['carrera_enfocada'] ?? 'Carrera no especificada'; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="general-card">
                                <span class="tamano">No hay docentes disponibles</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="grupo-header" class="seccion-header">
                    Grupo
                </div>
                <div id="grupo-content" class="seccion-contenido">
                    <div class="general-grid">
                        <?php if (!empty($companeros)): ?>
                            <?php foreach ($companeros as $companero): ?>
                                <div class="general-card">
                                    <span class="tamano"><?php echo $companero['matricula'] ?? 'N/A'; ?></span>
                                    <span class="espacio"><?php echo $companero['nombre_completo'] ?? 'Compañero'; ?></span>
                                    <span class="espacio"><?php echo $companero['carrera'] ?? 'Carrera'; ?></span>
                                    <span class="espacio">Grupo: <?php echo $companero['grupo'] ?? 'N/A'; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="general-card">
                                <span class="tamano">Grupo Asignado</span>
                                <div class="general-grid">
               
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
        <script src="/gestion_escolar/public/java/alumnos.js"></script>
</body>
</html>