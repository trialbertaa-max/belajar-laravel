import cv2
import numpy as np

im = cv2.imread('public/scan-assets/scan_2_20260925_135553.jpg')
print("Image shape:", im.shape)

# Let's inspect rows around 45-55 across columns
row40 = im[40, 500:600, 0]
row50 = im[50, 500:600, 0]
row60 = im[60, 500:600, 0]
print("Row 40 middle:", row40[:10])
print("Row 50 middle:", row50[:10])
print("Row 60 middle:", row60[:10])

# Let's check the right edge around col 760-770
col760 = im[500:510, 760, 0]
col766 = im[500:510, 766, 0]
col772 = im[500:510, 772, 0]
print("Col 760 middle:", col760)
print("Col 766 middle:", col766)
print("Col 772 middle:", col772)
