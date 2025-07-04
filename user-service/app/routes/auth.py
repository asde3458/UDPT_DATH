from flask import Blueprint, request, jsonify
from app.models.user import User
from app.config.database import db
import bcrypt

auth = Blueprint('auth', __name__)

@auth.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    user = User.query.filter_by(username=data['username']).first()
    
    if user and bcrypt.checkpw(data['password'].encode('utf-8'), user.password.encode('utf-8')):
        return jsonify(user.to_dict())
    return jsonify({'error': 'Invalid credentials'}), 401

@auth.route('/register', methods=['POST'])
def register():
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