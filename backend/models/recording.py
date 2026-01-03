from sqlalchemy import (
    Column, Integer, String, DateTime, ForeignKey, DECIMAL
)
from sqlalchemy.orm import relationship
from datetime import datetime
from .base import Base


class Recording(Base):
    __tablename__ = "recordings"

    id = Column(Integer, primary_key=True)

    camera_id = Column(Integer, ForeignKey("cameras.id"), nullable=False)

    file_path = Column(String(255), nullable=False)

    start_time = Column(DateTime, nullable=False)
    end_time = Column(DateTime, nullable=False)

    duration_sec = Column(Integer, nullable=False)

    frame_rate = Column(DECIMAL(5, 2))
    resolution = Column(String(50))

    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)

    camera = relationship("Camera", back_populates="recordings")
    events = relationship("Event", back_populates="recording")
