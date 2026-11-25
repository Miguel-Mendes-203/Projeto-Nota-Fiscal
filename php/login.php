<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnpj         = trim($_POST['cnpj'] ?? '');
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

        $stmtCliente = $pdo->prepare(
            "SELECT nr_cnpj, nm_razao_social
               FROM tb_cliente
              WHERE nr_cnpj = :cnpj AND nm_razao_social = :razao_social"
        );
        $stmtCliente->execute([
            ':cnpj'         => $cnpj,
            ':razao_social' => $razao_social,
        ]);
        $cliente = $stmtCliente->fetch();

        if ($usuario && $cliente && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            $_SESSION['id_usuario']   = $usuario['ID_USUARIO'];
            $_SESSION['nm_usuario']   = $usuario['NM_USUARIO'];
            $_SESSION['cnpj']         = $cliente['nr_cnpj'];
            $_SESSION['razao_social'] = $cliente['nm_razao_social'];
            header('Location: menu.php');
            exit;
        }

        $erro = 'Dados de login inválidos.';
    }
}

require 'login_view.php';