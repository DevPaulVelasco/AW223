from flask import Blueprint, request, jsonify
from Models.modelEstudiante import (
    selectAll,
    selectById,
    insertEstudiante,
    updateEstudiante,
    softDeleteEstudiante,
)

controladorAlumnoBP = Blueprint('controladorAlumno', __name__)


def _get_request_data():
    return request.get_json(silent=True) or request.form


# Ruta para arranque del servidor
@controladorAlumnoBP.route('/', methods=['GET'])
def inicio():
    return "Hola soy el backend de Alumno"


# Ruta para insert
@controladorAlumnoBP.route('/guardarAlumno', methods=['POST'])
def guardar():
    data = _get_request_data()
    nombreReq = str(data.get('txtNombre') or data.get('nombre') or '').strip()
    carreraReq = str(data.get('txtCarrera') or data.get('carrera') or '').strip()

    if not nombreReq or not carreraReq:
        return jsonify({"Error": "Los campos txtNombre y txtCarrera son obligatorios"}), 400

    try:
        nuevo_id = insertEstudiante(nombreReq, carreraReq)
        return jsonify({"Completado": "Estudiante guardado", "id": nuevo_id}), 201
    except Exception as e:
        return jsonify({"Error": str(e)}), 500


# Ruta para consulta todo
@controladorAlumnoBP.route('/consultatodo', methods=['GET'])
def consultatodo():
    try:
        estudiantes = selectAll()
        return jsonify(estudiantes), 200
    except Exception as e:
        return jsonify({"Error": str(e)}), 500


# Ruta para consultar por id
@controladorAlumnoBP.route('/consultarPorId', methods=['GET'])
@controladorAlumnoBP.route('/consultarPorId/<int:id>', methods=['GET'])
@controladorAlumnoBP.route('/consultarAlumno/<int:id>', methods=['GET'])
def consultarPorId(id=None):
    data = _get_request_data()
    idReq = id if id is not None else (request.args.get('id') or data.get('id') or data.get('txtId'))

    if not idReq:
        return jsonify({"Error": "Se requiere el id del estudiante"}), 400

    try:
        estudiante = selectById(idReq)
        if not estudiante:
            return jsonify({"Error": "Estudiante no encontrado"}), 404
        return jsonify(estudiante), 200
    except Exception as e:
        return jsonify({"Error": str(e)}), 500


# Ruta para actualizar estudiante
@controladorAlumnoBP.route('/actualizarAlumno', methods=['PUT'])
@controladorAlumnoBP.route('/actualizarAlumno/<int:id>', methods=['PUT'])
def actualizar(id=None):
    data = _get_request_data()
    idReq = id if id is not None else (data.get('id') or data.get('txtId'))
    nombreReq = str(data.get('txtNombre') or data.get('nombre') or '').strip()
    carreraReq = str(data.get('txtCarrera') or data.get('carrera') or '').strip()

    if not idReq or not nombreReq or not carreraReq:
        return jsonify({"Error": "Los campos id, txtNombre y txtCarrera son obligatorios"}), 400

    try:
        if not selectById(idReq):
            return jsonify({"Error": "Estudiante no encontrado"}), 404
        updateEstudiante(idReq, nombreReq, carreraReq)
        return jsonify({"Completado": "Estudiante actualizado correctamente"}), 200
    except Exception as e:
        return jsonify({"Error": str(e)}), 500


# Ruta para eliminar (soft delete)
@controladorAlumnoBP.route('/', methods=['DELETE'])
@controladorAlumnoBP.route('/eliminarAlumno', methods=['DELETE'])
@controladorAlumnoBP.route('/eliminarAlumno/<int:id>', methods=['DELETE'])
def eliminar(id=None):
    data = _get_request_data()
    idReq = id if id is not None else (data.get('id') or data.get('txtId') or request.args.get('id'))

    if not idReq:
        return jsonify({"Error": "Se requiere el id del alumno"}), 400

    try:
        softDeleteEstudiante(idReq)
        return jsonify({"Completado": "Alumno eliminado"}), 200
    except Exception as e:
        return jsonify({"Error": str(e)}), 500
