# install-php-toolchain.ps1 - PHP 8.3 + Composer, direct from official sources.
# Exists because winget's PHP.PHP.8.3 manifest chronically 404s: each new PHP patch
# archives the previous zip and the manifest lags (hit 2026-08-18 with 8.3.32).
# Reads the CURRENT 8.3 build + sha256 from php.net's releases.json, so it never pins
# a dead URL. Installs to %USERPROFILE%\tools\php-8.3, user PATH, no admin needed.
$ErrorActionPreference = 'Stop'

$dest = Join-Path $env:USERPROFILE 'tools\php-8.3'
$releases = Invoke-WebRequest -Uri 'https://downloads.php.net/~windows/releases/releases.json' -UseBasicParsing | Select-Object -ExpandProperty Content | ConvertFrom-Json
$build = $releases.'8.3'.'ts-vs16-x64'.zip
if (-not $build.path) { throw 'releases.json no longer lists a ts-vs16-x64 zip for 8.3 - inspect https://windows.php.net/download/ manually.' }
Write-Host "Current PHP 8.3 build: $($build.path)"

$zip = Join-Path $env:TEMP $build.path
Invoke-WebRequest -Uri "https://downloads.php.net/~windows/releases/$($build.path)" -OutFile $zip -UseBasicParsing
$actual = (Get-FileHash $zip -Algorithm SHA256).Hash.ToLower()
if ($actual -ne $build.sha256.ToLower()) { Remove-Item $zip -Force; throw "SHA256 MISMATCH - expected $($build.sha256), got $actual. Aborting." }
Write-Host 'SHA256 verified against releases.json.'

if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
New-Item -ItemType Directory -Force $dest | Out-Null
Expand-Archive -Path $zip -DestinationPath $dest -Force
Remove-Item $zip -Force

# php.ini: production template + the extensions the toolchain needs (composer, phpcs, phpstan)
$ini = Get-Content (Join-Path $dest 'php.ini-production') -Raw
$ini = $ini -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
foreach ($ext in 'curl','fileinfo','mbstring','openssl','zip') { $ini = $ini -replace ";extension=$ext", "extension=$ext" }
$ini = $ini -replace ';date.timezone =', 'date.timezone = Asia/Jerusalem'
Set-Content -Path (Join-Path $dest 'php.ini') -Value $ini -Encoding ascii

# Composer: official phar + published checksum, with a .bat shim next to php.exe
$phar = Join-Path $dest 'composer.phar'
Invoke-WebRequest -Uri 'https://getcomposer.org/download/latest-stable/composer.phar' -OutFile $phar -UseBasicParsing
$comSha = (Invoke-WebRequest -Uri 'https://getcomposer.org/download/latest-stable/composer.phar.sha256sum' -UseBasicParsing | Select-Object -ExpandProperty Content).Trim() -split '\s+' | Select-Object -First 1
$comActual = (Get-FileHash $phar -Algorithm SHA256).Hash.ToLower()
if ($comActual -ne $comSha.ToLower()) { Remove-Item $phar -Force; throw "Composer SHA256 MISMATCH - expected $comSha, got $comActual. Aborting." }
Set-Content -Path (Join-Path $dest 'composer.bat') -Value "@php `"%~dp0composer.phar`" %*" -Encoding ascii
Write-Host 'Composer verified and shimmed.'

# Persistent user PATH + current session
$userPath = [Environment]::GetEnvironmentVariable('Path', 'User')
if ($userPath -notlike "*$dest*") { [Environment]::SetEnvironmentVariable('Path', "$userPath;$dest", 'User') }
$env:Path = "$env:Path;$dest"

& (Join-Path $dest 'php.exe') -v
if ($LASTEXITCODE -ne 0) { throw 'php.exe failed to run - likely missing VC++ runtime. Fix: winget install Microsoft.VCRedist.2015+.x64 -e' }
& (Join-Path $dest 'php.exe') (Join-Path $dest 'composer.phar') -V

# Rebuild the repo's PHP quality vendor (PHPCS + PHPStan)
$repo = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
Push-Location (Join-Path $repo 'tooling\php-quality')
& (Join-Path $dest 'php.exe') (Join-Path $dest 'composer.phar') install --no-interaction --prefer-dist
Pop-Location

Write-Host ''
Write-Host 'DONE. PHP + Composer installed, quality vendor rebuilt.' -ForegroundColor Green
Write-Host 'Reopen the terminal (PATH refresh), then tell Claude to run the battery.'

