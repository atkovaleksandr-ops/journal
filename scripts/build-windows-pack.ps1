$ErrorActionPreference = 'Stop'

$env:Path = "C:\laragon\bin\nodejs\node-v22;$env:Path"
$env:CSC_IDENTITY_AUTO_DISCOVERY = 'false'

Set-Location 'C:\laragon\www\journal\apps\windows-electron'
& 'C:\laragon\bin\nodejs\node-v22\npm.cmd' run dist

$source = 'C:\laragon\www\journal\apps\windows-electron\dist\Journal-Windows-Setup.exe'
$destination = 'C:\laragon\www\journal\public\downloads\Journal-Windows-Setup.exe'
$legacyZip = 'C:\laragon\www\journal\public\downloads\Journal-Windows.zip'

if (-not (Test-Path -LiteralPath $source)) {
    throw "Installer was not created: $source"
}

Copy-Item -LiteralPath $source -Destination $destination -Force

if (Test-Path -LiteralPath $legacyZip) {
    Remove-Item -LiteralPath $legacyZip -Force
}

Write-Host "Windows installer updated: $destination"
