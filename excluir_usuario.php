<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_usuarios.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: listar_usuarios.php?msg=erro');
    exit;
}

// Não deixa o usuário excluir a si mesmo
if ($id === (int) $_SESSION['id_usuario']) {
    header('Location: listar_usuarios.php?msg=proprio');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM TB_USUARIO WHERE ID_USUARIO = :id');
    $stmt->execute([':id' => $id]);
    header('Location: listar_usuarios.php?msg=excluido');
} catch (PDOException $e) {
    header('Location: listar_usuarios.php?msg=erro');
}
exit;
