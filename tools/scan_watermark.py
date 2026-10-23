import glob
import os
import sys
from PIL import Image
import numpy as np

# Ensure UTF-8 output
sys.stdout.reconfigure(encoding='utf-8')

files = glob.glob('public/uploads/products/*.jpg') + glob.glob('public/uploads/products/*.png')
watermarked = []

for f in files:
    try:
        im = Image.open(f).convert('RGB')
        w, h = im.size
        # Crop top 25%
        top = np.array(im.crop((0, 0, w, int(h * 0.25))))
        # Check for red pixels typical of 'Hải Đăng' red logo (R > 170, G < 70, B < 70)
        red_mask = (top[:, :, 0] > 170) & (top[:, :, 1] < 70) & (top[:, :, 2] < 70)
        red_count = int(np.sum(red_mask))
        # Check for blue pixels typical of 'Panasonic' blue logo (R < 60, G < 120, B > 150)
        blue_mask = (top[:, :, 0] < 60) & (top[:, :, 1] < 120) & (top[:, :, 2] > 150)
        blue_count = int(np.sum(blue_mask))
        
        if red_count > 40 and blue_count > 40:
            watermarked.append((f, red_count, blue_count))
    except Exception as e:
        pass

print(f"Total product images on disk: {len(files)}")
print(f"Images with Hải Đăng watermark detected: {len(watermarked)}")
for f, r, b in watermarked:
    print(f"  {f} [red_px={r}, blue_px={b}]")
