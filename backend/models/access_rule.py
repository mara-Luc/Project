from sqlalchemy import (
    Column, Integer, Time, Date, Enum, DateTime, ForeignKey, JSON
)
from sqlalchemy.orm import relationship
from datetime import datetime
from .base import Base


class AccessRule(Base):
    __tablename__ = "access_rules"

    id = Column(Integer, primary_key=True)

    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    camera_group_id = Column(Integer, ForeignKey("camera_groups.id"))

    allowed_from = Column(Time, nullable=False)
    allowed_to = Column(Time, nullable=False)

    days_of_week = Column(JSON, nullable=False)

    valid_from = Column(Date, nullable=False)
    valid_to = Column(Date)

    status = Column(
        Enum("active", "disabled", name="access_rule_status"),
        nullable=False,
        default="active"
    )

    created_at = Column(DateTime, default=datetime.utcnow, nullable=False)

    user = relationship("User", back_populates="access_rules")
    camera_group = relationship("CameraGroup")