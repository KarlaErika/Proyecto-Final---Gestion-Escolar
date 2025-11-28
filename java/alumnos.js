/**
@param {string} sectionId 
 */

function showContent(sectionId) {
    // 1. Ocultar todas las secciones
    const allSections = document.querySelectorAll('.content-section');
    allSections.forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none'; 
    });

    // 2. Mostrar la sección seleccionada
    const targetSection = document.getElementById(sectionId + '-content');
    if (targetSection) {
        targetSection.classList.add('active');
        
        // Si es la página de inicio, usa 'flex' para el layout de 3 columnas
        if (sectionId === 'inicio') {
            targetSection.style.display = 'flex'; 
        } else {
            // Para las otras páginas, usa 'block'
            targetSection.style.display = 'block';
        }
    }

    // 3. Manejar el estado activo del botón
    const allButtons = document.querySelectorAll('.nav-button');
    allButtons.forEach(button => {
        button.classList.remove('active');
    });

    // Añadir la clase 'active' al botón clicado
    const clickedButton = document.querySelector(`.nav-button[data-target="${sectionId}"]`);
    if (clickedButton) {
        clickedButton.classList.add('active');
    }
}

/**
 * Función para alternar la visibilidad del cuerpo de mensajes.
 * Se llama desde el encabezado de "Mensajes Recibidos" en el HTML.
 */
function toggleMessages() {
    const messagesBody = document.getElementById('messages-body-content');
    if (messagesBody) {
        // Esta línea alterna la clase CSS que hace el despliegue
        messagesBody.classList.toggle('messages-expanded');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Seleccionar todos los títulos de las opciones
    const titulos = document.querySelectorAll('.titulo-opcion');
    
    // 2. Iterar sobre cada título para adjuntar un evento de clic
    titulos.forEach(titulo => {
        titulo.addEventListener('click', function() {
            // Obtener el elemento 'li' padre de este título
            const elementoPadre = this.parentElement;
            // Obtener el contenido desplegable de este elemento
            const contenido = elementoPadre.querySelector('.contenido');

            // --- Lógica para cerrar otros elementos ---
            // 3. Iterar sobre todos los contenidos para cerrarlos
            document.querySelectorAll('.contenido').forEach(otroContenido => {
                // Si el contenido actual no es el contenido en el que hicimos clic:
                if (otroContenido !== contenido) {
                    otroContenido.classList.remove('activo'); // Cierra el contenido
                    otroContenido.previousElementSibling.classList.remove('activo'); // Quita estilo 'activo' al título
                }
            });
            
            // --- Lógica para abrir/cerrar el elemento actual ---
            // 4. Alternar la clase 'activo' en el contenido
            contenido.classList.toggle('activo');
            // 5. Alternar la clase 'activo' en el título
            this.classList.toggle('activo');
        });
    });
});

// Funcionalidad JavaScript (scripts.js) con marcado de sección activa

const headers = document.querySelectorAll('.seccion-header');

headers.forEach(header => {
    header.addEventListener('click', () => {
        const contentId = header.id.replace('-header', '-content');
        const content = document.getElementById(contentId);

        // 1. Ocultar todos los contenidos y desactivar todos los encabezados
        // Esto asegura que solo uno esté abierto/activo a la vez (comportamiento de acordeón)
        document.querySelectorAll('.seccion-contenido').forEach(c => {
            if (c !== content) {
                c.classList.remove('show');
            }
        });

        document.querySelectorAll('.seccion-header').forEach(h => {
            if (h !== header) {
                h.classList.remove('active-header');
            }
        });


        // 2. Alternar la visibilidad del contenido clickeado
        const isCurrentlyOpen = content.classList.toggle('show');

        // 3. Marcar el encabezado como activo SOLO si el contenido está abierto
        if (isCurrentlyOpen) {
            header.classList.add('active-header');
        } else {
            // Si el contenido se está cerrando, también quitamos la marca de activo
            header.classList.remove('active-header');
        }
    });
});

// ... (código anterior)

// Funcionalidad JavaScript (scripts.js)

// El activador es la foto de perfil (ID: profile-toggler)
const toggler = document.getElementById('profile-toggler'); 
// El elemento a mostrar/ocultar es la sección de acciones (ID: profile-actions-menu)
const actionsMenu = document.getElementById('profile-actions-menu'); 

if (toggler && actionsMenu) {
    // Añadir el evento de click a la foto de perfil
    toggler.addEventListener('click', () => {
        // 🚨 CAMBIO CLAVE: Alternar la clase 'open' para la animación 🚨
        actionsMenu.classList.toggle('open'); 
    });
} else {
    console.error("No se encontraron los elementos necesarios para el despliegue. Verifica las IDs en tu HTML.");
}

