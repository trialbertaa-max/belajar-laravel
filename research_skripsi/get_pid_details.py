import subprocess

out = subprocess.check_output(['powershell', '-Command', 'Get-Process -Id 29056 | Format-List Id, ProcessName, SessionId, Path, StartTime, Handles']).decode('utf-8', errors='ignore')
print(out)
