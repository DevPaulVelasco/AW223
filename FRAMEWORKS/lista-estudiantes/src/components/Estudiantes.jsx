export default function Estudiante({estudiante}){
    return(

        <li>
        <strong>{estudiante.nombre}</strong> {" -- "}{estudiante.carrera}
        <button type="button">Eliminar</button>
        </li>

    );

}

//