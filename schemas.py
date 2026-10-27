from pydantic import BaseModel
from typing import List, Optional

class DataOutlet(BaseModel):
  id_outlet: int
  omzet: float = 0.0
  growth: float = 0.0
  kontribusi: float = 0.0
  frekuensi: float = 0.0
  jumlah_produk: int = 0.0

class RequestCluster(BaseModel):
  periode: str
  data: List[DataOutlet]