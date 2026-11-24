import sys, os
sys.path.insert(0, os.path.abspath('.'))
import json
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.cluster import KMeans
from clusteringprocess import metode_elbow, proses_clustering

with open('scratch/indikator_2026_11.json', 'r') as f:
    data = json.load(f)

result = proses_clustering(data)
df = pd.DataFrame(data)

variabel = ['omzet', 'frekuensi', 'kontribusi', 'growth', 'jumlah_produk']
x = df[variabel]
scaler = MinMaxScaler()
x_scaled = scaler.fit_transform(x)

wcss = []
k_max = min(len(df), 5)
for k in range(1, k_max + 1):
    km = KMeans(n_clusters=k, random_state=42, n_init=10)
    km.fit(x_scaled)
    wcss.append(float(km.inertia_))

n_cluster = metode_elbow(wcss)
kmeans = KMeans(n_clusters=n_cluster, random_state=42, n_init=10)
cluster_labels = kmeans.fit_predict(x_scaled)

df['cluster_raw'] = cluster_labels
cluster_omzet_mean = df.groupby('cluster_raw')['omzet'].mean().sort_values(ascending=False)
urutan_map = {cid: idx for idx, cid in enumerate(cluster_omzet_mean.index)}
df['cluster_final'] = df['cluster_raw'].map(urutan_map) + 1

# Check cluster characteristics
print(f"=== K OPTIMAL for 2026-11: {n_cluster} ===")
print("=== WCSS ===", wcss)

for c_final, group in df.groupby('cluster_final'):
    print(f"\n--- Cluster C{c_final} (n={len(group)}) ---")
    print(f"  Omzet: mean={group['omzet'].mean():,.0f}, min={group['omzet'].min():,.0f}, max={group['omzet'].max():,.0f}")
    print(f"  Kontribusi: mean={group['kontribusi'].mean():.2f}%, min={group['kontribusi'].min():.2f}%, max={group['kontribusi'].max():.2f}%")
    print(f"  Frekuensi: mean={group['frekuensi'].mean():.2f}, min={group['frekuensi'].min():.0f}, max={group['frekuensi'].max():.0f}")
    print(f"  Jumlah Produk: mean={group['jumlah_produk'].mean():.2f}, min={group['jumlah_produk'].min():.0f}, max={group['jumlah_produk'].max():.0f}")
    print(f"  Growth: mean={group['growth'].mean():.2f}%, min={group['growth'].min():.2f}%, max={group['growth'].max():.2f}%")

print("\n=== TOP OUTLETS IN 2026-11 ===")
cols = ['id_outlet', 'nama_outlet', 'cluster_final', 'omzet', 'kontribusi', 'frekuensi', 'jumlah_produk', 'growth']
print(df[cols].sort_values(['cluster_final', 'omzet'], ascending=[True, False]).head(20).to_string())
