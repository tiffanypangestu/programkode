import sys
import os
sys.path.insert(0, os.path.abspath('.'))
import json
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
from clusteringprocess import metode_elbow, proses_clustering

with open('scratch/indikator_2025_07.json', 'r') as f:
    data = json.load(f)

result = proses_clustering(data)

df = pd.DataFrame(data)
variabel = ['omzet', 'frekuensi', 'kontribusi', 'growth', 'jumlah_produk']
x = df[variabel]

scaler = MinMaxScaler()
x_scaled = scaler.fit_transform(x)
scaled_df = pd.DataFrame(x_scaled, columns=[v + '_scaled' for v in variabel])
df_combined = pd.concat([df, scaled_df], axis=1)

# kmeans
wcss = []
k_max = min(len(df), 5)
for k in range(1, k_max + 1):
    km = KMeans(n_clusters=k, random_state=42, n_init=10)
    km.fit(x_scaled)
    wcss.append(float(km.inertia_))

n_cluster = metode_elbow(wcss)
kmeans = KMeans(n_clusters=n_cluster, random_state=42, n_init=10)
cluster_labels = kmeans.fit_predict(x_scaled)

df_combined['cluster_raw'] = cluster_labels
cluster_omzet_mean = df_combined.groupby('cluster_raw')['omzet'].mean().sort_values(ascending=False)
urutan_map = {cid: idx for idx, cid in enumerate(cluster_omzet_mean.index)}
df_combined['cluster_final'] = df_combined['cluster_raw'].map(urutan_map) + 1

# Let's inspect centroids (raw scaled and inverse transform)
centroids_scaled = kmeans.cluster_centers_
centroids_unscaled = scaler.inverse_transform(centroids_scaled)
centroids_df = pd.DataFrame(centroids_unscaled, columns=variabel)
centroids_df['cluster_raw'] = range(n_cluster)
centroids_df['cluster_final'] = centroids_df['cluster_raw'].map(urutan_map) + 1
centroids_df = centroids_df.sort_values('cluster_final')

print("=== K OPTIMAL ===", n_cluster)
print("=== WCSS ===", wcss)
print("\n=== CENTROIDS (UNSCALED) ===")
print(centroids_df[['cluster_final', 'cluster_raw'] + variabel])

print("\n=== CENTROIDS (SCALED 0-1) ===")
centroids_scaled_df = pd.DataFrame(centroids_scaled, columns=variabel)
centroids_scaled_df['cluster_raw'] = range(n_cluster)
centroids_scaled_df['cluster_final'] = centroids_scaled_df['cluster_raw'].map(urutan_map) + 1
print(centroids_scaled_df.sort_values('cluster_final')[['cluster_final'] + variabel])

# Target outlets: Toko Cantik and Olivia Salon Supplier, plus others
targets = df_combined[df_combined['nama_outlet'].str.contains('Cantik|Olivia|Central|Christine', case=False, na=False)]
print("\n=== TARGET OUTLETS RAW VALUES ===")
cols_show = ['id_outlet', 'nama_outlet', 'cluster_final', 'omzet', 'growth', 'frekuensi', 'jumlah_produk', 'kontribusi']
print(targets[cols_show].to_string())

print("\n=== TARGET OUTLETS SCALED (0-1) VALUES ===")
scaled_cols_show = ['id_outlet', 'nama_outlet', 'cluster_final'] + [v + '_scaled' for v in variabel]
print(targets[scaled_cols_show].to_string())

# Distance to each centroid
print("\n=== DISTANCES TO CLUSTER CENTROIDS ===")
for idx, row in targets.iterrows():
    pt = x_scaled[idx]
    dists = {}
    for c_idx in range(n_cluster):
        c_final = urutan_map[c_idx] + 1
        dist = np.linalg.norm(pt - centroids_scaled[c_idx])
        dists[f"C{c_final}"] = round(dist, 4)
    print(f"{row['nama_outlet']} -> Assigned: C{row['cluster_final']} | Distances: {dists}")

# Summary of all clusters
print("\n=== CLUSTER SUMMARY ===")
for c_final, group in df_combined.groupby('cluster_final'):
    print(f"\nCluster C{c_final} (n={len(group)}):")
    print(f"  Omzet mean: {group['omzet'].mean():,.0f} (min: {group['omzet'].min():,.0f}, max: {group['omzet'].max():,.0f})")
    print(f"  Growth mean: {group['growth'].mean():.2f}% (min: {group['growth'].min():.2f}%, max: {group['growth'].max():.2f}%)")
    print(f"  Frekuensi mean: {group['frekuensi'].mean():.2f} (min: {group['frekuensi'].min():.0f}, max: {group['frekuensi'].max():.0f})")
    print(f"  Jumlah Produk mean: {group['jumlah_produk'].mean():.2f} (min: {group['jumlah_produk'].min():.0f}, max: {group['jumlah_produk'].max():.0f})")
    print(f"  Kontribusi mean: {group['kontribusi'].mean():.2f}%")
    print("  Outlets:", group['nama_outlet'].tolist())
