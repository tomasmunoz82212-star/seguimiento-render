<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo "Error de Laravel: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
?>