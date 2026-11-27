<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

$cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? $_GET['cnpj'] ?? '');

$stmt = $pdo->prepare(
    "SELECT nr_cnpj, nm_razao_social, nm_pais, nm_email FROM tb_cliente WHERE nr_cnpj = :cnpj"
);
$stmt->execute([':cnpj' => $cnpj]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header('Location: listar_clientes.php?msg=nao_encontrado');
    exit;
}

$erro  = '';
$dados = [
    'cnpj'         => $cliente['nr_cnpj'],
    'razao_social' => $cliente['nm_razao_social'] ?? '',
    'pais'         => $cliente['nm_pais'] ?? '',
    'email'        => $cliente['nm_email'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['razao_social'] = trim($_POST['razao_social'] ?? '');
    $dados['pais']         = trim($_POST['pais'] ?? '');
    $dados['email']        = trim($_POST['email'] ?? '');

    if ($dados['razao_social'] === '' || $dados['pais'] === '' || $dados['email'] === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (mb_strlen($dados['razao_social']) > 30 || mb_strlen($dados['pais']) > 20
              || mb_strlen($dados['email']) > 40) {
        $erro = 'Razão social (máx. 30), país (máx. 20) ou e-mail (máx. 40) acima do limite.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE tb_cliente
                    SET nm_razao_social = :razao_social, nm_pais = :pais, nm_email = :email
                  WHERE nr_cnpj = :cnpj"
            );
            $stmt->execute([
                ':razao_social' => $dados['razao_social'],
                ':pais'         => $dados['pais'],
                ':email'        => $dados['email'],
                ':cnpj'         => $cnpj,
            ]);

            if (($_SESSION['cnpj'] ?? '') === $cnpj) {
                $_SESSION['razao_social'] = $dados['razao_social'];
            }

            header('Location: listar_clientes.php?msg=editado');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar. Tente novamente.';
        }
    }
}

require 'editar_cliente.html';
