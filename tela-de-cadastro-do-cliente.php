<?php
require_once 'verifica_login.php';
require_once 'conexao.php';
$stmt = $pdo->prepare("SELECT NM_USUARIO, EMAIL FROM TB_USUARIO WHERE ID_USUARIO = :id");
$stmt->execute([':id' => $_SESSION['id_usuario']]);
$usuario = $stmt->fetch();

$stmt = $pdo->prepare("SELECT nr_cnpj, nm_razao_social FROM tb_cliente WHERE nr_cnpj = :cnpj");
$stmt->execute([':cnpj' => $_SESSION['cnpj']]);
$cliente = $stmt->fetch();

if (!$usuario || !$cliente) {
    header('Location: logout.php');
    exit;
}

require 'cadastrar-cliente.html';