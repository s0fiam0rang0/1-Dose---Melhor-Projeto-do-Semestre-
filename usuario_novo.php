<?php

require_once '../config.php';
require_admin();

// Somente administradores podem cadastrar usuários
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

$erro = '';
$sucesso = '';

$nome = '';
$usuario = '';
$nivel = 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica a proteção CSRF
    if (!csrf_verify()) {

        $erro = 'Não foi possível validar a solicitação. Tente novamente.';

    } else {

        $nome = trim($_POST['nome'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $nivel = $_POST['nivel'] ?? 'funcionario';

        if (
            $nome === '' ||
            $usuario === '' ||
            $senha === '' ||
            $confirmarSenha === ''
        ) {

            $erro = 'Preencha todos os campos.';

        } elseif (!in_array($nivel, ['admin', 'funcionario'], true)) {

            $erro = 'Nível de acesso inválido.';

        } elseif ($senha !== $confirmarSenha) {

            $erro = 'As senhas não coincidem.';

        } elseif (strlen($senha) < 8) {

            $erro = 'A senha deve possuir pelo menos 8 caracteres.';

        } else {

            // Verifica se esse login já existe
            $stmt = $pdo->prepare(
                'SELECT id
                 FROM usuarios
                 WHERE usuario = ?
                 LIMIT 1'
            );

            $stmt->execute([$usuario]);

            if ($stmt->fetch()) {

                $erro = 'Este nome de usuário já está cadastrado.';

            } else {

                // Nunca salva a senha aberta no banco
                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare(
                    'INSERT INTO usuarios
                    (nome, usuario, senha, nivel)
                    VALUES (?, ?, ?, ?)'
                );

                $stmt->execute([
                    $nome,
                    $usuario,
                    $senhaHash,
                    $nivel
                ]);

                $sucesso = 'Usuário cadastrado com sucesso.';

                // Limpa o formulário
                $nome = '';
                $usuario = '';
                $nivel = 'admin';
            }
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

    <title>Cadastrar usuário | 1 Dose</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="admin-body">

<div class="form-card">

    <a
        class="back-link"
        href="usuarios.php"
    >
        ← Voltar para usuários
    </a>

    <h1>Cadastrar usuário</h1>

    <p>
        Crie um novo acesso ao painel administrativo da 1 Dose.
    </p>


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

        <label>

            Nome

            <input
                type="text"
                name="nome"
                value="<?= e($nome) ?>"
                placeholder="Ex.: Samuel"
                required
            >

        </label>


        <label>

            Usuário

            <input
                type="text"
                name="usuario"
                value="<?= e($usuario) ?>"
                placeholder="Ex.: samuel"
                autocomplete="username"
                required
            >

        </label>


        <label>

            Senha

            <input
                type="password"
                name="senha"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </label>


        <label>

            Confirmar senha

            <input
                type="password"
                name="confirmar_senha"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </label>


        <label>

            Nível de acesso

            <select
                name="nivel"
                required
            >

                <option
                    value="admin"
                    <?= $nivel === 'admin' ? 'selected' : '' ?>
                >
                    Administrador
                </option>

                <option
                    value="funcionario"
                    <?= $nivel === 'funcionario' ? 'selected' : '' ?>
                >
                    Funcionário
                </option>

            </select>

        </label>


        <button
            class="btn btn-primary"
            type="submit"
        >
            Cadastrar usuário
        </button>

    </form>

</div>

</body>

</html>