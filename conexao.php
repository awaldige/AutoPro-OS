<?php

$configPath = __DIR__ . '/config.local.php';

if (!file_exists($configPath)) {
    error_log('Arquivo de configuração do banco não encontrado.');
    die('Configuração do banco de dados indisponível.');
}

$config = require $configPath;

if (!is_array($config)) {
    error_log('Configuração do banco inválida.');
    die('Configuração do banco de dados inválida.');
}

$host = $config['host'] ?? '';
$user = $config['user'] ?? '';
$pass = $config['pass'] ?? '';
$db   = $config['database'] ?? '';
$port = (int) ($config['port'] ?? 3306);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Erro na conexão com o banco: ' . $e->getMessage());
    die('Não foi possível conectar ao banco de dados.');
}

?>