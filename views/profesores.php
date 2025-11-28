<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Gesture Dashboard</title>
    <link rel="stylesheet" href="/gestion_escolar/public/css/profesores.css">
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
                    <span class="user-name"><?php echo $datosProfesor['nombre_completo'] ?? 'Usuario'; ?></span>
                    <span class="user-sub-role">Profesor</span>
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
        <button class="nav-button" data-target="comunicados" onclick="showContent('comunicados')">Comunicados</button>
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
                </div>
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
            <div class="info-personal-grid-container">
                <!-- Primera fila -->
                <div class="fila">
                    <div class="campo">Nombre Completo: <br><strong><?php echo $datosProfesor['nombre_completo'] ?? 'No disponible'; ?></strong></div>
                    <div class="campo">Carrera(s) enfocada(s): <br><strong><?php echo $datosProfesor['carrera_enfocada'] ?? 'No disponible'; ?></strong></div>
                </div>

                <!-- Segunda fila -->
                <div class="fila">
                    <div class="campo">Matrícula: <br><strong><?php echo $datosProfesor['matricula'] ?? 'No disponible'; ?></strong></div>
                    <div class="campo">Horario asignado: <br><strong><?php echo $datosProfesor['horario_trabajo'] ?? 'No disponible'; ?></strong></div>
                </div>

                <!-- Tercera fila -->
                <div class="fila">
                    <div class="caja-grande">
                        <strong>Carga de Materias:</strong><br>
                        <?php if (!empty($materias)): ?>
                            <?php foreach ($materias as $materia): ?>
                                • <?php echo $materia['materia'] ?? 'Materia'; ?> (<?php echo $materia['grupo'] ?? 'Grupo'; ?>)<br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            No hay materias asignadas
                        <?php endif; ?>
                    </div>
                    <div class="caja-grande">
                        <strong>Grupos Asignados:</strong><br>
                        <?php if (!empty($grupos)): ?>
                            <?php foreach ($grupos as $grupo): ?>
                                • <?php echo $grupo['nombre'] ?? 'Grupo'; ?> - <?php echo $grupo['carrera'] ?? 'Carrera'; ?><br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            No hay grupos asignados
                        <?php endif; ?>
                    </div>
                    <div class="vertical-stack-container">
                        <div class="campo-peque">Aula(s): <br><strong>
                            <?php if (!empty($grupos)): ?>
                                <?php 
                                $aulas = array_unique(array_column($grupos, 'aula'));
                                echo implode(', ', $aulas);
                                ?>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </strong></div>
                    </div>
                </div>
            </div>
        </section>

                
                
        <section id="comunicados-content" class="content-section" style="display: none;">
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
                    Docentes</div>
                    <div id="docentes-content" class="seccion-contenido">
                        <div class="general-grid">
                            <?php if (!empty($otrosProfesores)): ?>
                                <?php foreach ($otrosProfesores as $profesor): ?>
                                    <div class="general-card">
                                        <span class="tamano"><?php echo $profesor['matricula'] ?? 'N/A'; ?></span>
                                        <span class="espacio"><?php echo $profesor['nombre_completo'] ?? 'Profesor'; ?></span>
                                        <span class="espacio"><?php echo $profesor['carrera_enfocada'] ?? 'Carrera no especificada'; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="general-card">
                                    <span class="tamano">No hay otros docentes disponibles</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                

                <!-- Pestañas de grupos -->
                <div class="horizontal-tabs-container" class="seccion-header">
                    <?php if (!empty($alumnos)): ?>
                        <?php foreach ($alumnos as $grupoId => $grupoData): ?>
                            <div id="grupo<?php echo $grupoId; ?>-header" class="seccion-header">
                                <?php echo $grupoData['grupo_nombre'] ?? 'Grupo'; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="seccion-header">
                            No hay grupos
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Contenido de cada grupo -->
                <?php if (!empty($alumnos)): ?>
                    <?php foreach ($alumnos as $grupoId => $grupoData): ?>
                        <div id="grupo<?php echo $grupoId; ?>-content" class="seccion-contenido">
                            <h4>Grupo: <?php echo $grupoData['grupo_nombre'] ?? 'Grupo'; ?></h4>
                            <div class="general-grid">
                                <?php if (!empty($grupoData['alumnos'])): ?>
                                    <?php foreach ($grupoData['alumnos'] as $alumno): ?>
                                        <div class="general-card">
                                            <span class="tamano"><?php echo $alumno['matricula'] ?? 'N/A'; ?></span>
                                            <span class="espacio"><?php echo $alumno['nombre_completo'] ?? 'Alumno'; ?></span>
                                            <span class="espacio"><?php echo $alumno['carrera'] ?? 'Carrera'; ?></span>
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
                <?php else: ?>
                    <div class="seccion-contenido">
                        <div class="general-grid">
                            <div class="general-card">
                                <span class="tamano">No tienes alumnos asignados</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </section>
    </main>
        <script src="/gestion_escolar/public/java/profesores.js"></script>
</body>
</html>