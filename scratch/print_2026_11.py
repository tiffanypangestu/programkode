import json
import pandas as pd

with open('scratch/indikator_2026_11.json', 'r') as f:
    data = json.load(f)

df = pd.DataFrame(data)

# Let's inspect the clusters and distribution
print("All 40 outlets in 2026-11:")
cols = ['id_outlet', 'nama_outlet', 'omzet', 'kontribusi', 'frekuensi', 'jumlah_produk', 'growth']
print(df[cols].to_string())
