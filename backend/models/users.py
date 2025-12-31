from sqlalchemy import Column, Integer, String, Enum, DateTime, Boolean
from datetime import datetime
from werkzeug.security import generate_password_hash, check_password_hash
from .base import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True)
    name = Column(String(100), nullable=False)
    email = Column(String(255), unique=True)
    phone = Column(String(50))
    password_hash = Column(String(255), nullable=False)
    role = Column(Enum('admin','operator','viewer', name='user_role'),
                  nullable=False, default='viewer')
    two_factor_enabled = Column(Boolean, default=False)
    two_factor_secret = Column(String(64))
    status = Column(Enum('active','disabled','banned', name='user_status'),
                    nullable=False, default='active')
    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)
    updated_at = Column(DateTime, default=datetime.utcnow,
                        onupdate=datetime.utcnow, nullable=False)

    def set_password(self, password):
        self.password_hash = generate_password_hash(password)

    def check_password(self, password):
        return check_password_hash(self.password_hash, password)