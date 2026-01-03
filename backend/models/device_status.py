from sqlalchemy import (
    Column, Integer, String, Enum, DateTime
)
from datetime import datetime
from .base import Base


class DeviceStatusLog(Base):
    __tablename__ = "device_status_logs"

    id = Column(Integer, primary_key=True)

    device_type = Column(
        Enum("camera", "controller", "sensor", name="device_type"),
        nullable=False
    )

    device_id = Column(Integer, nullable=False)

    status = Column(
        Enum("online", "offline", "degraded", "error", name="device_status"),
        nullable=False
    )

    message = Column(String(255))

    timestamp = Column(DateTime, default=datetime.utcnow, nullable=False)