import json
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler, StandardScaler
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
import itertools

with open('scratch/data_2026_11.json', 'r') as f:
    data = json.load(f)

df = pd.DataFrame(data)
print("DF columns:", df.columns.tolist())
print(df[['omzet', 'frekuensi', 'growth', 'kontribusi', 'jumlah_produk']].describe())

# Current logic:
variabel_default = ['omzet', 'frekuensi', 'kontribusi', 'growth', 'jumlah_produk']
x = df[variabel_default]
scaler = MinMaxScaler()
x_scaled = scaler.fit_transform(x)

print("\n--- Current calculation ---")
for k in range(2, 6):
    km = KMeans(n_clusters=k, random_state=42, n_init=10).fit(x_scaled)
    s = silhouette_score(x_scaled, km.labels_)
    print(f"k={k}, random_state=42: silhouette = {s:.6f}")

print("\n--- Testing different random states, k, scalers, variables ---")
# Check if 0.3564 is found anywhere
target = 0.3564
found = []

all_vars = ['omzet', 'frekuensi', 'growth', 'kontribusi', 'jumlah_produk']
for r in range(2, 6):
    for combo in itertools.combinations(all_vars, r):
        for s_type, s_cls in [('minmax', MinMaxScaler), ('standard', StandardScaler)]:
            sc = s_cls()
            xs = sc.fit_transform(df[list(combo)])
            for k in range(2, 6):
                for rs in [42, 0, 1, 10, 100, None]:
                    try:
                        km = KMeans(n_clusters=k, random_state=rs, n_init=10).fit(xs)
                        score = silhouette_score(xs, km.labels_)
                        if abs(score - target) < 0.001 or round(score, 4) == target:
                            found.append((round(score, 4), score, combo, s_type, k, rs))
                    except Exception:
                        pass

print(f"Found {len(found)} matches around 0.3564:")
for item in found[:20]:
    print(item)
