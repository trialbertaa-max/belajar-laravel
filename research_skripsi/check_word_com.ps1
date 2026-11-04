try {
    $word = [System.Runtime.InteropServices.Marshal]::GetActiveObject('Word.Application')
    Write-Host "Word found! Open documents count: $($word.Documents.Count)"
    foreach ($doc in $word.Documents) {
        Write-Host "Doc: $($doc.FullName) | Saved: $($doc.Saved)"
    }
} catch {
    Write-Host "COM Error: $($_.Exception.Message)"
}
