import sys, os
sys.path.insert(0, os.path.abspath('.'))
import json
import pandas as pd
import numpy as np
from clusteringprocess import metode_elbow, proses_clustering
from sklearn.metrics import silhouette_score

with open('scratch/indikator_2026_11.json', 'r') as f:
    raw_data = json.load(f)

# Let's inspect the outlet list
outlet_names = [d['nama_outlet'] for d in raw_data]
outlet_ids = [d['id_outlet'] for d in raw_data]

sim_data = []
c1_ids = [92, 94, 95, 97, 100, 101, 103, 104, 112, 122]
c2_ids = [98, 102, 110, 113, 116, 117, 120, 125, 127, 130]
c3_ids = [105, 106, 107, 108, 109, 114, 119, 121, 128, 129]
c4_ids = [93, 96, 99, 111, 115, 118, 123, 124, 126, 131]

id_to_name = {d['id_outlet']: d['nama_outlet'] for d in raw_data}

# Assign specific realistic values:
c1_vals = [
    (22500000, 8, 510, 18.5),
    (21800000, 8, 495, 15.2),
    (21200000, 8, 480, 22.1),
    (20700000, 7, 465, 12.8),
    (20400000, 7, 455, 16.4),
    (19900000, 7, 440, 14.1),
    (19600000, 7, 435, 19.3),
    (19200000, 8, 425, 11.5),
    (18900000, 7, 415, 25.0), # e.g. Toko Cantik
    (18600000, 7, 410, 10.5), # e.g. Olivia
]

c2_vals = [
    (17200000, 6, 385, 32.5),
    (16800000, 6, 375, 28.4),
    (16400000, 5, 360, 41.2),
    (16000000, 6, 355, 25.8),
    (15700000, 5, 345, 38.0),
    (15400000, 6, 335, 22.4),
    (15100000, 5, 330, 35.1),
    (14800000, 5, 320, 29.6),
    (14500000, 6, 315, 24.3),
    (14200000, 5, 310, 31.0),
]

c3_vals = [
    (12900000, 4, 275, 12.5),
    (12500000, 4, 265, 8.2),
    (12100000, 3, 255, 14.0),
    (11700000, 4, 245, 6.5),
    (11400000, 3, 240, 10.8),
    (11000000, 4, 230, 5.2),
    (10700000, 3, 225, 11.4),
    (10400000, 3, 215, 7.8),
    (10100000, 4, 210, 4.3),
    (9800000, 3, 205, 9.1),
]

c4_vals = [
    (7900000, 2, 175, -22.5),
    (7500000, 2, 165, -31.0),
    (7100000, 2, 155, -18.4),
    (6700000, 1, 145, -42.1),
    (6400000, 2, 135, -26.5),
    (6000000, 1, 125, -38.2),
    (5700000, 2, 115, -21.0),
    (5400000, 1, 105, -45.5),
    (5000000, 1, 95, -33.8),
    (4600000, 1, 85, -29.4),
]

total_omzet = sum(v[0] for v in c1_vals + c2_vals + c3_vals + c4_vals)

for oid, vals in zip(c1_ids, c1_vals):
    sim_data.append({
        'id_outlet': oid,
        'nama_outlet': id_to_name[oid],
        'omzet': float(vals[0]),
        'frekuensi': float(vals[1]),
        'jumlah_produk': int(vals[2]),
        'growth': float(vals[3]),
        'kontribusi': round((vals[0] / total_omzet) * 100, 2)
    })

for oid, vals in zip(c2_ids, c2_vals):
    sim_data.append({
        'id_outlet': oid,
        'nama_outlet': id_to_name[oid],
        'omzet': float(vals[0]),
        'frekuensi': float(vals[1]),
        'jumlah_produk': int(vals[2]),
        'growth': float(vals[3]),
        'kontribusi': round((vals[0] / total_omzet) * 100, 2)
    })

for oid, vals in zip(c3_ids, c3_vals):
    sim_data.append({
        'id_outlet': oid,
        'nama_outlet': id_to_name[oid],
        'omzet': float(vals[0]),
        'frekuensi': float(vals[1]),
        'jumlah_produk': int(vals[2]),
        'growth': float(vals[3]),
        'kontribusi': round((vals[0] / total_omzet) * 100, 2)
    })

for oid, vals in zip(c4_ids, c4_vals):
    sim_data.append({
        'id_outlet': oid,
        'nama_outlet': id_to_name[oid],
        'omzet': float(vals[0]),
        'frekuensi': float(vals[1]),
        'jumlah_produk': int(vals[2]),
        'growth': float(vals[3]),
        'kontribusi': round((vals[0] / total_omzet) * 100, 2)
    })

# Now test proses_clustering
res = proses_clustering(sim_data)

print(f"k_optimal: {res['k_optimal']}")
print(f"silhouette_score: {res['silhouette_score']}")
print(f"wcss: {res['wcss']}")

df_res = pd.DataFrame(sim_data)
df_clust = pd.DataFrame(res['data'])
merged = pd.merge(df_res, df_clust, on='id_outlet')

print("\n=== CLUSTER SUMMARY IN SIMULATION ===")
for c, grp in merged.groupby('cluster'):
    print(f"\nCluster C{c} ({grp['status'].iloc[0]}) - n={len(grp)}:")
    print(f"  Omzet: {grp['omzet'].mean():,.0f} (min: {grp['omzet'].min():,.0f}, max: {grp['omzet'].max():,.0f})")
    print(f"  Kontribusi: {grp['kontribusi'].mean():.2f}% (min: {grp['kontribusi'].min():.2f}%, max: {grp['kontribusi'].max():.2f}%)")
    print(f"  Frekuensi: {grp['frekuensi'].mean():.2f} (min: {grp['frekuensi'].min():.0f}, max: {grp['frekuensi'].max():.0f})")
    print(f"  Jumlah Produk: {grp['jumlah_produk'].mean():.2f} (min: {grp['jumlah_produk'].min():.0f}, max: {grp['jumlah_produk'].max():.0f})")
    print(f"  Growth: {grp['growth'].mean():.2f}% (min: {grp['growth'].min():.2f}%, max: {grp['growth'].max():.2f}%)")
    print(f"  Outlets: {grp['nama_outlet'].tolist()}")
