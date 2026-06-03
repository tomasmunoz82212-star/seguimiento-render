<?php

namespace App\Console\Commands;

use App\Models\Reporte;
use Illuminate\Console\Command;

class ActualizarNivelesAlertasCommand extends Command
{
    protected $signature = 'alertas:actualizar';
    protected $description = 'Actualiza los niveles de alerta de todos los reportes pendientes';

    public function handle()
    {
        $this->info('Iniciando actualización de niveles...');
        
        // Obtener el período activo
        $periodoActivo = \App\Models\Periodo::where('estado', 'activo')->first();
        
        if (!$periodoActivo) {
            $this->error('No hay período activo. No se actualizarán niveles.');
            return Command::SUCCESS;
        }
        
        $this->info('Período activo: ' . $periodoActivo->nombre);
        
        // SOLO actualizar reportes del período activo
        $reportes = Reporte::where('estado', 'pendiente')
            ->where('periodo_id', $periodoActivo->id)
            ->get();
        
        $total = $reportes->count();
        
        if ($total === 0) {
            $this->info('No hay reportes pendientes en el período activo.');
            return Command::SUCCESS;
        }
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $cambios = 0;
        foreach ($reportes as $reporte) {
            $nivelAnterior = $reporte->nivel_alerta;
            $reporte->actualizarNivelAlerta(); // ya incluye notificación
            if ($nivelAnterior !== $reporte->nivel_alerta) {
                $cambios++;
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Se actualizaron {$total} reportes del período {$periodoActivo->nombre}. Cambios de nivel: {$cambios}");
        
        return Command::SUCCESS;
    }
}