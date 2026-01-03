from sqlalchemy import (
    Column, Integer, Enum, DateTime, ForeignKey, Boolean, DECIMAL, JSON
)
from sqlalchemy.orm import relationship
from datetime import datetime
from .base import Base


class Event(Base):
    __tablename__ = "events"

    id = Column(Integer, primary_key=True)

    timestamp = Column(DateTime, default=datetime.utcnow, nullable=False)

    event_type = Column(
        Enum(
            "rfid",
            "pin",
            "camera_motion",
            "system",
            "manual_override",
            name="event_type"
        ),
        nullable=False
    )

    user_id = Column(Integer, ForeignKey("users.id"))
    camera_id = Column(Integer, ForeignKey("cameras.id"))
    recording_id = Column(Integer, ForeignKey("recordings.id"))

    credential_id = Column(Integer)  # RFID or PIN, resolved in app logic

    success = Column(Boolean, default=False, nullable=False)
    confidence = Column(DECIMAL(5, 2))
    metadata = Column(JSON)

    user = relationship("User", back_populates="events")
    camera = relationship("Camera", back_populates="events")
    recording = relationship("Recording", back_populates="events")
