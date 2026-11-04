$word = New-Object -ComObject Word.Application
$word.Visible = $false
$asdPath = [System.Environment]::ExpandEnvironmentVariables('%LOCALAPPDATA%\Microsoft\Office\UnsavedFiles\DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.asd')
Write-Host "ASD Path: $asdPath"
Write-Host "Exists: $(Test-Path $asdPath)"

try {
    $doc = $word.Documents.Open($asdPath)
    Write-Host "Successfully opened in Word COM! Document name: $($doc.Name)"
    $savePath = "d:\laragon\www\belajar-laravel\PULIHAN_DARI_AUTORECOVER.docx"
    $doc.SaveAs2($savePath, 16) # 16 = wdFormatXMLDocument (.docx)
    Write-Host "Saved to: $savePath"
    $doc.Close([ref]$false)
} catch {
    Write-Host "Error opening: $($_.Exception.Message)"
} finally {
    $word.Quit()
    [System.Runtime.Interopservices.Marshal]::ReleaseComObject($word) | Out-Null
    [System.GC]::Collect()
    [System.GC]::WaitForPendingFinalizers()
}
