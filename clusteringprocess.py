import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
from typing import List, Dict, Any

def metode_elbow(wcss_list: list, threshold: float = 0.15) -> int:
    """
    PEMBUKTIAN K OPTIMAL (METODE ELBOW):
    Menghitung K Optimal secara kuantitatif berdasarkan rasio penurunan WCSS.
    Ketika penambahan nilai K hanya menurunkan WCSS < 15% (threshold = 0.15),
    """
    titik_n = len(wcss_list)
    if titik_n < 2:
        return 1

    for i in range(titik_n - 1):
        wcss_sebelum = wcss_list[i]
        wcss_sesudah = wcss_list[i + 1]

        # Rumus Rasio Persentase Penurunan WCSS
        penurunan = (wcss_sebelum - wcss_sesudah) / wcss_sebelum

        # Titik Elbow Terbukti: Penurunan sudah melandai (< 15%)
        if penurunan < threshold:
            k_optimal = i + 1
            return max(2, k_optimal)

    return max(2, titik_n - 1)


def proses_clustering(ListData: List[Any]) -> Dict[str, Any]:

    try:
        # 1. Konversi Payload JSON dari laravel ke DataFrame Pandas
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
                    'id_outlet': int(getattr(item, 'id_outlet', 0)),
                    'omzet': float(getattr(item, 'omzet', 0.0)),
                    'growth': float(getattr(item, 'growth', 0.0)),
                    'kontribusi': float(getattr(item, 'kontribusi', 0.0)),
                    'frekuensi': float(getattr(item, 'frekuensi', 0.0)),
                    'jumlah_produk': int(getattr(item, 'jumlah_produk', 0))
                })

        df = pd.DataFrame(list_data)

        if df.empty:
            return {"wcss": {}, "k_optimal": 0, "silhouette_score": 0.0, "data": []}

        # 2. Ambil 5 Indikator Fitur Operasional
        variabel = ['omzet', 'frekuensi', 'growth', 'kontribusi', 'jumlah_produk']
        x = df[variabel]

        # 3. Normalisasi Data dengan MinMaxScaler (Skala 0 - 1)
        scaler = MinMaxScaler()
        x_scaled = scaler.fit_transform(x)

        # 4. Hitung WCSS (Inertia) dari K=1 hingga K_max (Sesuai Jumlah Data)
        wcss = []
        k_max = min(len(df), 5)
        for k in range(1, k_max + 1):
            kmeans_temp = KMeans(n_clusters=k, random_state=42, n_init=10)
            kmeans_temp.fit(x_scaled)
            wcss.append(float(kmeans_temp.inertia_))

        # 5. Pembuktian K Optimal via Metode Elbow Kuantitatif
        n_cluster = metode_elbow(wcss)

        # 6. Eksekusi K-Means Menggunakan K Hasil Pembuktian
        kmeans = KMeans(n_clusters=n_cluster, random_state=42, n_init=10)
        cluster_labels = kmeans.fit_predict(x_scaled)

        # 7. Evaluasi Validitas Cluster 
        silhouette = 0.0
        if len(df) > n_cluster and n_cluster > 1:
            try:
                silhouette = float(silhouette_score(x_scaled, cluster_labels))
            except Exception:
                silhouette = 0.0

        df['cluster'] = [int(c) for c in cluster_labels]

        # 8. Urutkan Cluster Berdasarkan Rata-Rata Omzet (Terbesar ke Terkecil)
        cluster_omzet_mean = df.groupby('cluster')['omzet'].mean().sort_values(ascending=False)
        urutan_map = {cluster_id: idx for idx, cluster_id in enumerate(cluster_omzet_mean.index)}

        # 9. Pemetaan Status & Rekomendasi 
        hasil_keseluruhan = []
        for _, row in df.iterrows():
            posisi_urutan = urutan_map[row['cluster']]  # 0 = Omzet Tertinggi, n_cluster-1 = Terendah

            rasio_posisi = posisi_urutan / (n_cluster - 1) if n_cluster > 1 else 0.0

            # Klasifikasi Tingkatan Status Dinamis Sesuai Posisi Rasio
            if rasio_posisi == 0.0:
                status = 'Sangat Tinggi (Ramai)'
                rekomendasi = 'Pertahankan stok produk terlaris dan berikan promo bundling eksklusif'
            elif rasio_posisi <= 0.35:
                status = 'Tinggi'
                rekomendasi = 'Tingkatkan kuota penjualan dan berikan diskon khusus bundling'
            elif rasio_posisi <= 0.70:
                status = 'Sedang'
                rekomendasi = 'Tingkatkan kunjungan BC dan optimalkan penataan produk'
            else:
                status = 'Rendah (Sepi)'
                rekomendasi = 'Evaluasi produk, cek daya beli wilayah, atau berikan event promosi khusus'

            hasil_keseluruhan.append({
                'id_outlet': int(row['id_outlet']),
                'cluster': int(row['cluster']),
                'status': status,
                'rekomendasi_promosi': rekomendasi
            })

        # 10. Format Output WCSS untuk Grafik Garis Elbow
        wcss_format = {f"k{i+1}": round(w, 4) for i, w in enumerate(wcss)}

        return {
            "wcss": wcss_format,
            "k_optimal": n_cluster,
            "silhouette_score": round(silhouette, 4),
            "data": hasil_keseluruhan
        }

    except Exception as e:
        return {
            "error_detail": str(e)
        }