<?php
$host = "localhost";
$banco = "db_notas";
$usuario = "root";
$senha = "admin"

$conexao = new mysqli($host,$usuario,$senha,$banco);

if (!$conexao) {
    echo "Falha na conexão!";
}

?>