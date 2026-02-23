<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "Oficina";
$port = 3308; 

// Adicionamos o quinto parâmetro: a porta
$conn = new mysqli($host, $user, $pass, $db, $port);

// Verificar se houve erro
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Opcional: define o padrão de caracteres para evitar erros com acentos
$conn->set_charset("utf8");
?>