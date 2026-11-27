from app import mysql


# FUNCION PARA CONSULTAR TODO
def selectAll():
    cursor = mysql.connection.cursor()
    cursor.execute("SELECT * FROM tb_alumnos WHERE state = 1")
    consulta = cursor.fetchall()
    cursor.close()
    return consulta


# FUNCION PARA CONSULTAR POR ID
def selectById(id):
    cursor = mysql.connection.cursor()
    cursor.execute("SELECT * FROM tb_alumnos WHERE id = %s AND state = 1", (id,))
    consulta = cursor.fetchone()
    cursor.close()
    return consulta


# FUNCION PARA INSERTAR
def insertEstudiante(nombre, carrera):
    cursor = mysql.connection.cursor()
    cursor.execute(
        "INSERT INTO tb_alumnos (nombre, carrera) VALUES (%s, %s)",
        (nombre, carrera),
    )
    mysql.connection.commit()
    nuevo_id = cursor.lastrowid
    cursor.close()
    return nuevo_id


# FUNCION PARA BORRAR (softdelete)
def softDeleteEstudiante(id):
    cursor = mysql.connection.cursor()
    cursor.execute("UPDATE tb_alumnos SET state = 0 WHERE id = %s", (id,))
    mysql.connection.commit()
    cursor.close()


# FUNCION PARA ACTUALIZAR
def updateEstudiante(id, nombre, carrera):
    cursor = mysql.connection.cursor()
    cursor.execute(
        "UPDATE tb_alumnos SET nombre = %s, carrera = %s WHERE id = %s AND state = 1",
        (nombre, carrera, id),
    )
    mysql.connection.commit()
    cursor.close()


