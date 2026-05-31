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

        $reportes = Reporte::where('estado', 'pendiente')->get();
        $total = $reportes->count();

        if ($total === 0) {
            $this->info('No hay reportes pendientes.');
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
        $this->info("Se actualizaron {$total} reportes. Cambios de nivel: {$cambios}");

        return Command::SUCCESS;
    }
}