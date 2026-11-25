<?php
require_once 'verifica_login.php';
require_once 'conexao.php';

$erro    = '';
$sucesso = '';
$dados   = ['codigo' => '', 'emissao' => '', 'envio' => '', 'valor' => '', 'cnpj' => ''];

function dataValida(string $data): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['codigo']  = trim($_POST['codigo'] ?? '');
    $dados['emissao'] = trim($_POST['emissao'] ?? '');
    $dados['envio']   = trim($_POST['envio'] ?? '');
    $dados['valor']   = trim($_POST['valor'] ?? '');
    $dados['cnpj']    = preg_replace('/\D/', '', $_POST['cnpj'] ?? ''); 

    $valor = str_replace(',', '.', $dados['valor']);

    $codigo = null;
    if ($dados['codigo'] !== '') {
        $codigo = filter_var($dados['codigo'], FILTER_VALIDATE_INT);
    }

    if ($dados['emissao'] === '' || $dados['envio'] === '' || $dados['valor'] === '' || $dados['cnpj'] === '') {
        $erro = 'Preencha a data de emissão, a data de envio, o valor total e o CNPJ.';
    } elseif ($dados['codigo'] !== '' && (!$codigo || $codigo < 1)) {
        $erro = 'Código da nota inválido.';
    } elseif (!dataValida($dados['emissao']) || !dataValida($dados['envio'])) {
        $erro = 'Informe datas válidas.';
    } elseif ($dados['envio'] < $dados['emissao']) {
        $erro = 'A data de envio não pode ser anterior à data de emissão.';
    } elseif (!is_numeric($valor) || $valor < 0 || $valor > 9999.99) {
        $erro = 'Valor total inválido (use um valor entre 0,00 e 9.999,99).';
    } elseif (strlen($dados['cnpj']) !== 14) {
        $erro = 'O CNPJ deve ter 14 números.';
    } else {
        $stmt = $pdo->prepare("SELECT 1 FROM tb_cliente WHERE nr_cnpj = :cnpj");
        $stmt->execute([':cnpj' => $dados['cnpj']]);
        $clienteExiste = (bool) $stmt->fetch();

        $codigoExiste = false;
        if ($codigo !== null) {
            $stmt = $pdo->prepare("SELECT 1 FROM tb_nota WHERE cd_nota = :cod");
            $stmt->execute([':cod' => $codigo]);
            $codigoExiste = (bool) $stmt->fetch();
        }

        if (!$clienteExiste) {
            $erro = 'CNPJ não encontrado entre os clientes cadastrados.';
        } elseif ($codigoExiste) {
            $erro = 'Já existe uma nota com este código.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO tb_nota (cd_nota, dt_emissao, dt_envio, vl_total, fk_nr_cnpj)
                     VALUES (:cod, :emissao, :envio, :valor, :cnpj)"
                );
                                $stmt->execute([
                    ':cod'     => $codigo,
                    ':emissao' => $dados['emissao'],
                    ':envio'   => $dados['envio'],
                    ':valor'   => number_format((float) $valor, 2, '.', ''),
                    ':cnpj'    => $dados['cnpj'],
                ]);

                $sucesso = 'Nota cadastrada com sucesso! Código: ' . $pdo->lastInsertId();
                $dados   = ['codigo' => '', 'emissao' => '', 'envio' => '', 'valor' => '', 'cnpj' => ''];
            } catch (PDOException $e) {
                $erro = 'Erro ao cadastrar a nota. Tente novamente.';
            }
        }
    }
}
require 'cadastrar_nota_view.php';
