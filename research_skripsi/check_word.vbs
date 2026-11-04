On Error Resume Next
Set word = GetObject(, "Word.Application")
If Err.Number <> 0 Then
    WScript.Echo "Error connecting to Word: " & Err.Description
Else
    WScript.Echo "Connected to Word! Docs count: " & word.Documents.Count
    For Each doc In word.Documents
        WScript.Echo "Doc: " & doc.Name & " | Saved: " & doc.Saved & " | Path: " & doc.FullName
    Next
End If
