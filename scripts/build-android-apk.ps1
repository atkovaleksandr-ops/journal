$ErrorActionPreference = 'Stop'

$env:JAVA_HOME = 'C:\Program Files\Android\Android Studio\jbr'
$env:ANDROID_HOME = 'C:\Users\atkov\AppData\Local\Android\Sdk'
$env:ANDROID_SDK_ROOT = $env:ANDROID_HOME
$env:Path = "$env:JAVA_HOME\bin;$env:ANDROID_HOME\platform-tools;C:\laragon\bin\nodejs\node-v22;$env:Path"

Set-Location 'C:\laragon\www\journal\apps\android-capacitor'
& 'C:\laragon\bin\nodejs\node-v22\npm.cmd' run sync
& 'C:\laragon\bin\nodejs\node-v22\npm.cmd' run build:apk

$apk = 'C:\laragon\www\journal\apps\android-capacitor\android\app\build\outputs\apk\debug\app-debug.apk'
$destination = 'C:\laragon\www\journal\public\downloads\Journal-Android.apk'

Copy-Item -LiteralPath $apk -Destination $destination -Force
Write-Host "Android APK updated: $destination"
