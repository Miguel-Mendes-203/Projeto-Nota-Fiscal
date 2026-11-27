<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_clientes.php');
    exit;
}

$cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');

if ($cnpj === '') {
    header('Location: listar_clientes.php?msg=erro');
    exit;
}

// Não deixa excluir o cliente com o qual está logado
if ($cnpj === ($_SESSION['cnpj'] ?? '')) {
    header('Location: listar_clientes.php?msg=proprio');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM tb_cliente WHERE nr_cnpj = :cnpj');
    $stmt->execute([':cnpj' => $cnpj]);
    header('Location: listar_clientes.php?msg=excluido');
} catch (PDOException $e) {
    // Chave estrangeira: o cliente tem notas cadastradas
    header('Location: listar_clientes.php?msg=erro');
}
exit;
