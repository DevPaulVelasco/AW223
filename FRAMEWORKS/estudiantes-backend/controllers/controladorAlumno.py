from flask import Blueprint

controladorAlumnoBP = Blueprint('controladorAlumno',__name__)

#ruta para arranque del servidor
@controladorAlumnoBP.route('/')
def inicio():
    return "Hola soy el backend de Alumno"
#Ruta para insert
@controladorAlumnoBP.route('/guardarAlumno' ,methods=['POST'])
def guardar():
    return 'Se guardo el alumno'

#Ruta para consulta todo
@controladorAlumnoBP.route('/consultatodo' ,methods=['GET'])
def consultatodo():
    return 'se consulto todo'

#Ruta para actualizar 
@controladorAlumnoBP.route('/actualizarAlumno' ,methods=['PUT'])
def actualizar():
    return 'Actualizado correctamente'

#Ruta para eliminar 
@controladorAlumnoBP.route('/eliminarAlumno' ,methods=['delete'])
def eliminar():
    return 'Alumno eliminado'