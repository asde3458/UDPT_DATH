from flask import Flask
from flask_cors import CORS
from app.config.database import init_db
from app.routes.auth import auth
import os
from dotenv import load_dotenv

load_dotenv()

def create_app():
    app = Flask(__name__)
    CORS(app)
    
    # Initialize database
    init_db(app)
    
    # Register blueprints
    app.register_blueprint(auth, url_prefix='/api')
    
    return app 