<?php
require_once 'conexao.php';

$sql = "SELECT cnpj, razao_social, pais, email FROM clientes ORDER BY razao_social";
$clientes = $pdo->query($sql)->fetchAll();

require 'base_dados.sql';