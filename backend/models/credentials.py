from sqlalchemy import (
    Column, Integer, String, Enum, DateTime, ForeignKey, JSON
)
from sqlalchemy.orm import relationship
from datetime import datetime
from .base import Base


class RFIDCard(Base):
    __tablename__ = "rfid_cards"

    id = Column(Integer, primary_key=True)
    uid = Column(String(64), unique=True, nullable=False)

    user_id = Column(Integer, ForeignKey("users.id"))

    card_type = Column(
        Enum("normal", "magic_uid", "cloneable", name="rfid_card_type"),
        nullable=False,
        default="normal"
    )

    sector_data = Column(JSON)

    status = Column(
        Enum("active", "lost", "revoked", name="rfid_card_status"),
        nullable=False,
        default="active"
    )

    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)

    user = relationship("User", back_populates="rfid_cards")


class PINCode(Base):
    __tablename__ = "pin_codes"

    id = Column(Integer, primary_key=True)

    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    pin_hash = Column(String(255), nullable=False)

    status = Column(
        Enum("active", "expired", "revoked", name="pin_status"),
        nullable=False,
        default="active"
    )

    attempts = Column(Integer, nullable=False, default=0)
    last_attempt = Column(DateTime)

    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)

    user = relationship("User", back_populates="pin_codes")
