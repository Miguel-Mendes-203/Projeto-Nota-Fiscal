<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

$codigo = filter_input(INPUT_GET, 'codigo', FILTER_VALIDATE_INT);

if (!$codigo || $codigo < 1) {
    echo json_encode(['encontrado' => false]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT nm_produto, vl_unitario, nm_imagem
       FROM tb_produto
      WHERE cd_produto = :codigo"
);
$stmt->execute([':codigo' => $codigo]);
$produto = $stmt->fetch();

if (!$produto) {
    echo json_encode(['encontrado' => false]);
    exit;
}

echo json_encode([
    'encontrado' => true,
    'nome'       => $produto['nm_produto'],
    'valor'      => $produto['vl_unitario'],
    'imagem'     => $produto['nm_imagem'] ? 'uploads/' . rawurlencode($produto['nm_imagem']) : null,
]);