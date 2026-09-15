<?php

require '../config.php';
require_admin();

$pdo = db();

$produtos = $pdo
    ? $pdo->query('SELECT * FROM produtos ORDER BY id DESC')->fetchAll()
    : [];

$nivel = $_SESSION['admin_nivel'] ?? 'funcionario';
$nome = $_SESSION['admin_nome'] ?? 'Usuário';
$idLogado = (int)($_SESSION['admin_id'] ?? 0);

$mensagem = '';
$tipoMensagem = '';

// Mensagens de sucesso
if (
    isset($_GET['sucesso']) &&
    $_GET['sucesso'] === 'produto_excluido'
) {
    $mensagem = 'Produto excluído com sucesso.';
    $tipoMensagem = 'sucesso';
}

// Mensagens de erro
if (isset($_GET['erro'])) {

    if ($_GET['erro'] === 'sem_permissao') {

        $mensagem = 'Você não possui permissão para excluir produtos.';

    } elseif ($_GET['erro'] === 'produto_invalido') {

        $mensagem = 'Produto inválido.';

    } elseif ($_GET['erro'] === 'produto_nao_encontrado') {

        $mensagem = 'Produto não encontrado.';

    } elseif ($_GET['erro'] === 'csrf') {

        $mensagem = 'Não foi possível validar a solicitação. Tente novamente.';

    }

    if ($mensagem !== '') {
        $tipoMensagem = 'erro';
    }
}

?>

<!doctype html>

<html lang="pt-BR">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Admin | 1 Dose</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="admin-body">

<div class="admin-shell">

    <div class="admin-top">

        <div>

            <span class="eyebrow orange">
                PAINEL 1 DOSE
            </span>

            <h1>Produtos</h1>

            <p>
                Olá, <?= e($nome) ?>
            </p>

        </div>


        <div>

            <a
                class="btn btn-outline"
                href="../index.php"
            >
                Ver site
            </a>


            <a
                class="btn btn-primary"
                href="produto-form.php"
            >
                + Novo produto
            </a>


            <?php if ($nivel === 'admin'): ?>

                <a
                    class="btn btn-outline"
                    href="usuarios.php"
                >
                    Usuários
                </a>

            <?php endif; ?>


            <a
                class="btn btn-outline"
                href="usuario_senha.php?id=<?= $idLogado ?>"
            >
                Minha senha
            </a>


            <a
                class="text-link"
                href="logout.php"
            >
                Sair
            </a>

        </div>

    </div>


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


    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>Produto</th>

                    <th>Volume</th>

                    <th>Teor</th>

                    <th>Status</th>

                    <th>Ações</th>

                </tr>

            </thead>


            <tbody>

            <?php foreach ($produtos as $p): ?>

                <tr>

                    <td>

                        <strong>
                            <?= e($p['nome']) ?>
                        </strong>

                    </td>


                    <td>
                        <?= e($p['volume']) ?>
                    </td>


                    <td>
                        <?= e($p['teor']) ?>
                    </td>


                    <td>
                        <?= $p['ativo'] ? 'Ativo' : 'Oculto' ?>
                    </td>


                    <td>

                        <a
                            href="produto-form.php?id=<?= (int)$p['id'] ?>"
                        >
                            Editar
                        </a>


                        <?php if ($nivel === 'admin'): ?>

                            &nbsp; · &nbsp;

                            <form
                                method="post"
                                action="excluir.php"
                                style="display:inline;"
                                onsubmit="return confirm('Tem certeza que deseja excluir este produto?');"
                            >

                                <?= csrf_field() ?>

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$p['id'] ?>"
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


            <?php if (!$produtos): ?>

                <tr>

                    <td colspan="5">
                        Nenhum produto cadastrado.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>