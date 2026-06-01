<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    echo "Cargando autoload...<br>";
    require __DIR__ . '/../vendor/autoload.php';
    echo "Autoload OK<br>";

    echo "Creando app...<br>";
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "App creada<br>";

    echo "Obteniendo kernel...<br>";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "Kernel OK<br>";

    echo "Todo funciona correctamente.";
} catch (Throwable $e) {
    echo "<pre>";
    echo "ERROR: " . $e->getMessage() . "\n\n";
    echo "ARCHIVO: " . $e->getFile() . "\n";
    echo "LÍNEA: " . $e->getLine() . "\n\n";
    echo "TRACE:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>