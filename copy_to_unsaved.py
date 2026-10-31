import os, shutil

unsaved_dir = os.path.expandvars(r'%LOCALAPPDATA%\Microsoft\Office\UnsavedFiles')
os.makedirs(unsaved_dir, exist_ok=True)

# Copy from recycle bin directly
src = r'C:\$Recycle.Bin\S-1-5-21-2673345302-492722432-2933964872-1003\$R2V56TF.asd'
dst = os.path.join(unsaved_dir, 'DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.asd')
shutil.copy2(src, dst)
print('Copied to UnsavedFiles:', dst, 'size:', os.path.getsize(dst))
