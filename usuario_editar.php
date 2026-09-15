<?php

require_once '../config.php';
require_admin();

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

// Apenas administradores
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: usuarios.php');
    exit;
}

// Não permite editar a própria conta por esta página
if ($id === (int)$_SESSION['admin_id']) {
    header('Location: usuarios.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, nome, usuario, nivel
     FROM usuarios
     WHERE id = ?
     LIMIT 1'
);

$stmt->execute([$id]);

$u = $stmt->fetch();

if (!$u) {
    header('Location: usuarios.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica a proteção CSRF
    if (!csrf_verify()) {

        $erro = 'Não foi possível validar a solicitação. Tente novamente.';

    } else {

        $nome = trim($_POST['nome'] ?? '');
        $login = trim($_POST['usuario'] ?? '');
        $nivel = $_POST['nivel'] ?? '';

        if ($nome === '' || $login === '') {

            $erro = 'Preencha o nome e o usuário.';

        } elseif (!in_array($nivel, ['admin', 'funcionario'], true)) {

            $erro = 'Nível de acesso inválido.';

        } else {

            // Verifica se o login já pertence a outra conta
            $stmt = $pdo->prepare(
                'SELECT id
                 FROM usuarios
                 WHERE usuario = ?
                 AND id <> ?
                 LIMIT 1'
            );

            $stmt->execute([
                $login,
                $id
            ]);

            if ($stmt->fetch()) {

                $erro = 'Este nome de usuário já está sendo utilizado.';

            } else {

                // Protege o último administrador
                if (
                    $u['nivel'] === 'admin' &&
                    $nivel === 'funcionario'
                ) {

                    $stmt = $pdo->query(
                        "SELECT COUNT(*)
                         FROM usuarios
                         WHERE nivel = 'admin'"
                    );

                    $totalAdmins = (int)$stmt->fetchColumn();

                    if ($totalAdmins <= 1) {
                        $erro = 'Não é possível alterar o último administrador para funcionário.';
                    }
                }

                // Só atualiza se não houver erro
                if ($erro === '') {

                    $stmt = $pdo->prepare(
                        'UPDATE usuarios
                         SET nome = ?, usuario = ?, nivel = ?
                         WHERE id = ?'
                    );

                    $stmt->execute([
                        $nome,
                        $login,
                        $nivel,
                        $id
                    ]);

                    $u['nome'] = $nome;
                    $u['usuario'] = $login;
                    $u['nivel'] = $nivel;

                    $sucesso = 'Usuário atualizado com sucesso.';
                }
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

    <title>Editar usuário | 1 Dose</title>

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

    <h1>Editar usuário</h1>


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
                value="<?= e($u['nome']) ?>"
                required
            >

        </label>


        <label>

            Usuário

            <input
                type="text"
                name="usuario"
                value="<?= e($u['usuario']) ?>"
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
                    <?= $u['nivel'] === 'admin' ? 'selected' : '' ?>
                >
                    Administrador
                </option>

                <option
                    value="funcionario"
                    <?= $u['nivel'] === 'funcionario' ? 'selected' : '' ?>
                >
                    Funcionário
                </option>

            </select>

        </label>


        <button
            class="btn btn-primary"
            type="submit"
        >
            Salvar alterações
        </button>

    </form>

</div>

</body>

</html>