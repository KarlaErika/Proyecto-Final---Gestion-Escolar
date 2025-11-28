// Selección del rol (NO redirige)
const roleButtons = document.querySelectorAll(".role");
let selectedRole = "Alumno"; 

roleButtons.forEach(btn => {
    btn.addEventListener("click", () => {

        // Quitar active de todos
        roleButtons.forEach(b => b.classList.remove("active"));

        // Activar solo el seleccionado
        btn.classList.add("active");

        // Guardar rol
        selectedRole = btn.getAttribute("data-role");
        console.log("Rol seleccionado:", selectedRole);
    });
});

// Botón Login (AQUÍ SÍ redirige)
const loginBtn = document.querySelector(".btn-login");

loginBtn.addEventListener("click", () => {

    const usuarioInput = document.getElementById("usuario");
    const contraseñaInput = document.getElementById("contraseña");
    const userError = document.getElementById("user-error");
    const passError = document.getElementById("pass-error");

    let isValid = true;

    // Limpiar errores
    userError.textContent = "";
    passError.textContent = "";
    usuarioInput.classList.remove("input-error");
    contraseñaInput.classList.remove("input-error");

    // Validación
    if (usuarioInput.value.trim() === "") {
        userError.textContent = "El campo de usuario es obligatorio.";
        usuarioInput.classList.add("input-error");
        isValid = false;
    }

    if (contraseñaInput.value.trim() === "") {
        passError.textContent = "La contraseña es obligatoria.";
        contraseñaInput.classList.add("input-error");
        isValid = false;
    }

    if (!isValid) return;

    // Redirección REAL
    if (selectedRole === "Profesores") {
        window.location.href = "index.php?p=profesores";
        return;
    }

    if (selectedRole === "Alumno") {
        window.location.href = "index.php?p=alumnos";
        return;
    }

    if (selectedRole === "Administrativo") {
        window.location.href = "index.php?p=administrativo";
        return;
    }
});
const botones = document.querySelectorAll('.role');

botones.forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('#rol').value = btn.dataset.role;

        botones.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});
