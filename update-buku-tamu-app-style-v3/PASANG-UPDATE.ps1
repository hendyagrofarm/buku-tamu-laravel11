$ErrorActionPreference = 'Stop'

$Project = 'C:\xampp\htdocs\buku-tamu-laravel11'
$Source = Join-Path $PSScriptRoot 'files'

Write-Host ''
Write-Host '=== UPDATE TAMPILAN BUKU TAMU APP STYLE V3 ===' -ForegroundColor Cyan

if (-not (Test-Path (Join-Path $Project 'artisan'))) {
    Write-Host "Project tidak ditemukan di: $Project" -ForegroundColor Red
    Write-Host 'Pastikan nama folder project adalah buku-tamu-laravel11.' -ForegroundColor Yellow
    Read-Host 'Tekan Enter untuk keluar'
    exit 1
}

$Copies = @(
    @{ From = 'app\Http\Controllers\KioskController.php'; To = 'app\Http\Controllers\KioskController.php' },
    @{ From = 'app\Models\Visitor.php'; To = 'app\Models\Visitor.php' },
    @{ From = 'resources\views\layouts\kiosk.blade.php'; To = 'resources\views\layouts\kiosk.blade.php' },
    @{ From = 'resources\views\kiosk\home.blade.php'; To = 'resources\views\kiosk\home.blade.php' },
    @{ From = 'public\service-worker.js'; To = 'public\service-worker.js' }
)

foreach ($Item in $Copies) {
    $From = Join-Path $Source $Item.From
    $To = Join-Path $Project $Item.To

    if (-not (Test-Path $From)) {
        throw "File sumber tidak ditemukan: $From"
    }

    $Parent = Split-Path $To -Parent
    New-Item -ItemType Directory -Force -Path $Parent | Out-Null
    Copy-Item -Path $From -Destination $To -Force
    Write-Host "Disalin: $($Item.To)" -ForegroundColor Green
}

$RequiredFolders = @(
    'bootstrap\cache',
    'storage\framework\views',
    'storage\framework\sessions',
    'storage\framework\cache\data',
    'storage\logs'
)
foreach ($Folder in $RequiredFolders) {
    New-Item -ItemType Directory -Force -Path (Join-Path $Project $Folder) | Out-Null
}

$HomeFile = Join-Path $Project 'resources\views\kiosk\home.blade.php'
$Verified = Select-String -Path $HomeFile -Pattern 'GUESTEASE_APP_STYLE_V3' -Quiet
if (-not $Verified) {
    throw 'Verifikasi gagal: file tampilan belum terganti.'
}

Push-Location $Project
try {
    & php artisan optimize:clear
    if ($LASTEXITCODE -ne 0) {
        Write-Host 'Cache Laravel gagal dibersihkan otomatis. File tampilan tetap sudah disalin.' -ForegroundColor Yellow
    }
} finally {
    Pop-Location
}

Write-Host ''
Write-Host '[BERHASIL] Tampilan GuestEase App Style V3 sudah terpasang.' -ForegroundColor Green
Write-Host 'Jalankan: php artisan serve' -ForegroundColor White
Write-Host 'Lalu buka http://127.0.0.1:8000 dan tekan Ctrl+F5.' -ForegroundColor White
Write-Host ''
Read-Host 'Tekan Enter untuk menutup'
