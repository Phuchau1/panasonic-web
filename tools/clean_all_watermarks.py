import glob
import os
import sys
from PIL import Image
import numpy as np

sys.stdout.reconfigure(encoding='utf-8')

upload_dir = os.path.abspath('public/uploads/products')
files = glob.glob(os.path.join(upload_dir, '*.jpg')) + glob.glob(os.path.join(upload_dir, '*.png'))

cleaned_count = 0

for f in files:
    try:
        im = Image.open(f).convert('RGB')
        w, h = im.size
        arr = np.array(im)
        
        # Check top 25% for Hai Dang watermark
        top_h = int(h * 0.25)
        top = arr[:top_h, :, :]
        
        # Red pixels of Hai Dang logo: R > 170, G < 70, B < 70
        red_mask = (top[:, :, 0] > 170) & (top[:, :, 1] < 70) & (top[:, :, 2] < 70)
        red_count = int(np.sum(red_mask))
        
        # Blue pixels of Panasonic logo: R < 60, G < 120, B > 150
        blue_mask = (top[:, :, 0] < 60) & (top[:, :, 1] < 120) & (top[:, :, 2] > 150)
        blue_count = int(np.sum(blue_mask))
        
        if red_count > 35 and blue_count > 35:
            # Found Hai Dang banner watermark in top area
            # Find the bottom boundary of the watermark banner
            # The banner ends around y where red and blue pixels stop, usually <= 150px
            # Let's find highest y where red/blue logo pixels occur
            red_y, red_x = np.where(red_mask)
            blue_y, blue_x = np.where(blue_mask)
            max_y = max(red_y.max(), blue_y.max()) + 15 # add 15px padding
            
            # Make sure we don't cut into product if product is near
            # For these images, banner is at y <= 160, product starts at y >= 180
            cut_y = min(max_y, int(h * 0.22))
            
            # Fill banner area with pure white (255, 255, 255)
            arr[:cut_y, :, :] = 255
            
            # Save back
            cleaned = Image.fromarray(arr)
            cleaned.save(f, quality=95)
            cleaned_count += 1
            print(f"Cleaned watermark from: {os.path.basename(f)} (banner height: {cut_y}px, red={red_count}, blue={blue_count})")
    except Exception as e:
        print(f"Error processing {f}: {e}")

print(f"\n==========================================")
print(f"Total images scanned: {len(files)}")
print(f"Total images with Hải Đăng watermark cleaned: {cleaned_count}")
