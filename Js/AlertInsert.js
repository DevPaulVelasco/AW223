const parametros = new URLSearchParams(window.location.search);
const estado = parametros.get("estado");

if (estado === "exito" || estado === "error") {
    alert(
        estado === "exito"
            ? "Éxito: tu mensaje fue guardado."
            : "Error al guardar tu mensaje."
    );

    parametros.delete("estado");
    const query = parametros.toString();
    const nuevaUrl = `${window.location.pathname}${query ? `?${query}` : ""}${window.location.hash}`;
    window.history.replaceState({}, document.title, nuevaUrl);
}