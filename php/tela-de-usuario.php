<?php
require_once 'conexao.php';

$sql = "SELECT id, nome, email FROM usuarios ORDER BY id";
$usuarios = $pdo->query($sql)->fetchAll();

require 'base_dados.sql';