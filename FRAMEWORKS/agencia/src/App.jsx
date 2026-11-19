import { useState } from 'react';
import './App.css';
import canonImg from './img/canon.jpeg';
import cancunImg from './img/cancun.jpeg';
import monterreyImg from './img/monterrey.jpeg';
import qroImg from './img/qro.jpeg';

const destinos = [
  {
    id: 1,
    nombre: "Tuxtla Gutierrez, Mexico",
    ubicacion: "Chiapas",
    precio: "$1,500 mxn",
    descripcion: "Conoce el hermoso cañon del sumidero",
    imagen: canonImg
  },
  {
    id: 2,
    nombre: "Cancun, Mexico",
    ubicacion: "Quintana Roo",
    precio: "$4,500 mxn",
    descripcion: "Hermosas playas",
    imagen: cancunImg
  },
  {
    id: 3,
    nombre: "Monterrey, Mexico",
    ubicacion: "Nuevo Leon",
    precio: "$5,500 mxn",
    descripcion: "Conoce el cerro de la silla",
    imagen: monterreyImg
  },
  {
    id: 4,
    nombre: "Queretaro, Mexico",
    ubicacion: "Queretaro",
    precio: "$2,200 mxn",
    descripcion: "Hermosa Ciudad",
    imagen: qroImg
  }
];

function App() {
  const [busqueda, setBusqueda] = useState("");
  const [favoritos, setFavoritos] = useState([]); 
  const [detallesVisibles, setDetallesVisibles] = useState({});

  const destinosFiltrados = destinos.filter(destino => 
    destino.nombre.toLowerCase().includes(busqueda.toLowerCase()) ||
    destino.ubicacion.toLowerCase().includes(busqueda.toLowerCase())
  );

  const toggleFavorito = (id) => {
    if (favoritos.includes(id)) {
      setFavoritos(favoritos.filter(favId => favId !== id));
    } else {
      setFavoritos([...favoritos, id]);
    }
  };

  const toggleDetalles = (id) => {
    setDetallesVisibles({
      ...detallesVisibles,
      [id]: !detallesVisibles[id]
    });
  };

  return (
    <div classname="contendor-principal">
      <header classname="encabezado">
        <h1>Agencia de viajes</h1>
        <div classname="contador">
          favoritos: {favoritos.length}

        </div>

      </header>

      <input
      type='text'
      className='input-busqueda'
      placeholder='a donde quieres viajar?...'
      value={busqueda}
      onChange={(e) => setBusqueda (e.target.value)}
      />
     <div className="grid-tarjetas">
        {destinosFiltrados.map((destino) => {

          const esFavorito = favoritos.includes(destino.id);
          const verDetalles = detallesVisibles[destino.id];

          return (
            <div key={destino.id} className="tarjeta">
              <img src={destino.imagen} alt={destino.nombre} />
              
              <div className="cuerpo-tarjeta">
                <h3>{destino.nombre}</h3>
                <p style={{ margin: '5px 0', color: '#666' }}> {destino.ubicacion}</p>
                <p className="precio">{destino.precio}</p>


                {verDetalles && (
                  <p className="descripcion">
                    {destino.descripcion}
                  </p>
                )}

                {/* Botones de interacción */}
                <div className="contenedor-botones">
                  {/* Botón de Favorito (b) */}
                  <button 
                    onClick={() => toggleFavorito(destino.id)}
                    className={`btn ${esFavorito ? 'btn-favorito-activo' : 'btn-favorito-inactivo'}`}
                  >
                    {esFavorito ? 'Favorito' : 'Marcar'}
                  </button>


                  <button 
                    onClick={() => toggleDetalles(destino.id)}
                    className="btn btn-detalles"
                  >
                    {verDetalles ? 'Ocultar' : '+ Detalles'}
                  </button>
                </div>

              </div>
            </div>
          );
        })}
      </div>

    </div>
  );
}

export default App;