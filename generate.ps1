$files = @("check_status.php", "checkout.php", "generate_full_content.py", "index.html", "payment.php")
$out = "conteudo_completo.txt"
Set-Content -Path $out -Value "" -Encoding utf8

foreach ($f in $files) {
    if (Test-Path $f) {
        Add-Content -Path $out -Value "`n==================================================" -Encoding utf8
        Add-Content -Path $out -Value "FILE: $f" -Encoding utf8
        Add-Content -Path $out -Value "==================================================`n" -Encoding utf8
        $content = Get-Content -Path $f -Raw
        Add-Content -Path $out -Value $content -Encoding utf8
    }
}
Write-Host "Done written to $out"
