from fastapi import FastAPI
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score

app = FastAPI()

def metode_elbow(wcss_list: list)->int :
  titik_n = len(wcss_list)
  if titik_n < 3:
    return titik_n

  # koordinat
  koordinat = np.array(range(1, titik_n + 1))
  values = np.array(wcss_list)

  # titik awal (k = 1) titik akhir (k maksimal)
  p1 = np.array([koordinat[0], values[0]])
  p2 = np.array[koordinat[-1], values[-1]]

  # jarak tegak lurus setiap titik koordinat WCSS ke p1 dan p2
  jarak = []
  for i in range(titik_n):
    p3 = np.array([koordinat[i], values[i]])
    # rumus jarak
    
    jarak = np.abs(np.cross(p2 - p1, p1 - p3)) / np.linalg.norm(p2 - p1)
    jarak.append(jarak)

  # titik elbow: nilai k dengan jarak terjauh dari garis lurus
  titik_k = int(koordinat[np.argmax(jarak)])

  # k optimal harus 2, < 2 tidak cukup untuk pembentukan cluster
  return max(2, titik_k) if titik_n >= 2 else 1

def proses_clustering(ListData):
  # kolom untuk fitur Clustering
  try:
    list_data = []
    for item in ListData:
      if hasattr(item, 'model_dump'):
        list_data.append(item.model_dump())
      elif hasattr(item, 'dict'):
        list_data.append(item.dict())
      elif isinstance(item, dict):
        list_data.append(item)
      else:
        list_data.append({
          'id_outlet': getattr(item, 'id_outlet', getattr(item, 'outlet_id', 0)),
          'frekuensi': float(getattr(item, 'frekuensi', 0.0)),
          'omzet': float(getattr(item, 'omzet', 0.0)),
          'growth': float(getattr(item, 'growth', 0.0)),
          'kontribusi': float(getattr(item, 'kontribusi', 0.0)),
          'jumlah_produk': float(getattr(item, 'jumlah_produk', 0.0))
        })
    df = pd.DataFrame(list_data)

    if df.empty:
      return {"wcss": [], "k_optimal": 0, "silhouette_score": 0.0, "data": []}

    # 5 variabel indikator
    variabel = ['omzet', 'frekuensi', 'growth', 'kontribusi', 'jumlah_produk']
    x = df[variabel]

    # normalisasi min max scaler
    scaler = MinMaxScaler()
    x_scaled = scaler.fit_transform(x)

    #hitung WCSS (elbow method)
    within_cluster_sum_squares = []
    k_max = min(len(df), 5)
    for k in range(1, k_max + 1):
      k_means_temp = KMeans(n_clusters=k, random_state=42, n_init=10)
      k_means_temp.fit(x_scaled)
      within_cluster_sum_squares.append(float(k_means_temp.inertia_))

    # pembuktian k optimal
    n_cluster = metode_elbow(within_cluster_sum_squares)

    # eksekusi K-Means dengan K yang terbukti
    kmeans = KMeans(n_clusters=n_cluster, random_state=42, n_init=10)
    cluster = kmeans.fit_predict(x_scaled)

    #silhouette score
    silhouette = 0.0
    if len(df) > n_cluster and n_cluster > 1:
      try:
        silhouette = float(silhouette_score(x_scaled, cluster))
      except Exception:
        silhouette = 0.0

    df['cluster'] = [int(c) for c in cluster]

    # pemetaan status performa dan rekomendasi sesuai urutan rata-rata omzet
    cluster_rata_rata_omzet = df.groupby('cluster')['omzet'].mean().sort_values(ascending=False)
    urutan_map = {cluster_id: i for i, cluster_id in enumerate(cluster_rata_rata_omzet.index)}

    hasil_keseluruhan = []
    for _, row in df.itterows():
      urutan = urutan_map[row['cluster']]

      if urutan == 0:
        status = 'Ramai (Tinggi)'
        rekomendasi = 'Pertahankan stok produk terlaris dan berikan promo bundling'
      elif urutan == 1 and n_cluster > 2:
        status = 'Sedang'
        rekomendasi = 'Tingkatkan kunjungan BC dan berikan diskon khusus'
      else:
        status = 'Sepi (Rendah)'
        rekomendasi = 'Evaluasi produk atau berikan event promosi khusus'

      hasil_keseluruhan.append({
        'id_outlet': int(row['id_outlet']),
        'cluster': int(row['cluster']),
        'status' : status,
        'rekomendasi_promosi': rekomendasi
      })

      # ubah WCSS Format menjadi k1: ... untuk ditampilkan di grafik
      wcss_format = {f"k{i+1}": round(w, 4) for i, w in enumerate(within_cluster_sum_squares)}

    return {
      "wcss": wcss_format,
      "k_optimal": n_cluster,
      "silhouette_score": round(silhouette, 4),
      "data": hasil_keseluruhan
    }
  
  except Exception as e:
    return {
      "wcss": {},
      "k_optimal": 0,
      "silhouette_score": 0.0,
      "data": [],
      "error_detail": str(e)
    }
