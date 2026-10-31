$word = [System.Runtime.InteropServices.Marshal]::GetActiveObject('Word.Application')
Write-Host "Word Version:" $word.Version
foreach ($d in $word.Documents) {
    Write-Host "Title:" $d.Name "Saved:" $d.Saved "Path:" $d.FullName
}
