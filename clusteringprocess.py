import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
from typing import List, Dict, Any

def metode_elbow(wcss_list: list) -> int:
    """
    PEMBUKTIAN K OPTIMAL (METODE ELBOW):
    Menghitung K Optimal secara adaptif berdasarkan rasio penurunan WCSS (Inertia).
    Titik siku (elbow point) adalah titik di mana penurunan WCSS melambat secara drastis
    (rasio penurunan drop_{k-1} terhadap drop_k bernilai maksimal).
    Jika pola data optimal pada 3 cluster maka menghasilkan 3, jika 4 maka 4.
    """
    titik_n = len(wcss_list)
    if titik_n < 3:
        return max(2, titik_n)

    drops = []
    for i in range(titik_n - 1):
        prev_w = float(wcss_list[i])
        next_w = float(wcss_list[i + 1])
        drops.append(max(0.0001, prev_w - next_w))

    ratios = []
    for i in range(len(drops) - 1):
        prev_drop = drops[i]
        next_drop = drops[i + 1]
        r = prev_drop / next_drop if next_drop > 0 else 1.0
        ratios.append(r)

    if not ratios:
        return 3

    best_idx = int(np.argmax(ratios))
    k_optimal = best_idx + 2  # index 0 -> k=2, index 1 -> k=3, index 2 -> k=4

    return max(2, min(k_optimal, titik_n - 1))


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

        # 9. Pemetaan Status & Rekomendasi Sesuai Jumlah Cluster (k_optimal Metode Elbow)
        hasil_keseluruhan = []
        for _, row in df.iterrows():
            posisi_urutan = urutan_map[row['cluster']]  # 0 = Omzet Tertinggi, n_cluster-1 = Terendah

            if n_cluster == 2:
                if posisi_urutan == 0:
                    status = 'Tinggi (Ramai)'
                    rekomendasi = 'Pertahankan stok produk terlaris dan loyalitas outlet unggulan'
                else:
                    status = 'Rendah (Sepi)'
                    rekomendasi = 'Tingkatkan promosi, evaluasi produk dan lakukan kunjungan berkala'
            elif n_cluster == 3:
                if posisi_urutan == 0:
                    status = 'Tinggi (Ramai)'
                    rekomendasi = 'Pertahankan stok produk terlaris dan berikan promo bundling eksklusif'
                elif posisi_urutan == 1:
                    status = 'Sedang'
                    rekomendasi = 'Tingkatkan kunjungan BC dan optimalkan penataan produk'
                else:
                    status = 'Rendah (Sepi)'
                    rekomendasi = 'Evaluasi produk, cek daya beli wilayah, atau berikan event promosi khusus'
            elif n_cluster == 4:
                if posisi_urutan == 0:
                    status = 'Sangat Tinggi (Ramai)'
                    rekomendasi = 'Pertahankan stok produk terlaris dan berikan promo bundling eksklusif'
                elif posisi_urutan == 1:
                    status = 'Tinggi'
                    rekomendasi = 'Tingkatkan kuota penjualan dan berikan diskon khusus bundling'
                elif posisi_urutan == 2:
                    status = 'Sedang'
                    rekomendasi = 'Tingkatkan kunjungan BC dan optimalkan penataan produk'
                else:
                    status = 'Rendah (Sepi)'
                    rekomendasi = 'Evaluasi produk, cek daya beli wilayah, atau berikan event promosi khusus'
            else:
                if posisi_urutan == 0:
                    status = 'Sangat Tinggi (Ramai)'
                    rekomendasi = 'Pertahankan stok produk terlaris dan berikan promo bundling eksklusif'
                elif posisi_urutan == 1:
                    status = 'Tinggi'
                    rekomendasi = 'Tingkatkan kuota penjualan dan berikan diskon khusus bundling'
                elif posisi_urutan == 2:
                    status = 'Sedang'
                    rekomendasi = 'Tingkatkan kunjungan BC dan optimalkan penataan produk'
                elif posisi_urutan == 3:
                    status = 'Rendah'
                    rekomendasi = 'Evaluasi minat pasar dan tingkatkan program diskon promosi'
                else:
                    status = 'Sangat Rendah (Sepi)'
                    rekomendasi = 'Evaluasi menyeluruh penjualan outlet dan minat beli wilayah'

            hasil_keseluruhan.append({
                'id_outlet': int(row['id_outlet']),
                'cluster': int(posisi_urutan + 1),
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
