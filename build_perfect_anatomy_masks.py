import numpy as np
from PIL import Image, ImageFilter
import os

im = Image.open('public/images/workstation_skeleton.jpg').convert('RGBA')
w, h = im.size
arr = np.array(im, dtype=np.float32)
rgb = arr[:, :, :3]
lum = 0.299 * rgb[:, :, 0] + 0.587 * rgb[:, :, 1] + 0.114 * rgb[:, :, 2]

yy, xx = np.mgrid[0:h, 0:w]

region_masks = {}

# 1. Head / Skull (Cranium & Face down to chin, stops before neck/C-spine)
region_masks['head'] = (yy <= 218) & (xx >= 280) & (xx <= 498) & (lum > 8)

# 2. Spine (C-spine in neck from Y=215, down through T-spine, L-spine, and sacrum Y=685)
region_masks['spine'] = (yy >= 215) & (yy <= 685) & (xx >= 365) & (xx <= 413) & (lum > 8)

# 3. Thorax (Clavicles at Y=265 down to 12th ribs at Y=532. DOES NOT include neck Y<265, and excludes central spine)
# In photo media_1792808055308.jpg, Thorax is the ribcage & clavicles
region_masks['thorax'] = (yy >= 265) & (yy <= 532) & (xx >= 255) & (xx <= 523) & (lum > 8)

# 4. Pelvis & Abdomen (Iliac crests, sacral base, ischium, pubis)
region_masks['pelvis'] = (yy > 535) & (yy <= 720) & (xx >= 246) & (xx <= 532) & (lum > 8)

# 5. Right Arm (Patient right / Viewer left: shoulder, humerus, elbow, forearm, wrist, hand)
# Strictly outside the torso: xx < 255 (top) and xx < 246 (pelvis level)
region_masks['right_arm'] = (yy >= 260) & (yy <= 830) & (lum > 8) & (
    ((yy <= 535) & (xx < 255)) | 
    ((yy > 535) & (xx < 246))
)

# 6. Left Arm (Patient left / Viewer right: shoulder, humerus, elbow, forearm, wrist, hand)
# Strictly outside the torso: xx > 523 (top) and xx > 532 (pelvis level)
region_masks['left_arm'] = (yy >= 260) & (yy <= 830) & (lum > 8) & (
    ((yy <= 535) & (xx > 523)) | 
    ((yy > 535) & (xx > 532))
)

# 7. Right Leg (Patient right / Viewer left: femur, knee, tibia, fibula, ankle, foot)
# STRICTLY inside xx >= 238 and xx <= 389 so hands (xx < 200) NEVER glow!
region_masks['right_leg'] = (yy > 720) & (xx >= 238) & (xx <= 389) & (lum > 8)

# 8. Left Leg (Patient left / Viewer right: femur, knee, tibia, fibula, ankle, foot)
# STRICTLY inside xx > 389 and xx <= 542 so hands (xx > 580) NEVER glow!
region_masks['left_leg'] = (yy > 720) & (xx > 389) & (xx <= 542) & (lum > 8)

os.makedirs('public/images/parts', exist_ok=True)

for key, mask in region_masks.items():
    part_rgba = np.zeros((h, w, 4), dtype=np.uint8)
    intensity = np.where(mask, lum / 255.0, 0.0)
    
    # Core electric neon cyan (#00f0ff) with white highlights
    r = np.clip(intensity * 110 + (intensity ** 2) * 145, 0, 255).astype(np.uint8)
    g = np.clip(intensity * 235 + (intensity ** 2) * 20, 0, 255).astype(np.uint8)
    b = np.full((h, w), 255, dtype=np.uint8)
    alpha = np.clip(intensity * 255.0 * 1.35, 0, 255).astype(np.uint8)
    
    part_rgba[:, :, 0] = r
    part_rgba[:, :, 1] = g
    part_rgba[:, :, 2] = b
    part_rgba[:, :, 3] = alpha
    
    part_img = Image.fromarray(part_rgba, 'RGBA')
    
    # Atmospheric cyan halo glow (radius=6 for clean organ adherence)
    glow_alpha = part_img.split()[3].filter(ImageFilter.GaussianBlur(radius=6))
    glow_rgba = np.zeros((h, w, 4), dtype=np.uint8)
    glow_rgba[:, :, 0] = 0
    glow_rgba[:, :, 1] = 230
    glow_rgba[:, :, 2] = 255
    glow_rgba[:, :, 3] = np.array(glow_alpha)
    glow_img = Image.fromarray(glow_rgba, 'RGBA')
    
    final_img = Image.alpha_composite(glow_img, part_img)
    out_path = f'public/images/parts/skeleton_part_{key}.png'
    final_img.save(out_path, optimize=True)

    # Check bounding box of glow image
    nonzeros = np.where(np.array(final_img)[:, :, 3] > 10)
    y_min, y_max = nonzeros[0].min(), nonzeros[0].max()
    x_min, x_max = nonzeros[1].min(), nonzeros[1].max()
    print(f'Part [{key}]: Y=[{y_min}, {y_max}], X=[{x_min}, {x_max}]')

print('All 8 perfect anatomical masks generated!')
