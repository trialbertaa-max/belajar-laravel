<?php
header('Content-Type: text/plain');

$script = dirname(__DIR__) . '/hyu_dicom_server.py';
$baseDir = dirname(__DIR__);
$python = 'C:\Python310\python.exe';

echo "1. Testing file existence:\n";
echo "Python exists: " . (file_exists($python) ? 'YES' : 'NO') . "\n";
echo "Script exists: " . (file_exists($script) ? 'YES' : 'NO') . "\n";

echo "\n2. Testing running python directly from PHP:\n";
$output = [];
$return_var = 0;
exec("\"$python\" -V 2>&1", $output, $return_var);
echo "Exit code: $return_var\n";
echo "Output: " . implode("\n", $output) . "\n";

echo "\n3. Testing running powershell command:\n";
$output2 = [];
$return_var2 = 0;
$cmd = 'powershell -Command "Get-Host | Select-Object Version"';
exec($cmd . ' 2>&1', $output2, $return_var2);
echo "PowerShell Exit code: $return_var2\n";
echo "PowerShell Output: " . implode("\n", $output2) . "\n";

echo "\n4. Testing Start-Process:\n";
$startScript = "powershell -Command \"Start-Process -FilePath '$python' -ArgumentList '$script' -WorkingDirectory '$baseDir' -WindowStyle Hidden\"";
$output3 = [];
$return_var3 = 0;
exec($startScript . ' 2>&1', $output3, $return_var3);
echo "Start-Process Exit code: $return_var3\n";
echo "Start-Process Output: " . implode("\n", $output3) . "\n";

echo "\n5. Checking port 4242 after 1 second:\n";
sleep(1);
$fp = @fsockopen('127.0.0.1', 4242, $errno, $errstr, 1);
if (is_resource($fp)) {
    echo "Port 4242 is OPEN!\n";
    fclose($fp);
} else {
    echo "Port 4242 is NOT open: $errno - $errstr\n";
}

