from flask import Flask, request, jsonify, Response
from app.config.database import db, init_db
from app.models.user import User
import bcrypt
import os
from dotenv import load_dotenv
from typing import Dict, Any, Union, Tuple

# Load environment variables
load_dotenv()

app = Flask(__name__)

# Initialize database
init_db(app)

@app.route('/login', methods=['POST'])
def login() -> Tuple[Response, int]:
    data = request.get_json()
    user = User.query.filter_by(username=data['username']).first()
    
    if user and bcrypt.checkpw(data['password'].encode('utf-8'), user.password.encode('utf-8')):
        return jsonify({
            'id': user.id,
            'username': user.username,
            'role': user.role
        }), 200
    return jsonify({'error': 'Invalid credentials'}), 401

@app.route('/register', methods=['POST'])
def register() -> Tuple[Response, int]:
    data = request.get_json()
    
    if User.query.filter_by(username=data['username']).first():
        return jsonify({'error': 'Username already exists'}), 400
    
    hashed_password = bcrypt.hashpw(data['password'].encode('utf-8'), bcrypt.gensalt())
    
    new_user = User(
        username=data['username'],
        password=hashed_password.decode('utf-8'),
        role=data['role']
    )
    
    db.session.add(new_user)
    db.session.commit()
    
    return jsonify({'message': 'User created successfully'}), 201

if __name__ == '__main__':
    port = int(os.getenv('PORT', 5000))
    with app.app_context():
        db.create_all()
    app.run(host='0.0.0.0', port=port) 