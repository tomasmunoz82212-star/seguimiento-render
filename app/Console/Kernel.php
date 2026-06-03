use App\Console\Commands\ActualizarNivelesAlertasCommand;

protected function schedule(Schedule $schedule)
{
    // Para pruebas locales: cada minuto
    $schedule->command('alertas:actualizar')
        ->everyMinute()
        ->withoutOverlapping();
    
    // Para producción: cada hora (descomentar cuando corresponda)
    // $schedule->command('alertas:actualizar')->hourly();
}