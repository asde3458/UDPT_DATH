from flask_sqlalchemy import SQLAlchemy
from flask import Flask
from typing import TypeVar, Type
import os

T = TypeVar('T')
db = SQLAlchemy()

def init_db(app: Flask) -> None:
    db_config = {
        'host': os.getenv('DB_HOST', 'localhost'),
        'user': os.getenv('DB_USER', 'root'),
        'password': os.getenv('DB_PASS', 'root'),
        'database': os.getenv('DB_NAME', 'userservice')
    }

    app.config['SQLALCHEMY_DATABASE_URI'] = f"mysql://{db_config['user']}:{db_config['password']}@{db_config['host']}/{db_config['database']}"
    app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
    
    db.init_app(app) 