import subprocess

out = subprocess.check_output(['powershell', '-Command', 'Get-Process winword | Format-List Id, ProcessName, MainWindowTitle, Responding']).decode('utf-8', errors='ignore')
print(out)
