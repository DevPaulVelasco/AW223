const botonBienvenida = document.getElementById('cambiar-bienvenida');
const tituloBienvenida = document.getElementById('titulo-principal');

botonBienvenida.addEventListener('click', () => {
    tituloBienvenida.textContent = '¡Bienvenido a mi portafolio web!';
});

const botonColor = document.getElementById("cambiar-color-habilidades");
const botonFuente = document.getElementById("cambiar-fuente-habilidades");
const habilidades = document.querySelectorAll(".lista-habilidades li");

botonColor.addEventListener("click", () => {
    habilidades.forEach((habilidad) => {
        habilidad.style.backgroundColor = "#da3413";
        habilidad.style.borderColor = "#660f0f";
    });
});

botonFuente.addEventListener("click", () => {
    habilidades.forEach((habilidad) => {
        habilidad.style.fontFamily = "Georgia, serif";
    });
});

const formulario = document.querySelector(".contact-form");
const campoNombre = document.getElementById("nombre");
const campoEmail = document.getElementById("email");

formulario.addEventListener("submit", (evento) => {
    evento.preventDefault();

    if (campoNombre.value.trim() === "") {
        alert("Por favor, escribe tu nombre.");
        campoNombre.focus();
        return;
    }

    if (campoEmail.value.trim() === "") {
        alert("Por favor, escribe tu correo electrónico.");
        campoEmail.focus();
        return;
    }

    alert("¡Gracias! Los datos del formulario se enviaron correctamente.");
    formulario.reset();
});