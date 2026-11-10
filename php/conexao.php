<?php
$server = 'localhost' 
$database = "db_notas";
$user = "root";
$password = "admin"

$conexao = new mysqli($server, $database, $user, $password);

if (!$conexao == false ) {
    die("Falha na conexão!");
}

?>