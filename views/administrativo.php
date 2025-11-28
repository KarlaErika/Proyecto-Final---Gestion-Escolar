<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Gesture Dashboard</title>
    <link rel="stylesheet" href="/gestion_escolar/public/css/admistrativo.css">
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
                    <span class="user-name"><?php echo $datosAdmin['nombre_completo'] ?? 'Usuario'; ?></span>
                    <span class="user-sub-role">Administrativo</span>
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
        <button class="nav-button" data-target="Comunicados" onclick="showContent('consultar')">Comunicados</button>
        <button class="nav-button" data-target="contactar" onclick="showContent('contactar')">Contactar</button>
    </nav>

    <main class="dashboard-container">
        
        <section id="inicio-content" class="dashboard-content content-section active">
            <section class="card wide-card notices-card">
                <h2 class="card-title">Avisos Importantes</h2>
                <div class="card-item placeholder" style="height:90px;"></div>
                <div class="card-item placeholder" style="height:90px;"></div>
                <div class="card-item placeholder" style="height:90px;"></div>
            </section>

            <section class="card wide-card events-card">
                <h2 class="card-title">Eventos</h2>
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
            </section>
        
            <main class="dashboard-container-mensajes">
            <section class="card messages-card">
                    <h2 class="card-title messages-header" onclick="toggleMessages()">Mensajes Recibidos 
                        <span class="badge">9+</span></h2>
                    <div id="messages-body-content" class="messages-body">
                        <p>Aqui van los mensajes...</p>
                    </div>
                </section>
        </div>
        </section>

        <section id="info-personal-content" class="content-section info-personal-grid" style="display: none;">
    
        <div class="info-personal-grid-container fila fila-docente">
        
                <!-- Primera fila -->
            <div class="columna-izquierda">
                 <div class="campo campo-docente">Nombre Completo: <br><strong><?php echo $datosAdmin['nombre_completo'] ?? 'No disponible'; ?></strong></div>
            <div class="campo campo-docente">Número de empleado: <br><strong><?php echo $datosAdmin['num_empleado'] ?? 'No disponible'; ?></strong></div>
            <div class="campo campo-docente">Horario de Atención: <br><strong><?php echo $datosAdmin['horario_atencion'] ?? 'No disponible'; ?></strong></div>
            <div class="campo campo-docente">Ubicación de Oficina: <br><strong><?php echo $datosAdmin['ubicacion'] ?? 'No disponible'; ?></strong></div>
            <div class="campo campo-docente">Responsabilidad Asignada: <br><strong><?php echo $datosAdmin['responsabilidad'] ?? 'No disponible'; ?></strong></div>
            </div>
            

            <!-- Segunda fila -->
            <div class="columna-derecha">
                <div class="caja-grande altura-doble">
                    <strong>Carrera(s) Encargada(s):</strong><br>
                    <?php if (!empty($carreras)): ?>
                        <?php foreach ($carreras as $carrera): ?>
                            • <?php echo $carrera['carrera'] ?? 'Carrera'; ?><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No hay carreras asignadas
                    <?php endif; ?>
                </div>
                
                <div class="caja-grande altura-doble">
                    <strong>Grupo(s) Encargado(s):</strong><br>
                    <?php if (!empty($grupos)): ?>
                        <?php foreach ($grupos as $grupo): ?>
                            • <?php echo $grupo['nombre'] ?? 'Grupo'; ?> - <?php echo $grupo['carrera'] ?? 'Carrera'; ?><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No hay grupos asignados
                    <?php endif; ?>
                </div>
                
                <div class="caja-grande altura-simple">Responsabilidad Asignada:</div>
            </div>
            

        </div>
        </section>

        <section id="consultar-content" class="content-section" style="display: none;">
            <div class="container">

        <div id="horario-header" class="seccion-header">
            Agregar Comunicado
        </div>
        <div id="horario-content" class="seccion-contenido">
            <div class="general-grid">
                <div class="general-card">
                    Escribe un comunicado...
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
                            <?php if (!empty($otrosAdministrativos)): ?>
                                <?php foreach ($otrosAdministrativos as $admin): ?>
                                    <div class="general-card">
                                        <span class="tamano"><?php echo $admin['num_empleado'] ?? 'N/A'; ?></span>
                                        <span class="espacio"><?php echo $admin['nombre_completo'] ?? 'Administrativo'; ?></span>
                                        <span class="espacio"><?php echo $admin['horario_atencion'] ?? 'Horario no disponible'; ?></span>
                                        <span class="espacio"><?php echo $admin['ubicacion'] ?? 'Ubicación no disponible'; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="general-card">
                                    <span class="tamano">No hay otros administrativos</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <div id="docentes-header" class="seccion-header">
                    Docentes</div>
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

                <div class="horizontal-tabs-container" class="seccion-header">
                    <?php if (!empty($todosLosGrupos)): ?>
                        <?php foreach ($todosLosGrupos as $grupo): ?>
                            <div id="grupo<?php echo $grupo['id']; ?>-header" class="seccion-header">
                                <?php echo $grupo['nombre']; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- CONTENIDO DE CADA GRUPO -->
                <?php if (!empty($todosLosGrupos)): ?>
                    <?php foreach ($todosLosGrupos as $grupo): ?>
                        <div id="grupo<?php echo $grupo['id']; ?>-content" class="seccion-contenido">
                            <div class="general-grid">
                                <?php 
                                $alumnosGrupo = $adminModel->obtenerAlumnosPorGrupo($grupo['id']);
                                if (!empty($alumnosGrupo)): 
                                ?>
                                    <?php foreach ($alumnosGrupo as $alumno): ?>
                                        <div class="general-card">
                                            <span class="tamano"><?php echo $alumno['matricula'] ?? 'N/A'; ?></span>
                                            <span class="espacio"><?php echo $alumno['nombre_completo'] ?? 'Alumno'; ?></span>
                                            <span class="espacio"><?php echo $alumno['carrera'] ?? 'Carrera'; ?></span>
                                            <span class="espacio">Grupo: <?php echo $alumno['grupo'] ?? 'N/A'; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="general-card">
                                        <span class="tamano">No hay alumnos en este grupo</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </section>
    </main>
        <script src="/gestion_escolar/public/java/administrativo.js"></script>
</body>
</html>