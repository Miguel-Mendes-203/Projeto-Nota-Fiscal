<?php

require_once 'verifica_login.php';
require_once 'conexao.php';

$erro    = '';
$sucesso = '';
$dados   = ['codigo_produto' => '', 'codigo_nota' => '', 'quantidade' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['codigo_produto'] = trim($_POST['codigo_produto'] ?? '');
    $dados['codigo_nota']    = trim($_POST['codigo_nota'] ?? '');
    $dados['quantidade']     = trim($_POST['quantidade'] ?? '');

    $codProduto = filter_var($dados['codigo_produto'], FILTER_VALIDATE_INT);
    $codNota    = filter_var($dados['codigo_nota'], FILTER_VALIDATE_INT);
    $quantidade = filter_var($dados['quantidade'], FILTER_VALIDATE_INT);

    if (!$codProduto || !$codNota || !$quantidade || $codProduto < 1 || $codNota < 1 || $quantidade < 1) {
        $erro = 'Informe o código do produto, o código da nota e uma quantidade válida.';
    } else {

    $stmt = $pdo->prepare("SELECT vl_unitario FROM tb_produto WHERE cd_produto = :c");
        $stmt->execute([':c' => $codProduto]);
        $produto = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT 1 FROM tb_nota WHERE cd_nota = :c");
        $stmt->execute([':c' => $codNota]);
        $notaExiste = (bool) $stmt->fetch();

        if (!$produto) {
            $erro = 'Produto não encontrado.';
        } elseif (!$notaExiste) {
            $erro = 'Nota não encontrada.';
        } else {
            $totalItem = round($quantidade * (float) $produto['vl_unitario'], 2);

            if ($totalItem > 999.99) {
                $erro = 'O total do item passa de R$ 999,99. Diminua a quantidade.';
            } else {
                try {
                    $stmt = $pdo->prepare(
                        "INSERT INTO tb_produto_has_tb_nota
                                (fk_cd_produto, fk_cd_nota, qt_produto, vl_total_item)
                         VALUES (:produto, :nota, :qtd, :total)"
                    );
                    $stmt->execute([
                        ':produto' => $codProduto,
                        ':nota'    => $codNota,
                        ':qtd'     => $quantidade,
                        ':total'   => number_format($totalItem, 2, '.', ''),
                    ]);

                    $sucesso = 'Produto adicionado à nota com sucesso!';
                    $dados   = ['codigo_produto' => '', 'codigo_nota' => '', 'quantidade' => ''];
                } catch (PDOException $e) {
                    $erro = 'Erro ao adicionar o produto. Tente novamente.';
                }
            }
        }
    }
}

require 'produto-amostra.html';