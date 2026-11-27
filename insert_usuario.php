<?php
// Cadastro de usuário (tela Registro).
// O arquivo antigo usava mysqli ($conexao), a função aviso() que não existe,
// não parava a execução depois de um erro e não informava o ID_USUARIO.
require_once 'conexao.php';

$erro  = '';
$dados = ['nome' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['nome']  = trim($_POST['nome'] ?? '');
    $dados['email'] = trim($_POST['email'] ?? '');
    $senha          = $_POST['senha'] ?? '';
    $confirmar      = $_POST['confirmar_senha'] ?? '';

    if ($dados['nome'] === '' || $dados['email'] === '' || $senha === '' || $confirmar === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (mb_strlen($dados['nome']) > 50 || mb_strlen($dados['email']) > 60) {
        $erro = 'Nome (máx. 50) ou e-mail (máx. 60) acima do limite.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não conferem.';
    } else {
        $stmt = $pdo->prepare("SELECT 1 FROM TB_USUARIO WHERE EMAIL = :email");
        $stmt->execute([':email' => $dados['email']]);

        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            try {
                // Calcula o próximo código (funciona com ou sem AUTO_INCREMENT)
                $proximoId = (int) $pdo->query(
                    "SELECT COALESCE(MAX(ID_USUARIO), 0) + 1 FROM TB_USUARIO"
                )->fetchColumn();

                $stmt = $pdo->prepare(
                    "INSERT INTO TB_USUARIO (ID_USUARIO, NM_USUARIO, EMAIL, senha)
                     VALUES (:id, :nome, :email, :senha)"
                );
                $stmt->execute([
                    ':id'    => $proximoId,
                    ':nome'  => $dados['nome'],
                    ':email' => $dados['email'],
                    ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                ]);

                header('Location: login.php');
                exit;
            } catch (PDOException $e) {
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
        }
    }
}

require 'registro.html';
