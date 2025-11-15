// Selección de roles (Alumno / Profesor / Administrativo)
const roleButtons = document.querySelectorAll(".role");
let selectedRole = "Alumno"; // valor por defecto

roleButtons.forEach(btn => {
    btn.addEventListener("click", () => {

        roleButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        selectedRole = btn.textContent.trim();
        console.log("Rol seleccionado:", selectedRole);
    });
});

// Evento del botón Iniciar Sesión
const loginBtn = document.querySelector(".btn-login");

loginBtn.addEventListener("click", () => {

    const usuario = document.querySelector("input[type='text']").value;
    const contraseña = document.querySelector("input[type='password']").value;

    if (usuario === "" || contraseña === "") {
        alert("Por favor llena todos los campos.");
        return;
    }

    // 🔥 Redirección SOLO para PROFESOR
    if (selectedRole === "Profesor") {
        window.location.href = "profesor.html";
        return;
    }

     if (selectedRole === "Alumno") {
        window.location.href = "alumnos.html";
        return;

    } if (selectedRole === "Administrativo") {
        window.location.href = "administrativo.html";
        return;
    }

    // Otros roles solo muestran mensaje
    alert(`Iniciando sesión como: ${selectedRole}\nUsuario: ${usuario}`);
});
