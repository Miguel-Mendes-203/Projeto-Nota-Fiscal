<?php
require_once 'conexao.php';

$erro  = '';
$dados = ['nome' => '', 'email' => '', 'cnpj' => '', 'razao_social' => '', 'pais' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['nome']         = trim($_POST['nome'] ?? '');
    $dados['email']        = trim($_POST['email'] ?? '');
    $dados['cnpj']         = preg_replace('/\D/', '', $_POST['cnpj'] ?? ''); // só números
    $dados['razao_social'] = trim($_POST['razao_social'] ?? '');
    $dados['pais']         = trim($_POST['pais'] ?? '');
    $senha                 = $_POST['senha'] ?? '';
    $confirmar             = $_POST['confirmar_senha'] ?? '';

    if (in_array('', [$dados['nome'], $dados['email'], $dados['cnpj'], $dados['razao_social'], $dados['pais'], $senha, $confirmar], true)) {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($dados['cnpj']) !== 14) {
        $erro = 'O CNPJ deve ter 14 números.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não conferem.';
    } elseif (mb_strlen($dados['nome']) > 50 || mb_strlen($dados['razao_social']) > 30
              || mb_strlen($dados['email']) > 40 || mb_strlen($dados['pais']) > 20) {
        $erro = 'Nome (máx. 50), razão social (máx. 30), e-mail (máx. 40) ou país (máx. 20) acima do limite.';
    } else {
        // Verifica duplicidade
        $stmt = $pdo->prepare("SELECT 1 FROM TB_USUARIO WHERE EMAIL = :email");
        $stmt->execute([':email' => $dados['email']]);
        $emailExiste = (bool) $stmt->fetch();

        $stmt = $pdo->prepare("SELECT 1 FROM tb_cliente WHERE nr_cnpj = :cnpj");
        $stmt->execute([':cnpj' => $dados['cnpj']]);
        $cnpjExiste = (bool) $stmt->fetch();

        if ($emailExiste) {
            $erro = 'Este e-mail já está cadastrado.';
        } elseif ($cnpjExiste) {
            $erro = 'Este CNPJ já está cadastrado.';
        } else {
            try {
                $pdo->beginTransaction();

                // ID_USUARIO não tem AUTO_INCREMENT: calcula o próximo código
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

                $stmt = $pdo->prepare(
                    "INSERT INTO tb_cliente (nr_cnpj, nm_razao_social, nm_pais, nm_email)
                     VALUES (:cnpj, :razao_social, :pais, :email)"
                );
                $stmt->execute([
                    ':cnpj'         => $dados['cnpj'],
                    ':razao_social' => $dados['razao_social'],
                    ':pais'         => $dados['pais'],
                    ':email'        => $dados['email'],
                ]);

                $pdo->commit();
                header('Location: login.php');
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                $erro = 'Erro ao cadastrar. Tente novamente.';
            }
        }
    }
}

require 'cadastro.html';