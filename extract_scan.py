import sys
import os
import pydicom
import cv2
import numpy as np

def extract_dcm_to_jpg(dcm_path, output_jpg_path):
    if not os.path.exists(dcm_path):
        print(f"File tidak ditemukan: {dcm_path}")
        return False
    
    ds = pydicom.dcmread(dcm_path)
    pixel_array = ds.pixel_array.astype(float)
    
    # Handle multi-frame (misal USG video / multi-slice), ambil frame pertama
    if len(pixel_array.shape) == 3 and pixel_array.shape[0] > 3:
        pixel_array = pixel_array[0]
        
    norm_img = (np.maximum(pixel_array, 0) / pixel_array.max()) * 255.0
    img_uint8 = np.uint8(norm_img)
    
    os.makedirs(os.path.dirname(output_jpg_path), exist_ok=True)
    cv2.imwrite(output_jpg_path, img_uint8)
    print(f"Berhasil ekstrak: {dcm_path} -> {output_jpg_path}")
    return True

if __name__ == "__main__":
    if len(sys.argv) >= 3:
        extract_dcm_to_jpg(sys.argv[1], sys.argv[2])
    else:
        print("Penggunaan: python extract_scan.py <input.dcm> <output.jpg>")
