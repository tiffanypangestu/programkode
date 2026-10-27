from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from schemas import RequestCluster
from clusteringprocess import proses_clustering

app = FastAPI(title="Analisis Perfoma Outlet Clustering FastAPI")

#izinkan metode masuk
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

@app.post("/api/analisa-clustering")
def analisa_outlet(request: RequestCluster):
  hasil = proses_clustering(request.data)
  if "error_detail" in hasil:
    raise HTTPException(status_code=500, detail=f"Terjadi kesalahan pada proses clustering: {hasil['error_detail']}")
  return {
    "status": "success",
    "periode": request.periode,
    "data": hasil
  }

@app.get("/")
def root():
  return {
    "message" : "FastAPI Analisis UD Medan berjalan lancar!"
  }