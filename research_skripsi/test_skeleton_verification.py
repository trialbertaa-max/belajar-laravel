import urllib.request
import numpy as np
from PIL import Image

# 1. Verify HTML response
url = 'http://127.0.0.1:8000/scans'
html = urllib.request.urlopen(url).read().decode('utf-8')

assert '?v=3' in html, 'Missing ?v=3 cache buster!'
assert 'id="skel-spine"' in html, 'Missing skel-spine!'

pos_spine = html.find('id="skel-spine"')
pos_thorax = html.find('id="skel-thorax"')
pos_pelvis = html.find('id="skel-pelvis"')
assert pos_spine > pos_thorax, 'skel-spine MUST be after skel-thorax!'
assert pos_spine > pos_pelvis, 'skel-spine MUST be after skel-pelvis!'
print('[OK] DOM hierarchy verified: Spine is on the top layer!')

# 2. Verify all image masks
parts_dir = 'public/images/parts'

# Thorax verification
im_th = Image.open(f'{parts_dir}/skeleton_part_thorax.png')
arr_th = np.array(im_th)[:, :, 3]
neck_pixels = ((arr_th > 10) & (np.arange(1376)[:, None] < 264)).sum()
lumbar_pixels = ((arr_th > 10) & (np.arange(1376)[:, None] > 535)).sum()
mid_spine_pixels = ((arr_th > 10) & (np.arange(1376)[:, None] >= 290) & (np.arange(1376)[:, None] <= 530) & (np.arange(768) >= 358) & (np.arange(768) <= 420)).sum()
assert neck_pixels == 0, f'Neck pixels found in thorax: {neck_pixels}'
assert lumbar_pixels == 0, f'Lumbar pixels found in thorax: {lumbar_pixels}'
assert mid_spine_pixels == 0, f'Spine pixels found in thorax: {mid_spine_pixels}'
print('[OK] Thorax mask verified: Neck is 0, Lumbar is 0, Central Spine is 0!')

# Legs verification (NO HANDS)
im_rleg = Image.open(f'{parts_dir}/skeleton_part_right_leg.png')
arr_rleg = np.array(im_rleg)[:, :, 3]
hand_left_in_rleg = ((arr_rleg > 10) & (np.arange(1376)[:, None] >= 650) & (np.arange(1376)[:, None] <= 830) & (np.arange(768) < 220)).sum()
hand_right_in_rleg = ((arr_rleg > 10) & (np.arange(1376)[:, None] >= 650) & (np.arange(1376)[:, None] <= 830) & (np.arange(768) > 550)).sum()
assert hand_left_in_rleg == 0 and hand_right_in_rleg == 0, 'Hands found in right_leg!'

im_lleg = Image.open(f'{parts_dir}/skeleton_part_left_leg.png')
arr_lleg = np.array(im_lleg)[:, :, 3]
hand_left_in_lleg = ((arr_lleg > 10) & (np.arange(1376)[:, None] >= 650) & (np.arange(1376)[:, None] <= 830) & (np.arange(768) < 220)).sum()
hand_right_in_lleg = ((arr_lleg > 10) & (np.arange(1376)[:, None] >= 650) & (np.arange(1376)[:, None] <= 830) & (np.arange(768) > 550)).sum()
assert hand_left_in_lleg == 0 and hand_right_in_lleg == 0, 'Hands found in left_leg!'
print('[OK] Legs mask verified: Zero hand pixels in both legs!')

# Spine verification
im_sp = Image.open(f'{parts_dir}/skeleton_part_spine.png')
arr_sp = np.array(im_sp)[:, :, 3]
cspine_pixels = ((arr_sp > 10) & (np.arange(1376)[:, None] >= 215) & (np.arange(1376)[:, None] <= 265)).sum()
tspine_pixels = ((arr_sp > 10) & (np.arange(1376)[:, None] > 265) & (np.arange(1376)[:, None] <= 530)).sum()
lspine_pixels = ((arr_sp > 10) & (np.arange(1376)[:, None] > 530) & (np.arange(1376)[:, None] <= 685)).sum()
ribs_in_spine = ((arr_sp > 10) & ((np.arange(768) < 350) | (np.arange(768) > 426))).sum()
assert cspine_pixels > 1000, 'Missing C-spine!'
assert tspine_pixels > 1000, 'Missing T-spine!'
assert lspine_pixels > 1000, 'Missing L-spine & Sacrum!'
assert ribs_in_spine == 0, 'Ribs found in spine!'
print('[OK] Spine mask verified: Covers C-spine, T-spine, L-spine, and Sacrum with zero rib bleed!')

print('ALL SKELETON ANATOMICAL VERIFICATIONS PASSED!')
