import shutil, os

# Source is the preserved recycle bin ASD file
asd_src = r'C:\$Recycle.Bin\S-1-5-21-2673345302-492722432-2933964872-1003\$R2V56TF.asd'
word_appdata = os.path.expandvars(r'%APPDATA%\Microsoft\Word')
asd_dest = os.path.join(word_appdata, 'AutoRecovery save of DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.asd')

# 1. Restore the ASD file into Word's AutoRecovery directory
shutil.copy2(asd_src, asd_dest)
print('1. Restored asd to:', asd_dest, 'size:', os.path.getsize(asd_dest))

# 2. Also keep a copy of the ASD directly in Downloads\skripsehh
shutil.copy2(asd_src, r'C:\Users\Guest User\Downloads\skripsehh\AutoRecovery save of DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.asd')
print('2. Copied asd to skripsehh as well')

# 3. Remove the extra file we created in skripsehh
extra_file = r'C:\Users\Guest User\Downloads\skripsehh\DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU.docx'
if os.path.exists(extra_file):
    os.remove(extra_file)
    print('3. Removed extra file from skripsehh:', extra_file)

# 4. Restore DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx in skripsehh to the exact 10/24 2:22 PM state (before today's edits)
shutil.copy2(r'd:\laragon\www\belajar-laravel\DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP_PRE_243_REVISI.docx', r'C:\Users\Guest User\Downloads\skripsehh\DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')
print('4. Restored DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx in skripsehh!')
