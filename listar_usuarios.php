<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

// A coluna de senha não é selecionada de propósito.
$sql = "SELECT ID_USUARIO AS id, NM_USUARIO AS nome, EMAIL AS email
          FROM TB_USUARIO
         ORDER BY ID_USUARIO";
$usuarios = $pdo->query($sql)->fetchAll();

$msg = $_GET['msg'] ?? '';

require 'tela-de-usuario.html';
