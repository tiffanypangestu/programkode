from fastapi import Depends, FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy.orm import Session
from schemas import RequestCluster, ResponseCluster
from clusteringprocess import proses_clustering

from database import get_db, engine, Base
import models

# Inisialisasi tabel di database
try:
    Base.metadata.create_all(bind=engine)
except Exception as e:
    print(f"Warning: Database create_all belum dapat dijalankan: {e}")

app = FastAPI(title="Analisis Performa Outlet Clustering FastAPI")

# Izinkan frontend laravel masuk
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

@app.get("/")
def root():
    return {
        "status": "online",
        "message": "FastAPI Analisis UD Medan berjalan lancar!"
    }


@app.post("/api/analisa-clustering", response_model=ResponseCluster)
def analisa_outlet(request: RequestCluster, db: Session = Depends(get_db)):
    hasil = proses_clustering(request.data)
    
    # Kalau ada error di clustering, lemparkan HTTPException  
    if "error_detail" in hasil:
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan pada proses clustering: {hasil['error_detail']}")

    # Simpan hasil clustering ke database jika memungkinkan
    try:
        data_cluster = hasil.get("data", [])
        outlet_name_map = {item.id_outlet: item.nama_outlet for item in request.data if item.id_outlet}

        for item in data_cluster:
            record_database = models.ModelHasilClustering(
                outlet_id=item["id_outlet"],
                nama_outlet=outlet_name_map.get(item["id_outlet"], f"Outlet#{item['id_outlet']}"),
                cluster_id=item["cluster"],
                kategori_performa=item["status"],
                status_prioritas=item.get("rekomendasi_promosi", "")
            )
            db.add(record_database)
        db.commit()
    except Exception as e:
        db.rollback()
        print(f"Peringatan: Gagal menyimpan hasil clustering ke DB: {e}")

    # Balikkan hasil JSON ke Laravel
    return {
        "status": "success",
        "periode": request.periode,
        "data": hasil
    }
