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

