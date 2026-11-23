import Estudiante from "./components/Estudiantes";
import "./App.css";

export default function App() {

    const estudiantes = [
      {id:1, nombre:"Paul", carrera:"Ingeniería"},
      {id:2, nombre:"Sofia", carrera:"Psicología"},
      {id:3, nombre:"Enrique", carrera:"Medicina"},
      {id:4, nombre:"Stephanie", carrera:"Medicina"},
    ];
  return (
    <main className="contenedor">
      <h1>Listado de estudiantes</h1>
      <form action="">
        <input type="text" placeholder="Nombre" />
        <input type="text" placeholder="Carrera" /> 
        <button type="button">Agregar</button> 
      </form>

      <h2>Estudiantes Registrados</h2>
      <ul>
        {estudiantes.map((estudiante) => {
          return (
            <Estudiante 
              key={estudiante.id} 
              estudiante={estudiante}
            />
          );
        })}
      </ul>

      <p>Total de estudiantes: {estudiantes.length}</p>

    </main>

  );
}

//