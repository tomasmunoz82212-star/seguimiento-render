<?php
echo "PHP funciona correctamente.<br>";

try {
    $pdo = new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                   getenv('DB_USERNAME'),
                   getenv('DB_PASSWORD'));
    echo "Conexión a PostgreSQL exitosa.";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>