# =============================================================================
# Script: ejecutar_workers.ps1
# Descripción: Ejecuta múltiples queue workers de Laravel en paralelo
# Uso: .\ejecutar_workers.ps1 -Workers 10
#      .\ejecutar_workers.ps1 -Workers 20 -Queue "default"
#      .\ejecutar_workers.ps1 -Workers 80 -MaxJobs 1 -Timeout 1200
# =============================================================================

param(
    [Parameter(Mandatory=$false)]
    [int]$Workers = 50,  # Número de workers a ejecutar en paralelo
    
    [Parameter(Mandatory=$false)]
    [string]$Queue = "default",  # Nombre de la cola
    
    [Parameter(Mandatory=$false)]
    [int]$MaxJobs = 1,  # Máximo de jobs por worker antes de reiniciar
    
    [Parameter(Mandatory=$false)]
    [int]$Timeout = 1200,  # Timeout en segundos por job
    
    [Parameter(Mandatory=$false)]
    [switch]$StopOnEmpty,  # Detener cuando la cola esté vacía
    
    [Parameter(Mandatory=$false)]
    [switch]$OpenInNewWindows  # Abrir cada worker en una ventana nueva
)

$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
if (-not $scriptPath) { $scriptPath = Get-Location }

# Verificar que existe artisan
$artisanPath = Join-Path $scriptPath "artisan"
if (-not (Test-Path $artisanPath)) {
    Write-Host "ERROR: No se encontró el archivo 'artisan' en $scriptPath" -ForegroundColor Red
    Write-Host "Asegúrate de ejecutar este script desde el directorio raíz de Laravel" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "   Laravel Queue Workers - Ejecución Paralela" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Directorio: $scriptPath" -ForegroundColor Gray
Write-Host "  Workers a iniciar: $Workers" -ForegroundColor Green
Write-Host "  Cola: $Queue" -ForegroundColor Yellow
Write-Host "  Max Jobs por worker: $MaxJobs" -ForegroundColor Yellow
Write-Host "  Timeout: $Timeout segundos" -ForegroundColor Yellow
Write-Host ""

# Construir el comando base
$stopFlag = if ($StopOnEmpty) { "--stop-when-empty" } else { "" }
$baseCommand = "php artisan queue:work --queue=$Queue --max-jobs=$MaxJobs --timeout=$Timeout $stopFlag"

Write-Host "Comando base: $baseCommand" -ForegroundColor Gray
Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Iniciando $Workers workers..." -ForegroundColor Yellow
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$processes = @()

if ($OpenInNewWindows) {
    # Abrir cada worker en una ventana nueva de PowerShell
    for ($i = 1; $i -le $Workers; $i++) {
        $windowTitle = "Queue Worker $i"
        $process = Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$scriptPath'; Write-Host 'Worker $i iniciado...' -ForegroundColor Green; $baseCommand" -PassThru
        $processes += $process
        Write-Host "  [Worker $i] Iniciado en nueva ventana (PID: $($process.Id))" -ForegroundColor Green
        Start-Sleep -Milliseconds 100  # Pequeña pausa para evitar congestión
    }
} else {
    # Ejecutar todos como background jobs en la misma consola
    for ($i = 1; $i -le $Workers; $i++) {
        $job = Start-Job -ScriptBlock {
            param($path, $command, $workerId)
            Set-Location $path
            $output = Invoke-Expression $command 2>&1
            return @{
                WorkerId = $workerId
                Output = $output
            }
        } -ArgumentList $scriptPath, $baseCommand, $i
        
        Write-Host "  [Worker $i] Iniciado como background job (Job ID: $($job.Id))" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "         Todos los workers iniciados" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

if ($OpenInNewWindows) {
    Write-Host "Se han abierto $Workers ventanas de PowerShell." -ForegroundColor Yellow
    Write-Host "Cada ventana ejecuta un queue worker independiente." -ForegroundColor Gray
    Write-Host ""
    Write-Host "Para detener todos los workers, cierra las ventanas o ejecuta:" -ForegroundColor Gray
    Write-Host "  Get-Process php | Stop-Process" -ForegroundColor White
} else {
    Write-Host "Los workers están ejecutándose como background jobs." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Comandos útiles:" -ForegroundColor Gray
    Write-Host "  Get-Job              - Ver estado de los jobs" -ForegroundColor White
    Write-Host "  Get-Job | Wait-Job   - Esperar a que terminen todos" -ForegroundColor White
    Write-Host "  Get-Job | Stop-Job   - Detener todos los jobs" -ForegroundColor White
    Write-Host "  Get-Job | Remove-Job - Limpiar jobs completados" -ForegroundColor White
    Write-Host ""
    
    # Preguntar si desea esperar
    Write-Host "¿Deseas esperar a que terminen todos los jobs? (S/N): " -ForegroundColor Yellow -NoNewline
    $response = Read-Host
    
    if ($response -match '^[sS]$') {
        Write-Host ""
        Write-Host "Esperando a que terminen todos los workers..." -ForegroundColor Yellow
        Write-Host "Presiona Ctrl+C para cancelar la espera (los jobs seguirán ejecutándose)" -ForegroundColor Gray
        Write-Host ""
        
        $jobs = Get-Job
        $totalJobs = $jobs.Count
        
        while ($jobs | Where-Object { $_.State -eq 'Running' }) {
            $running = ($jobs | Where-Object { $_.State -eq 'Running' }).Count
            $completed = ($jobs | Where-Object { $_.State -eq 'Completed' }).Count
            $failed = ($jobs | Where-Object { $_.State -eq 'Failed' }).Count
            
            Write-Host "`r  Ejecutando: $running | Completados: $completed | Fallidos: $failed" -NoNewline -ForegroundColor Cyan
            Start-Sleep -Seconds 2
            $jobs = Get-Job
        }
        
        Write-Host ""
        Write-Host ""
        Write-Host "================================================" -ForegroundColor Cyan
        Write-Host "         Todos los workers terminaron" -ForegroundColor Green
        Write-Host "================================================" -ForegroundColor Cyan
        
        # Mostrar resumen
        $jobs = Get-Job
        $completed = ($jobs | Where-Object { $_.State -eq 'Completed' }).Count
        $failed = ($jobs | Where-Object { $_.State -eq 'Failed' }).Count
        
        Write-Host ""
        Write-Host "  Completados: $completed" -ForegroundColor Green
        Write-Host "  Fallidos: $failed" -ForegroundColor $(if ($failed -gt 0) { "Red" } else { "Gray" })
        
        # Limpiar jobs
        Get-Job | Remove-Job
        Write-Host ""
        Write-Host "Jobs limpiados." -ForegroundColor Gray
    }
}

Write-Host ""

