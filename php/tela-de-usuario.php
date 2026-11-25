<?php
require_once 'conexao.php';

$sql = "SELECT ID_USUARIO AS id, NM_USUARIO AS nome, EMAIL AS email
          FROM TB_USUARIO
         ORDER BY ID_USUARIO";
$usuarios = $pdo->query($sql)->fetchAll();

require 'listar_usuarios_view.php';