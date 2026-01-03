from sqlalchemy import (
    Column, Integer, String, Enum, DateTime, ForeignKey
)
from sqlalchemy.orm import relationship
from datetime import datetime
from .base import Base


class CameraGroup(Base):
    __tablename__ = "camera_groups"

    id = Column(Integer, primary_key=True)
    name = Column(String(100), nullable=False, unique=True)
    description = Column(String(255))

    cameras = relationship(
        "Camera",
        back_populates="group",
        cascade="all, delete-orphan"
    )


class Camera(Base):
    __tablename__ = "cameras"

    id = Column(Integer, primary_key=True)
    group_id = Column(Integer, ForeignKey("camera_groups.id"))

    name = Column(String(100), nullable=False)
    location = Column(String(255))
    rtsp_url = Column(String(255), nullable=False)
    ip_address = Column(String(45))
    model = Column(String(100))

    status = Column(
        Enum("online", "offline", "degraded", name="camera_status"),
        nullable=False,
        default="offline"
    )

    last_heartbeat = Column(DateTime)
    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)

    group = relationship("CameraGroup", back_populates="cameras")
    recordings = relationship("Recording", back_populates="camera")
    events = relationship("Event", back_populates="camera")
