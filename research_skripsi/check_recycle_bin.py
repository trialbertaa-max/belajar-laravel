import shutil, os

recycle_dir = r'C:\$Recycle.Bin\S-1-5-21-2673345302-492722432-2933964872-1003'
for f in os.listdir(recycle_dir):
    full_path = os.path.join(recycle_dir, f)
    print(f, os.path.getsize(full_path), os.path.getmtime(full_path))
    if f.endswith('.asd'):
        dest = f"recovered_{f}.asd"
        shutil.copy2(full_path, dest)
        print(f"Copied {f} to {dest}")
