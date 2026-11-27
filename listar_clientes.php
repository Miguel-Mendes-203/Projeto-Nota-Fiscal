<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

$sql = "SELECT nr_cnpj AS cnpj, nm_razao_social AS razao_social,
               nm_pais AS pais, nm_email AS email
          FROM tb_cliente
         ORDER BY nm_razao_social";
$clientes = $pdo->query($sql)->fetchAll();

$msg = $_GET['msg'] ?? '';

require 'listar-clientes.html';
