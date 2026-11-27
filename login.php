<?php
session_start();
require_once 'conexao.php';

// Quem já está logado vai direto para o menu
if (!empty($_SESSION['id_usuario'])) {
    header('Location: home.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnpj         = preg_replace('/\D/', '', $_POST['cnpj'] ?? ''); // o banco guarda só números
    $email        = trim($_POST['email'] ?? '');
    $senha        = $_POST['senha'] ?? '';
    $razao_social = trim($_POST['razao_social'] ?? '');

    if ($cnpj === '' || $email === '' || $senha === '' || $razao_social === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmtUsuario = $pdo->prepare(
            "SELECT ID_USUARIO, NM_USUARIO, senha
               FROM TB_USUARIO
              WHERE EMAIL = :email"
        );
        $stmtUsuario->execute([':email' => $email]);
        $usuario = $stmtUsuario->fetch();

        // O cliente precisa ser do mesmo e-mail do usuário
        // (assim ninguém entra misturando e-mail de um com CNPJ de outro)
        $stmtCliente = $pdo->prepare(
            "SELECT nr_cnpj, nm_razao_social
               FROM tb_cliente
              WHERE nr_cnpj = :cnpj
                AND nm_razao_social = :razao_social
                AND nm_email = :email"
        );
        $stmtCliente->execute([
            ':cnpj'         => $cnpj,
            ':razao_social' => $razao_social,
            ':email'        => $email,
        ]);
        $cliente = $stmtCliente->fetch();

        if ($usuario && $cliente && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            $_SESSION['id_usuario']   = $usuario['ID_USUARIO'];
            $_SESSION['nm_usuario']   = $usuario['NM_USUARIO'];
            $_SESSION['cnpj']         = $cliente['nr_cnpj'];
            $_SESSION['razao_social'] = $cliente['nm_razao_social'];
            header('Location: home.php'); // antes ia para menu.php, que não existe
            exit;
        }

        $erro = 'Dados de login inválidos.';
    }
}

require 'login.html';