// Bloc de Notas Funcional - VERSIÓN MEJORADA
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Inicializando bloc de notas...');
    
    const notesContainer = document.getElementById('notes-container');
    const newNoteInput = document.getElementById('new-note-input');
    const addNoteBtn = document.getElementById('add-note-btn');
    
    if (!notesContainer || !newNoteInput || !addNoteBtn) {
        console.error('❌ No se encontraron elementos del bloc de notas');
        return;
    }
    
    console.log('✅ Elementos encontrados correctamente');
    
    // Estado del loading
    let isLoading = false;
    
    // Función para mostrar loading
    function setLoading(state) {
        isLoading = state;
        addNoteBtn.disabled = state;
        addNoteBtn.textContent = state ? '⏳' : '+';
    }
    
    // Función para agregar nota
    async function addNote() {
        if (isLoading) return;
        
        const contenido = newNoteInput.value.trim();
        console.log('➕ Intentando agregar nota:', contenido);
        
        if (!contenido) {
            alert('Por favor escribe una nota');
            return;
        }
        
        setLoading(true);
        
        try {
            const formData = new FormData();
            formData.append('action', 'add_note');
            formData.append('contenido', contenido);
            
            console.log('📤 Enviando petición...');
            const response = await fetch('/gestion_escolar/app/controllers/NotaController.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            console.log('📥 Respuesta recibida:', result);
            
            if (result.success) {
                newNoteInput.value = '';
                await loadNotes();
                console.log('✅ Nota agregada correctamente');
            } else {
                alert('Error: ' + (result.error || 'Desconocido'));
            }
        } catch (error) {
            console.error('❌ Error de conexión:', error);
            alert('Error al conectar con el servidor');
        } finally {
            setLoading(false);
        }
    }
    
    // Función para cargar notas
    async function loadNotes() {
        try {
            console.log('📥 Cargando notas...');
            const formData = new FormData();
            formData.append('action', 'get_notes');
            
            const response = await fetch('/gestion_escolar/app/controllers/NotaController.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            console.log('📋 Notas cargadas:', result.notes);
            
            if (result.success) {
                renderNotes(result.notes);
            }
        } catch (error) {
            console.error('❌ Error cargando notas:', error);
        }
    }
    
    // Función para renderizar notas
    function renderNotes(notes) {
        console.log('🎨 Renderizando', notes.length, 'notas');
        
        if (notes.length === 0) {
            notesContainer.innerHTML = '<div class="no-notes-message">No hay notas. ¡Agrega una nueva!</div>';
            return;
        }
        
        notesContainer.innerHTML = notes.map(note => `
            <div class="note-item" data-note-id="${note.id}">
                <input type="checkbox" class="note-checkbox" ${note.completado ? 'checked' : ''}>
                <span class="note-text ${note.completado ? 'completed' : ''}">
                    ${note.contenido}
                </span>
                <small class="note-date">${formatDate(note.fecha_creacion)}</small>
                <button class="delete-note-btn" title="Eliminar nota">X</button>
            </div>
        `).join('');
        
        // Agregar eventos
        attachNoteEvents();
    }
    
    // Función para formatear fecha
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Función para agregar eventos a las notas
    function attachNoteEvents() {
        // Checkboxes
        document.querySelectorAll('.note-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const noteId = this.parentElement.dataset.noteId;
                toggleNote(noteId, this);
            });
        });
        
        // Botones de eliminar
        document.querySelectorAll('.delete-note-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const noteId = this.parentElement.dataset.noteId;
                deleteNote(noteId);
            });
        });
    }
    
    // Función para toggle note
    async function toggleNote(noteId, checkbox) {
        try {
            const formData = new FormData();
            formData.append('action', 'toggle_note');
            formData.append('note_id', noteId);
            
            const response = await fetch('/gestion_escolar/app/controllers/NotaController.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                const noteText = checkbox.parentElement.querySelector('.note-text');
                noteText.classList.toggle('completed', checkbox.checked);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Función para eliminar nota
    async function deleteNote(noteId) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta nota?')) return;
        
        try {
            const formData = new FormData();
            formData.append('action', 'delete_note');
            formData.append('note_id', noteId);
            
            const response = await fetch('/gestion_escolar/app/controllers/NotaController.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                await loadNotes();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Event listeners
    addNoteBtn.addEventListener('click', addNote);
    newNoteInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') addNote();
    });
    
    // Cargar notas al iniciar
    loadNotes();
    console.log('✅ Bloc de notas inicializado correctamente');
});

