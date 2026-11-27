<?php
// Conexão com o banco usando PDO (todos os outros arquivos usam $pdo).
// O arquivo antigo tinha ';' faltando, usava mysqli com os parâmetros fora de ordem
// e testava a conexão de forma invertida.
$server   = 'localhost';
$database = 'db_notas';
$user     = 'root';
$password = 'admin';

try {
    $pdo = new PDO(
        "mysql:host=$server;dbname=$database;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Falha na conexão com o banco de dados!');
}
