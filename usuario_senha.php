<?php

require_once '../config.php';
require_admin();

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

$id = (int)($_GET['id'] ?? 0);

$idLogado = (int)($_SESSION['admin_id'] ?? 0);
$nivelLogado = $_SESSION['admin_nivel'] ?? 'funcionario';

if (!$id) {
    header('Location: index.php');
    exit;
}

// Verifica se está alterando a própria senha
$propriaConta = ($id === $idLogado);

// Funcionário só pode alterar a própria senha
if (!$propriaConta && $nivelLogado !== 'admin') {
    header('Location: index.php');
    exit;
}

// Busca o usuário
$stmt = $pdo->prepare(
    'SELECT id, nome, usuario, senha, nivel
     FROM usuarios
     WHERE id = ?
     LIMIT 1'
);

$stmt->execute([$id]);

$usuarioDados = $stmt->fetch();

if (!$usuarioDados) {
    header('Location: index.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica CSRF
    if (!csrf_verify()) {

        $erro = 'Não foi possível validar a solicitação. Tente novamente.';

    } else {

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        // Se estiver alterando a própria senha,
        // precisa confirmar a senha atual
        if ($propriaConta && $senhaAtual === '') {

            $erro = 'Informe sua senha atual.';

        } elseif (
            $propriaConta &&
            !password_verify($senhaAtual, $usuarioDados['senha'])
        ) {

            $erro = 'A senha atual está incorreta.';

        } elseif ($novaSenha === '' || $confirmarSenha === '') {

            $erro = 'Preencha a nova senha e a confirmação.';

        } elseif (strlen($novaSenha) < 8) {

            $erro = 'A nova senha deve possuir pelo menos 8 caracteres.';

        } elseif ($novaSenha !== $confirmarSenha) {

            $erro = 'As senhas não coincidem.';

        } elseif (
            $propriaConta &&
            password_verify($novaSenha, $usuarioDados['senha'])
        ) {

            $erro = 'A nova senha deve ser diferente da senha atual.';

        } else {

            $senhaHash = password_hash(
                $novaSenha,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                'UPDATE usuarios
                 SET senha = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $senhaHash,
                $id
            ]);

            $sucesso = $propriaConta
                ? 'Sua senha foi alterada com sucesso.'
                : 'Senha do usuário alterada com sucesso.';
        }
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

    <title>Alterar senha | 1 Dose</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="admin-body">

<div class="form-card">

    <?php if ($propriaConta): ?>

        <a
            class="back-link"
            href="index.php"
        >
            ← Voltar ao painel
        </a>

    <?php else: ?>

        <a
            class="back-link"
            href="usuarios.php"
        >
            ← Voltar para usuários
        </a>

    <?php endif; ?>


    <h1>
        <?= $propriaConta ? 'Alterar minha senha' : 'Alterar senha' ?>
    </h1>


    <p>
        Usuário:
        <strong>
            <?= e($usuarioDados['nome']) ?>
        </strong>
    </p>

    <p>
        Login:
        <strong>
            <?= e($usuarioDados['usuario']) ?>
        </strong>
    </p>


    <?php if (!$propriaConta): ?>

        <p>
            Você está redefinindo a senha deste usuário como administrador.
        </p>

    <?php endif; ?>


    <?php if ($erro): ?>

        <p
            style="
                padding:12px;
                margin:15px 0;
                border-radius:8px;
                background:#ffe5e5;
                color:#8b0000;
            "
        >
            <?= e($erro) ?>
        </p>

    <?php endif; ?>


    <?php if ($sucesso): ?>

        <p
            style="
                padding:12px;
                margin:15px 0;
                border-radius:8px;
                background:#e5f6e8;
                color:#176b2c;
            "
        >
            <?= e($sucesso) ?>
        </p>

    <?php endif; ?>


    <form method="post">

        <?= csrf_field() ?>


        <?php if ($propriaConta): ?>

            <label>

                Senha atual

                <input
                    type="password"
                    name="senha_atual"
                    autocomplete="current-password"
                    required
                >

            </label>

        <?php endif; ?>


        <label>

            Nova senha

            <input
                type="password"
                name="nova_senha"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </label>


        <label>

            Confirmar nova senha

            <input
                type="password"
                name="confirmar_senha"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </label>


        <button
            class="btn btn-primary"
            type="submit"
        >
            <?= $propriaConta ? 'Alterar minha senha' : 'Redefinir senha' ?>
        </button>

    </form>

</div>

</body>

</html>