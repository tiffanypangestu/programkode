import sys, os
sys.path.insert(0, os.path.abspath('.'))
import json
import pandas as pd
from clusteringprocess import proses_clustering

for p in ['2026-11', '2025-07']:
    with open(f'scratch/indikator_{p}_verified.json', 'r') as f:
        data = json.load(f)

    res = proses_clustering(data)
    print(f"\n==================== PERIOD: {p} ====================")
    print(f"k_optimal: {res['k_optimal']}")
    print(f"silhouette_score: {res['silhouette_score']}")
    print(f"wcss: {res['wcss']}")

    df_data = pd.DataFrame(data)
    df_clust = pd.DataFrame(res['data'])
    merged = pd.merge(df_data, df_clust, on='id_outlet')

    print("\n--- CLUSTER STATS ---")
    for c, grp in merged.groupby('cluster'):
        print(f"\nCluster C{c} - Status: '{grp['status'].iloc[0]}' (n={len(grp)}):")
        print(f"  Omzet: mean={grp['omzet'].mean():,.0f} (min={grp['omzet'].min():,.0f}, max={grp['omzet'].max():,.0f})")
        print(f"  Kontribusi: mean={grp['kontribusi'].mean():.2f}% (min={grp['kontribusi'].min():.2f}%, max={grp['kontribusi'].max():.2f}%)")
        print(f"  Frekuensi: mean={grp['frekuensi'].mean():.2f} (min={grp['frekuensi'].min():.0f}, max={grp['frekuensi'].max():.0f})")
        print(f"  Jumlah Produk: mean={grp['jumlah_produk'].mean():.2f} (min={grp['jumlah_produk'].min():.0f}, max={grp['jumlah_produk'].max():.0f})")
        print(f"  Growth: mean={grp['growth'].mean():.2f}% (min={grp['growth'].min():.2f}%, max={grp['growth'].max():.2f}%)")

    print("\n--- FIRST 8 OUTLETS AS SHOWN IN WEB TABLE ---")
    cols = ['id_outlet', 'nama_outlet', 'omzet', 'growth', 'frekuensi', 'jumlah_produk', 'kontribusi', 'cluster', 'status']
    print(merged[cols].head(8).to_string())
