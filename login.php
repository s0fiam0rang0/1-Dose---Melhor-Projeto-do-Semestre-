<?php

require_once '../config.php';

if (is_admin()) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($usuario === '' || $senha === '') {

        $erro = 'Preencha o usuário e a senha.';

    } else {

        $pdo = db();

        if (!$pdo) {

            $erro = 'Não foi possível conectar ao banco de dados.';

        } else {

            $stmt = $pdo->prepare(
                'SELECT id, nome, usuario, senha, nivel
                 FROM usuarios
                 WHERE usuario = ?
                 LIMIT 1'
            );

            $stmt->execute([$usuario]);

            $admin = $stmt->fetch();

            if ($admin && password_verify($senha, $admin['senha'])) {

                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];
                $_SESSION['admin_nivel'] = $admin['nivel'];
                $_SESSION['ultima_atividade'] = time();

                header('Location: index.php');
                exit;

            } else {

                $erro = 'Usuário ou senha incorretos.';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Área Administrativa | 1 Dose</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

    <main class="container">

        <section class="admin-login">

            <h1>Área Administrativa</h1>

            <p>
                Acesse o painel de gerenciamento da 1 Dose.
            </p>

            <?php if ($erro): ?>

                <p class="erro">
                    <?= e($erro) ?>
                </p>

            <?php endif; ?>

            <form method="POST">

                <label for="usuario">
                    Usuário
                </label>

                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    autocomplete="username"
                    required
                >

                <label for="senha">
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    autocomplete="current-password"
                    required
                >

                <button type="submit">
                    Entrar
                </button>

            </form>

            <p>
                <a href="../index.php">
                    Voltar para o site
                </a>
            </p>

        </section>

    </main>

</body>

</html>