<?php
/**
 * Conexión PDO a MySQL, leyendo la configuración de variables de entorno
 * (las mismas que define docker-compose.yml para el servicio "app").
 */
function getConnection(): PDO
{
    $host = getenv('DB_HOST') ?: 'db';
    $db   = getenv('DB_NAME') ?: 'proyecto_intermodular';
    $user = getenv('DB_USER') ?: 'app_user';
    $pass = getenv('DB_PASSWORD') ?: 'app_pass';

    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
