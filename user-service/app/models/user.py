from app.config.database import db
from sqlalchemy import Integer, String
from typing import Dict, Any
from typing_extensions import TypeGuard
from flask_sqlalchemy.model import Model

class User(db.Model):  # type: ignore[misc, valid-type]
    """User model for authentication and authorization."""
    __tablename__ = 'users'
    
    id: int = db.Column(Integer, primary_key=True)  # type: ignore[attr-defined]
    username: str = db.Column(String(80), unique=True, nullable=False)  # type: ignore[attr-defined]
    password: str = db.Column(String(120), nullable=False)  # type: ignore[attr-defined]
    role: str = db.Column(String(20), nullable=False)  # type: ignore[attr-defined]

    def __repr__(self) -> str:
        return f'<User {self.username}>'

    def to_dict(self) -> Dict[str, Any]:
        return {
            'id': self.id,
            'username': self.username,
            'role': self.role
        } 