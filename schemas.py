from pydantic import BaseModel
from typing import List, Optional, Dict, Any

# laravel wajib mengirimkan data 5 indikator variabel + periode
class DataOutlet(BaseModel):
    id_outlet: int
    nama_outlet: Optional[str] = ""
    omzet: float = 0.0
    growth: float = 0.0
    kontribusi: float = 0.0
    frekuensi: float = 0.0
    jumlah_produk: int = 0
    petugas: Optional[str] = ""

class RequestCluster(BaseModel):
    periode: str
    data: List[DataOutlet]

# data output dari process clustering, dikirim ke Laravel
class ItemHasilClustering(BaseModel):
    id_outlet: int
    cluster: int
    status: str
    rekomendasi_promosi: str

class ResponseDetailClustering(BaseModel):
    wcss: Dict[str, float]
    k_optimal: int
    silhouette_score: float
    data: List[ItemHasilClustering]

class ResponseCluster(BaseModel):
    status: str
    periode: str
    data: ResponseDetailClustering
