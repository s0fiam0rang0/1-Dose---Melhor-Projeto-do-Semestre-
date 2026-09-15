<?php

require_once '../config.php';
require_admin();

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

// Apenas administradores podem acessar
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Mensagem de sucesso
if (
    isset($_GET['sucesso']) &&
    $_GET['sucesso'] === 'excluido'
) {
    $mensagem = 'Usuário excluído com sucesso.';
    $tipoMensagem = 'sucesso';
}

// Mensagens de erro
if (isset($_GET['erro'])) {

    if ($_GET['erro'] === 'propria_conta') {

        $mensagem = 'Você não pode excluir a própria conta.';

    } elseif ($_GET['erro'] === 'ultimo_admin') {

        $mensagem = 'Não é possível excluir o último administrador do sistema.';

    } elseif ($_GET['erro'] === 'nao_encontrado') {

        $mensagem = 'Usuário não encontrado.';

    } elseif ($_GET['erro'] === 'csrf') {

        $mensagem = 'Não foi possível validar a solicitação. Tente novamente.';

    }

    if ($mensagem !== '') {
        $tipoMensagem = 'erro';
    }
}

$stmt = $pdo->query(
    'SELECT id, nome, usuario, nivel
     FROM usuarios
     ORDER BY nome ASC'
);

$usuarios = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Usuários | 1 Dose</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="admin-body">

<div class="form-card">

    <a
        class="back-link"
        href="index.php"
    >
        ← Voltar ao painel
    </a>

    <h1>Usuários</h1>

    <p>
        Gerencie as pessoas que possuem acesso ao painel da 1 Dose.
    </p>


    <?php if ($mensagem !== ''): ?>

        <?php if ($tipoMensagem === 'sucesso'): ?>

            <p
                style="
                    padding:12px;
                    margin:15px 0;
                    border-radius:8px;
                    background:#e5f6e8;
                    color:#176b2c;
                "
            >
                <?= e($mensagem) ?>
            </p>

        <?php else: ?>

            <p
                style="
                    padding:12px;
                    margin:15px 0;
                    border-radius:8px;
                    background:#ffe5e5;
                    color:#8b0000;
                "
            >
                <?= e($mensagem) ?>
            </p>

        <?php endif; ?>

    <?php endif; ?>


    <p>

        <a
            href="usuario_novo.php"
            class="btn btn-primary"
        >
            + Cadastrar usuário
        </a>

    </p>


    <div style="overflow-x:auto; margin-top:25px;">

        <table
            style="
                width:100%;
                border-collapse:collapse;
            "
        >

            <thead>

                <tr>

                    <th style="text-align:left; padding:10px;">
                        Nome
                    </th>

                    <th style="text-align:left; padding:10px;">
                        Usuário
                    </th>

                    <th style="text-align:left; padding:10px;">
                        Nível
                    </th>

                    <th style="text-align:left; padding:10px;">
                        Ações
                    </th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($usuarios as $u): ?>

                <tr style="border-top:1px solid #ddd;">

                    <td style="padding:10px;">
                        <?= e($u['nome']) ?>
                    </td>

                    <td style="padding:10px;">
                        <?= e($u['usuario']) ?>
                    </td>

                    <td style="padding:10px;">

                        <?php if ($u['nivel'] === 'admin'): ?>

                            Administrador

                        <?php else: ?>

                            Funcionário

                        <?php endif; ?>

                    </td>


                    <td style="padding:10px;">

                        <a
                            href="usuario_senha.php?id=<?= (int)$u['id'] ?>"
                        >
                            Alterar senha
                        </a>


                        <?php if ((int)$u['id'] !== (int)$_SESSION['admin_id']): ?>

                            &nbsp; | &nbsp;

                            <a
                                href="usuario_editar.php?id=<?= (int)$u['id'] ?>"
                            >
                                Editar
                            </a>

                            &nbsp; | &nbsp;


                            <form
                                method="post"
                                action="usuario_excluir.php"
                                style="display:inline;"
                                onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"
                            >

                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$u['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    style="
                                        background:none;
                                        border:none;
                                        padding:0;
                                        margin:0;
                                        color:#b00020;
                                        cursor:pointer;
                                        text-decoration:underline;
                                        font:inherit;
                                    "
                                >
                                    Excluir
                                </button>

                            </form>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>