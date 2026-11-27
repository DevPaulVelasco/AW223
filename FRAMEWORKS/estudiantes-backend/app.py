from flask import Flask
from flask_mysqldb import MySQL
from config import config

mysql = MySQL()


def create_app():
    app = Flask(__name__)
    app.config.from_object(config)
    mysql.init_app(app)

    from controllers.controladorAlumno import controladorAlumnoBP
    app.register_blueprint(controladorAlumnoBP)

    return app


if __name__ == '__main__':
    from app import create_app as _create_app
    app = _create_app()
    app.run(port=5000, debug=True)
