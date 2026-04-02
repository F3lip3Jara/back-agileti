# =============================================================================
# Script: workers_paralelo.ps1
# Descripción: Abre múltiples terminales con queue:work para procesar en paralelo
# Uso: .\workers_paralelo.ps1 -n 80    (abre 80 workers, uno por cada chunk)
#      .\workers_paralelo.ps1 -n 20    (abre 20 workers)
# =============================================================================

param(
    [Parameter(Mandatory=$false)]
    [Alias("n")]
    [int]$NumWorkers = 117,  # Por defecto 80 para cubrir todos los chunks
    
    [Parameter(Mandatory=$false)]
    [int]$Timeout = 1200
)

$laravelPath = Split-Path -Parent $MyInvocation.MyCommand.Path
if (-not $laravelPath) { $laravelPath = Get-Location }

Write-Host ""
Write-Host "╔════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   QUEUE WORKERS PARALELOS - VALIDAR ETIQUETAS  ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Iniciando $NumWorkers workers en paralelo..." -ForegroundColor Yellow
Write-Host "  Directorio: $laravelPath" -ForegroundColor Gray
Write-Host "  Timeout por job: $Timeout segundos" -ForegroundColor Gray
Write-Host ""

# Comando que ejecutará cada worker
$command = "php artisan queue:work --max-jobs=1 --timeout=$Timeout --stop-when-empty"

# Iniciar workers en terminales separadas
for ($i = 1; $i -le $NumWorkers; $i++) {
    $title = "Worker-$i"
    
    # Crear comando para ejecutar en la nueva ventana
    $scriptBlock = @"
`$Host.UI.RawUI.WindowTitle = '$title'
Write-Host '===========================================' -ForegroundColor Cyan
Write-Host '  Worker $i de $NumWorkers' -ForegroundColor Green
Write-Host '===========================================' -ForegroundColor Cyan
Write-Host ''
cd '$laravelPath'
$command
Write-Host ''
Write-Host 'Worker $i finalizado.' -ForegroundColor Yellow
Start-Sleep -Seconds 3
"@

    # Iniciar proceso
    Start-Process powershell -ArgumentList "-NoProfile", "-Command", $scriptBlock
    
    # Mostrar progreso
    $progress = [math]::Round(($i / $NumWorkers) * 100)
    Write-Host "  [$i/$NumWorkers] Worker iniciado ($progress%)" -ForegroundColor Green
    
    # Pequeña pausa para no saturar el sistema
    Start-Sleep -Milliseconds 50
}

Write-Host ""
Write-Host "╔════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║   ¡$NumWorkers workers iniciados exitosamente!      ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "  Cada worker procesará un job y se cerrará automáticamente." -ForegroundColor Gray
Write-Host ""
Write-Host "  Para detener TODOS los workers:" -ForegroundColor Yellow
Write-Host "    Get-Process php | Stop-Process -Force" -ForegroundColor White
Write-Host ""
Write-Host "  Para ver los procesos PHP activos:" -ForegroundColor Yellow
Write-Host "    Get-Process php" -ForegroundColor White
Write-Host ""

