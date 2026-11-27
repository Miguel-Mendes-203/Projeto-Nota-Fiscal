<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT ID_USUARIO, NM_USUARIO, EMAIL FROM TB_USUARIO WHERE ID_USUARIO = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: listar_usuarios.php?msg=nao_encontrado');
    exit;
}

$erro  = '';
$dados = [
    'id'    => $usuario['ID_USUARIO'],
    'nome'  => $usuario['NM_USUARIO'] ?? '',
    'email' => $usuario['EMAIL'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['nome']  = trim($_POST['nome'] ?? '');
    $dados['email'] = trim($_POST['email'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirmar      = $_POST['confirmar_senha'] ?? '';

    if ($dados['nome'] === '' || $dados['email'] === '') {
        $erro = 'Preencha o nome e o e-mail.';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (mb_strlen($dados['nome']) > 50 || mb_strlen($dados['email']) > 60) {
        $erro = 'Nome (máx. 50) ou e-mail (máx. 60) acima do limite.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não conferem.';
    } else {
        // E-mail repetido em OUTRO usuário
        $stmt = $pdo->prepare("SELECT 1 FROM TB_USUARIO WHERE EMAIL = :email AND ID_USUARIO <> :id");
        $stmt->execute([':email' => $dados['email'], ':id' => $id]);

        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado em outro usuário.';
        } else {
            try {
                if ($senha !== '') {
                    $stmt = $pdo->prepare(
                        "UPDATE TB_USUARIO SET NM_USUARIO = :nome, EMAIL = :email, senha = :senha
                          WHERE ID_USUARIO = :id"
                    );
                    $stmt->execute([
                        ':nome'  => $dados['nome'],
                        ':email' => $dados['email'],
                        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                        ':id'    => $id,
                    ]);
                } else {
                    $stmt = $pdo->prepare(
                        "UPDATE TB_USUARIO SET NM_USUARIO = :nome, EMAIL = :email
                          WHERE ID_USUARIO = :id"
                    );
                    $stmt->execute([
                        ':nome'  => $dados['nome'],
                        ':email' => $dados['email'],
                        ':id'    => $id,
                    ]);
                }

                if ($id === (int) $_SESSION['id_usuario']) {
                    $_SESSION['nm_usuario'] = $dados['nome'];
                }

                header('Location: listar_usuarios.php?msg=editado');
                exit;
            } catch (PDOException $e) {
                $erro = 'Erro ao salvar. Tente novamente.';
            }
        }
    }
}

require 'editar_usuario.html';
