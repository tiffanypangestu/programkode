from sqlalchemy import Column, Integer, String, DateTime, Float
from datetime import datetime
from database import Base

class ModelHasilClustering(Base):
    __tablename__ = "hasil_clustering" #MySQL

    id = Column(Integer, primary_key=True, index=True)
    outlet_id = Column(Integer, nullable=False, index=True)
    nama_outlet = Column(String(255), nullable=False)
    cluster_id = Column(Integer)
    kategori_performa = Column(String(100))
    status_prioritas = Column(String(100))
    created_at = Column(DateTime, default=datetime.utcnow)