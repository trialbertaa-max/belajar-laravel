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

# 1. Head / Skull (Cranium & Face down to chin, stops at Y=214 before neck/C-spine)
region_masks['head'] = (yy <= 214) & (xx >= 295) & (xx <= 480) & (lum > 8)

# 2. Spine (C-spine in neck from Y=215, down through T-spine, L-spine, and sacrum Y=685)
# In photo media_1792813850665.jpg, Spine is the midline vertebral column
region_masks['spine'] = (yy >= 215) & (yy <= 685) & (xx >= 356) & (xx <= 422) & (lum > 8)

# 3. Thorax (Clavicles & Ribcage)
# Strictly matches photo media_1792813841546.jpg:
# - Clavicles at Y=268..285 across X=[265, 510]
# - Ribcages at Y=285..530: EXCLUDES central spine (xx between 356 and 422)
# - STRICTLY excludes neck (yy < 268)
# - STRICTLY excludes lumbar spine (yy > 530)
# - STRICTLY excludes arms (xx < 250 or xx > 525)
thorax_clavicles = (yy >= 268) & (yy <= 285) & (xx >= 265) & (xx <= 510)
thorax_ribs_left = (yy > 285) & (yy <= 530) & (xx >= 250) & (xx < 356)
thorax_ribs_right = (yy > 285) & (yy <= 530) & (xx > 422) & (xx <= 525)
region_masks['thorax'] = (thorax_clavicles | thorax_ribs_left | thorax_ribs_right) & (lum > 8)

# 4. Pelvis & Abdomen (Iliac crests, pelvic girdle down to Y=720, lumbar spine excluded)
region_masks['pelvis'] = (yy >= 565) & (yy <= 720) & (xx >= 246) & (xx <= 532) & (lum > 8)

# 5. Right Arm (Patient right / Viewer left: shoulder, humerus, forearm, hand)
region_masks['right_arm'] = (yy >= 260) & (yy <= 830) & (lum > 8) & (
    ((yy <= 535) & (xx < 250)) | 
    ((yy > 535) & (xx < 246))
)

# 6. Left Arm (Patient left / Viewer right: shoulder, humerus, forearm, hand)
region_masks['left_arm'] = (yy >= 260) & (yy <= 830) & (lum > 8) & (
    ((yy <= 535) & (xx > 525)) | 
    ((yy > 535) & (xx > 532))
)

# 7. Right Leg (femur, knee, tibia, fibula, ankle, foot)
# STRICTLY inside xx in [238, 388] so hand (xx < 210, yy < 830) is 100% excluded!
region_masks['right_leg'] = (yy > 720) & (xx >= 238) & (xx <= 388) & (lum > 8)

# 8. Left Leg (femur, knee, tibia, fibula, ankle, foot)
# STRICTLY inside xx in (388, 542] so hand (xx > 560, yy < 830) is 100% excluded!
region_masks['left_leg'] = (yy > 720) & (xx > 388) & (xx <= 542) & (lum > 8)

os.makedirs('public/images/parts', exist_ok=True)
scratch_dir = r'C:\Users\Guest User\.gemini\antigravity-ide\brain\d5fcbe69-63fa-47ba-8baa-936593c181a2\scratch'
os.makedirs(scratch_dir, exist_ok=True)

for key, mask in region_masks.items():
    part_rgba = np.zeros((h, w, 4), dtype=np.uint8)
    intensity = np.where(mask, lum / 255.0, 0.0)
    
    # Crisp cyan core
    r = np.clip(intensity * 125 + (intensity ** 2) * 130, 0, 255).astype(np.uint8)
    g = np.clip(intensity * 242 + (intensity ** 2) * 13, 0, 255).astype(np.uint8)
    b = np.full((h, w), 255, dtype=np.uint8)
    alpha = np.clip(intensity * 255.0 * 1.5, 0, 255).astype(np.uint8)
    
    part_rgba[:, :, 0] = r
    part_rgba[:, :, 1] = g
    part_rgba[:, :, 2] = b
    part_rgba[:, :, 3] = alpha
    
    part_img = Image.fromarray(part_rgba, 'RGBA')
    
    # Tight subtle halo (radius=2 px)
    glow_alpha = part_img.split()[3].filter(ImageFilter.GaussianBlur(radius=2))
    glow_rgba = np.zeros((h, w, 4), dtype=np.uint8)
    glow_rgba[:, :, 0] = 0
    glow_rgba[:, :, 1] = 235
    glow_rgba[:, :, 2] = 255
    glow_rgba[:, :, 3] = np.array(glow_alpha)
    glow_img = Image.fromarray(glow_rgba, 'RGBA')
    
    final_part = Image.alpha_composite(glow_img, part_img)
    final_arr = np.array(final_part)
    
    # Enforce strict spatial containment so blur never spills into prohibited zones
    if key == 'thorax':
        # Zero out neck (Y < 268), lumbar (Y > 530), arms (X < 250 or X > 525), and central spine (Y > 285 & 356 <= X <= 422)
        invalid_th = (yy < 268) | (yy > 530) | (xx < 250) | (xx > 525) | ((yy > 285) & (xx >= 356) & (xx <= 422))
        final_arr[invalid_th, 3] = 0
    elif key == 'right_leg':
        # Zero out hands (X < 238 or Y <= 720 or X > 388)
        invalid_rl = (xx < 238) | (xx > 388) | (yy <= 720)
        final_arr[invalid_rl, 3] = 0
    elif key == 'left_leg':
        # Zero out hands (X > 542 or Y <= 720 or X <= 388)
        invalid_ll = (xx <= 388) | (xx > 542) | (yy <= 720)
        final_arr[invalid_ll, 3] = 0
    elif key == 'spine':
        # Zero out ribs / lateral (X < 356 or X > 422 or Y < 215 or Y > 685)
        invalid_sp = (xx < 356) | (xx > 422) | (yy < 215) | (yy > 685)
        final_arr[invalid_sp, 3] = 0
    elif key == 'pelvis':
        # Zero out lumbar spine above Y=565, femurs below Y=720, lateral hands
        invalid_pv = (yy < 565) | (yy > 720) | (xx < 246) | (xx > 532)
        final_arr[invalid_pv, 3] = 0
    elif key == 'head':
        invalid_hd = (yy > 214) | (xx < 295) | (xx > 480)
        final_arr[invalid_hd, 3] = 0
        
    final_part = Image.fromarray(final_arr, 'RGBA')
    out_path = f'public/images/parts/skeleton_part_{key}.png'
    final_part.save(out_path, optimize=True)
    
    # Composite over base image for visual validation
    composite = im.copy()
    composite.alpha_composite(final_part)
    composite.convert('RGB').resize((240, 430)).save(f'{scratch_dir}/preview_active_{key}.jpg')

    # Bounding check
    nonzeros = np.where(final_arr[:, :, 3] > 10)
    y_min, y_max = nonzeros[0].min(), nonzeros[0].max()
    x_min, x_max = nonzeros[1].min(), nonzeros[1].max()
    print(f'Part [{key}]: Y=[{y_min}, {y_max}], X=[{x_min}, {x_max}], px={len(nonzeros[0])}')

print('All 8 parts regenerated with strict spatial containment!')
