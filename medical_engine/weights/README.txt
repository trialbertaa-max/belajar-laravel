Direktori Penyimpanan Bobot Model AI (Pretrained Weights) Hyu PACS
======================================================================

Letakkan berkas checkpoint model pretrained di folder ini dengan nama:
  -> bone_suppression_cxr.pth
  (atau format .onnx jika menggunakan ONNX runtime)

Format & Spesifikasi yang Didukung:
- Arsitektur: U-Net / BSE-Net / cGAN / Conditional Diffusion Model
- Input: Grayscale Chest Radiograph (CXR) 512x512 atau 1024x1024
- Output: Soft-Tissue Image (Tulang Iga & Klavikula Tersupresi)

Begitu file 'bone_suppression_cxr.pth' diletakkan di sini, engine
medical_engine/bone_suppression.py akan otomatis mendeteksi dan mengaktifkan
inferensi visual pada viewer Hyu PACS.
